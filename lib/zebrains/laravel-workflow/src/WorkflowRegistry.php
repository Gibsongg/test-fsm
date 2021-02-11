<?php

namespace ZeroDaHero\LaravelWorkflow;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\EventListener\GuardExpression;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;
use Symfony\Component\Workflow\Metadata\InMemoryMetadataStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use ZeroDaHero\LaravelWorkflow\Events\WorkflowSubscriber;
use ZeroDaHero\LaravelWorkflow\Exceptions\DuplicateWorkflowException;
use ZeroDaHero\LaravelWorkflow\Exceptions\RegistryNotTrackedException;
use ZeroDaHero\LaravelWorkflow\MarkingStores\EloquentMarkingStore;
use ZeroDaHero\LaravelWorkflow\Listeners\GuardListener;

class WorkflowRegistry
{
    protected Registry $registry;
    protected array $config;
    protected array $registryConfig;
    protected EventDispatcher $dispatcher;

    /**
     * Keeps track of loaded workflows
     * (Useful when loading workflows after the config load)
     *
     * @var array
     */
    protected array $loadedWorkflows = [];

    /**
     * WorkflowRegistry constructor
     *
     * @param array $config
     * @param array $registryConfig
     *
     * @throws \ReflectionException
     */
    public function __construct(array $config, array $registryConfig = null)
    {
        $this->registry = new Registry();
        $this->config = $config;
        $this->registryConfig = $registryConfig ?? $this->getDefaultRegistryConfig();
        $this->dispatcher = new EventDispatcher();

        $subscriber = new WorkflowSubscriber();
        $this->dispatcher->addSubscriber($subscriber);

        foreach ($this->config as $name => $workflowData) {
            $this->addFromArray($name, $workflowData);
        }
    }

    /**
     * Return the $subject workflow
     *
     * @param object $subject
     * @param string $workflowName
     *
     * @return Workflow
     */
    public function get($subject, $workflowName = null): Workflow
    {
        return $this->registry->get($subject, $workflowName);
    }

    /**
     * Returns all workflows for the given subject
     *
     * @param object $subject
     *
     * @return Workflow[]
     */
    public function all($subject): array
    {
        return $this->registry->all($subject);
    }

    /**
     * Add a workflow to the subject
     *
     * @param Workflow $workflow
     * @param string $supportStrategy
     *
     * @return void
     * @throws DuplicateWorkflowException
     */
    public function add(Workflow $workflow, $supportStrategy): void
    {
        if (!$this->isLoaded($workflow->getName(), $supportStrategy)) {
            $this->registry->addWorkflow($workflow, new InstanceOfSupportStrategy($supportStrategy));
            $this->setLoaded($workflow->getName(), $supportStrategy);
        }
    }

    /**
     * Gets the loaded workflows
     *
     * @param string $supportStrategy
     *
     * @return array
     * @throws RegistryNotTrackedException
     *
     */
    public function getLoaded($supportStrategy = null): array
    {
        if (!$this->registryConfig['track_loaded']) {
            throw new RegistryNotTrackedException('This registry is not being tracked, and thus has not recorded any loaded workflows.');
        }

        if ($supportStrategy) {
            return $this->loadedWorkflows[$supportStrategy] ?? [];
        }

        return $this->loadedWorkflows;
    }

    /**
     * Add a workflow to the registry from array
     *
     * @param string $name
     * @param array $workflowData
     *
     * @return void
     * @throws DuplicateWorkflowException
     *
     * @throws \ReflectionException
     */
    public function addFromArray($name, array $workflowData): void
    {
        $guardsConfiguration = [];
        $metadata = $this->extractWorkflowPlacesMetaData($workflowData);

        $places = [];

        foreach ($workflowData['places'] as $key => $place) {
            if (is_array($place)) {
                $places[] = $key;
            } else {
                $places[] = $place;
            }
        }

        $builder = new DefinitionBuilder($places);

        foreach ($workflowData['transitions'] as $transitionName => $transition) {
            if (!is_string($transitionName)) {
                $transitionName = $transition['name'];
            }

            $transition['from'] = (array)$transition['from'];
            $transition['to'] = (array)$transition['to'];
            $workflowData['type'] = $workflowData['type'] ?? 'workflow';

            if ($workflowData['type'] === 'workflow') {
                foreach ($transition['from'] as $form) {
                    $transitionObj = new Transition($transitionName, $form, $transition['to']);
                    $builder->addTransition($transitionObj);

                    if (isset($transition['metadata'])) {
                        $metadata['transitions']->attach($transitionObj, $transition['metadata']);
                    }
                }
            } elseif ($workflowData['type'] === 'state_machine') {
                foreach ($transition['from'] as $from) {
                    $transitionObj = new Transition($transitionName, $from, $transition['to']);
                    $builder->addTransition($transitionObj);

                    foreach ($transition['to'] as $to) {
                        if (isset($transition['guard'])) {
                            $transitionObj = new Transition($transitionName, $from, $to);
                            $eventName = sprintf('workflow.%s.guard.%s', $name, $transitionName);
                            $listener = new GuardExpression($transitionObj, $transition['guard']);
                            $guardsConfiguration[$eventName][] = $listener;
                        }

                        if (isset($transition['metadata'])) {
                            $metadata['transitions']->attach($transitionObj, $transition['metadata']);
                        }
                    }
                }
            }

            $this->dispatcher->addSubscriber(new GuardListener($guardsConfiguration, new ExpressionLanguage()));
        }

        $metadataStore = new InMemoryMetadataStore(
            $metadata['workflow'],
            $metadata['places'],
            $metadata['transitions']
        );

        $builder->setMetadataStore($metadataStore);

        if (isset($workflowData['initial_places'])) {
            $builder->setInitialPlaces($workflowData['initial_places']);
        }

        $eventsToDispatch = $this->parseEventsToDispatch($workflowData);

        $definition = $builder->build();
        $markingStore = $this->getMarkingStoreInstance($workflowData);
        $workflow = $this->getWorkflowInstance($name, $workflowData, $definition, $markingStore, $eventsToDispatch);

        foreach ($workflowData['supports'] as $supportedClass) {
            $this->add($workflow, $supportedClass);
        }
    }

    /**
     * Parses events to dispatch data from config
     * @param array $workflowData
     * @return mixed|null
     */
    protected function parseEventsToDispatch(array $workflowData)
    {
        if (array_key_exists('events_to_dispatch', $workflowData)) {
            return $workflowData['events_to_dispatch'];
        }

        // Null dispatches all, [] dispatches none.
        return null;
    }

    /**
     * Gets the default registry config
     *
     * @return array
     */
    protected function getDefaultRegistryConfig(): array
    {
        return [
            'track_loaded' => false,
            'ignore_duplicates' => true,
        ];
    }

    /**
     * Checks if the workflow is already loaded for this supported class
     *
     * @param string $workflowName
     * @param string $supportStrategy
     *
     * @return bool
     * @throws DuplicateWorkflowException
     *
     */
    protected function isLoaded($workflowName, $supportStrategy): bool
    {
        if (!$this->registryConfig['track_loaded']) {
            return false;
        }

        if (isset($this->loadedWorkflows[$supportStrategy]) && in_array($workflowName, $this->loadedWorkflows[$supportStrategy], true)) {
            if (!$this->registryConfig['ignore_duplicates']) {
                throw new DuplicateWorkflowException(sprintf('Duplicate workflow (%s) attempting to be loaded for %s', $workflowName, $supportStrategy)); // phpcs:ignore
            }

            return true;
        }

        return false;
    }

    /**
     * Sets the workflow as loaded
     *
     * @param string $workflowName
     * @param string $supportStrategy
     *
     * @return void
     */
    protected function setLoaded($workflowName, $supportStrategy): void
    {
        if (!$this->registryConfig['track_loaded']) {
            return;
        }

        if (!isset($this->loadedWorkflows[$supportStrategy])) {
            $this->loadedWorkflows[$supportStrategy] = [];
        }

        $this->loadedWorkflows[$supportStrategy][] = $workflowName;
    }

    /**
     * Return the workflow instance
     *
     * @param string $name
     * @param array $workflowData
     * @param Definition $definition
     * @param MarkingStoreInterface $markingStore
     *
     * @return Workflow
     */
    protected function getWorkflowInstance(
        $name,
        array $workflowData,
        Definition $definition,
        MarkingStoreInterface $markingStore,
        ?array $eventsToDispatch = null
    ): Workflow
    {
        if (isset($workflowData['class'])) {
            $className = $workflowData['class'];

            return new $className($definition, $markingStore, $this->dispatcher, $name);
        } elseif (isset($workflowData['type']) && $workflowData['type'] === 'state_machine') {
            return new StateMachine($definition, $markingStore, $this->dispatcher, $name);
        } else {
            return new Workflow($definition, $markingStore, $this->dispatcher, $name, $eventsToDispatch);
        }
    }

    /**
     * Return the making store instance
     *
     * @param array $workflowData
     *
     * @return MarkingStoreInterface
     * @throws \ReflectionException
     *
     */
    protected function getMarkingStoreInstance(array $workflowData): MarkingStoreInterface
    {
        $markingStoreData = $workflowData['marking_store'] ?? [];
        $property = $markingStoreData['property'] ?? 'marking';

        if (array_key_exists('type', $markingStoreData)) {
            $type = $markingStoreData['type'];
        } else {
            $workflowType = $workflowData['type'] ?? 'workflow';
            $type = ($workflowType === 'state_machine') ? 'single_state' : 'multiple_state';
        }

        $markingStoreClass = $markingStoreData['class'] ?? EloquentMarkingStore::class;

        return new $markingStoreClass(
            ($type === 'single_state'),
            $property
        );
    }

    /**
     * Extracts workflow and places metadata from the config
     * NOTE: This modifies the provided config!
     *
     * @param array $workflowData
     *
     * @return array
     */
    protected function extractWorkflowPlacesMetaData(array &$workflowData): array
    {
        $metadata = [
            'workflow' => [],
            'places' => [],
            'transitions' => new \SplObjectStorage(),
        ];

        if (isset($workflowData['metadata'])) {
            $metadata['workflow'] = $workflowData['metadata'];
            unset($workflowData['metadata']);
        }

        foreach ($workflowData['places'] as $key => &$place) {
            if (is_int($key) && !is_array($place)) {
                // no metadata, just place name
                continue;
            }

            if (isset($place['metadata'])) {
                if (is_int($key) && !$place['name']) {
                    throw new InvalidArgumentException(sprintf('Unknown name for place at index %d', $key));
                }

                $name = !is_int($key) ? $key : $place['name'];
                $metadata['places'][$name] = $place['metadata'];

                $place = $name;
            }
        }

        return $metadata;
    }
}
