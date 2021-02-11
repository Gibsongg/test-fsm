<?php

namespace ZeroDaHero\LaravelWorkflow\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent as SymfonyGuardEvent;

/**
 * @author Boris Koumondji <brexis@yahoo.fr>
 */
class WorkflowSubscriber implements EventSubscriberInterface
{
    public function guardEvent(SymfonyGuardEvent $event): void
    {
        $workflowName = $event->getWorkflowName();
        $transitionName = $event->getTransition()->getName();

        $workflowEvent = new GuardEvent($event);

        event($workflowEvent);
        event('workflow.guard', $workflowEvent);
        event(sprintf('workflow.%s.guard', $workflowName), $workflowEvent);
        event(sprintf('workflow.%s.guard.%s', $workflowName, $transitionName), $workflowEvent);
    }

    public function leaveEvent(Event $event): void
    {
        $places = $event->getTransition()->getFroms();
        $workflowName = $event->getWorkflowName();

        $workflowEvent = new LeaveEvent($event);

        event($workflowEvent);
        event('workflow.leave', $workflowEvent);
        event(sprintf('workflow.%s.leave', $workflowName), $workflowEvent);

        foreach ($places as $place) {
            event(sprintf('workflow.%s.leave.%s', $workflowName, $place), $workflowEvent);
        }
    }

    public function transitionEvent(Event $event): void
    {
        $workflowName = $event->getWorkflowName();
        $transitionName = $event->getTransition()->getName();

        $workflowEvent = new TransitionEvent($event);

        event($workflowEvent);
        event('workflow.transition', $workflowEvent);
        event(sprintf('workflow.%s.transition', $workflowName), $workflowEvent);
        event(sprintf('workflow.%s.transition.%s', $workflowName, $transitionName), $workflowEvent);
    }

    public function enterEvent(Event $event): void
    {
        $places = $event->getTransition()->getTos();
        $workflowName = $event->getWorkflowName();

        $workflowEvent = new EnterEvent($event);

        event($workflowEvent);
        event('workflow.enter', $workflowEvent);
        event(sprintf('workflow.%s.enter', $workflowName), $workflowEvent);

        foreach ($places as $place) {
            event(sprintf('workflow.%s.enter.%s', $workflowName, $place), $workflowEvent);
        }
    }

    public function enteredEvent(Event $event): void
    {
        $places = $event->getTransition() ? $event->getTransition()->getTos() : [];
        $workflowName = $event->getWorkflowName();

        $workflowEvent = new EnteredEvent($event);

        event($workflowEvent);
        event('workflow.entered', $workflowEvent);
        event(sprintf('workflow.%s.entered', $workflowName), $workflowEvent);

        foreach ($places as $place) {
            event(sprintf('workflow.%s.entered.%s', $workflowName, $place), $workflowEvent);
        }
    }

    public function completedEvent(Event $event): void
    {
        $workflowName = $event->getWorkflowName();
        $transitionName = $event->getTransition()->getName();

        $workflowEvent = new CompletedEvent($event);

        event($workflowEvent);
        event('workflow.completed', $workflowEvent);
        event(sprintf('workflow.%s.completed', $workflowName), $workflowEvent);
        event(sprintf('workflow.%s.completed.%s', $workflowName, $transitionName), $workflowEvent);
    }

    public function announceEvent(Event $event): void
    {
        $workflowName = $event->getWorkflowName();
        $transitionName = $event->getTransition()->getName();

        $workflowEvent = new AnnounceEvent($event);

        event($workflowEvent);
        event('workflow.announce', $workflowEvent);
        event(sprintf('workflow.%s.announce', $workflowName), $workflowEvent);
        event(sprintf('workflow.%s.announce.%s', $workflowName, $transitionName), $workflowEvent);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.guard' => ['guardEvent'],
            'workflow.leave' => ['leaveEvent'],
            'workflow.transition' => ['transitionEvent'],
            'workflow.enter' => ['enterEvent'],
            'workflow.entered' => ['enteredEvent'],
            'workflow.completed' => ['completedEvent'],
            'workflow.announce' => ['announceEvent'],
        ];
    }
}
