<?php

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Dumper\StateMachineGraphvizDumper;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Process\Process;

class DrawService
{
    public function fromConfig()
    {
        include "../vendor/autoload.php";
        $conf = include "../config/workflow.php";

        foreach ($conf as $workflowName => $config) {
            $format = 'png';
            $transitions = [];
            foreach ($config['transitions'] as $key => $transition) {
                $transitions[] = new Transition($key, $transition['from'], $transition['to']);
            }

            $definition = new Definition($config['places'], $transitions);

            $graph = new StateMachineGraphvizDumper();

            $dot = $graph->dump($definition);
            $dotCommand = ['dot', "-T$format", '-o', "$workflowName.$format"];
            $process = new Process($dotCommand);

            $path = __DIR__ . '/picture';

            $process->setWorkingDirectory($path);
            $process->setInput($graph->dump($definition));
            $process->mustRun();


            echo '<pre>' . print_r($dot, true) . '</pre>';

        }
    }

    public function fromConstructor($dataPlaces, $dataTransitions)
    {
        $format = 'png';
        $workflowName = 'temp';

        $places = [];

        foreach ($dataPlaces as $key => $place) {
            $places[] = $place['id'];
        }

        $transitions = [];
        foreach ($dataTransitions as $key => $transition) {
            $transitions[] = new Transition($transition['id'], $transition['from'], $transition['to']);
        }

        $definition = new Definition($places, $transitions);

        $graph = new StateMachineGraphvizDumper();

        $dotCommand = ['dot', "-T$format", '-o', "$workflowName.$format"];
        $process = new Process($dotCommand);

        $path = dirname(__DIR__) . '/picture';

        $process->setWorkingDirectory($path);
        $process->setInput($graph->dump($definition));
        $process->mustRun();

        return '/picture/' . $workflowName . '.' . $format;
    }

    public function dump()
    {

    }

    public function buildPlaceInTransitions(array $transition): string
    {

        if (is_array($transition) && count($transition) > 1) {
            $value = "['" . implode("', '", (array)$transition) . "']";
        } else {
            if (is_array($transition)) {
                $transition = $transition[0];
            }

            $value = "'{$transition}'";
        }

        return $value;
    }

    public function createPhpScheme(array $data)
    {

        $places = [];

        foreach ($data['places'] as $place) {
            $places[] = $place['id'];
        }

        $placeString = "'" . implode("',\n       '", $places) . "'";

        $transitions = [];

        foreach ($data['transitions'] as $k => $transition) {

            $from = $this->buildPlaceInTransitions($transition['from']);
            $to = $this->buildPlaceInTransitions($transition['to']);

            $transitions[] = "'{$transition['id']}' => [
             'from' => {$from},
             'to' => {$to},
        ]";
        }

        $transitionsString = implode(",\n         ", $transitions);

        $php = <<<PHP
'{$data['name']}' => [
    'type' => '{$data['type']}',
    'marking_store' => [
       'type' => 'single_state',
       'property' => '{$data['marking_store']['property']}'
    ],
    'supports' => '{$data['supports']}',
    'places' => [
       {$placeString}
    ],
    'transitions' => [
     {$transitionsString}
    ]
  ]
PHP;

        return $php;

    }
}
