<?php

namespace App\Http\Controllers;

use App\Dictionary\TaskStatusDictionary;
use App\Http\Requests\Task\TaskCreate;
use App\Http\Requests\Task\TaskCreateRequest;
use App\Models\Claim;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Config;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Symfony\Component\Workflow\Transition;
use Workflow;
use Storage;
use Symfony\Component\Process\Process;

class ClaimController extends Controller
{

    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $claim = Claim::find(1);
        /** @var Claim $claim */

        echo 'Статус:' .  $claim->status . '<br>';

        $transitions = $claim->workflow_transitions();

        foreach ($transitions as $transition) {
            /** @var Transition $transition */
            echo '<a href="/claims/1/status?t='. $transition->getName() .' ">'. $transition->getName() .'</a><br>';
        }

        echo '<pre>' . print_r($transitions, true) . '</pre>';



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $model = new Task;

        return view('task.create', ['model' => $model, 'statuses' => TaskStatusDictionary::getCollection()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TaskCreate $request
     * @return Response
     */
    public function store(TaskCreate $request)
    {
        $this->taskService->createTask($request);
        return redirect()->route('tasks.index');
    }


    public function diagram()
    {
        $workflowName = 'test';
        $format = 'png';

        $path = storage_path();

        $subject = new Claim();
        $workflow = Workflow::get($subject, $workflowName);
        $definition = $workflow->getDefinition();

        $dumper = new GraphvizDumper();
        //$dumper = new StateMachineGraphvizDumper();
        $dotCommand = ['dot', "-T$format", '-o', "$workflowName.$format"];

        $process = new Process($dotCommand);
        $process->setWorkingDirectory($path);
        $process->setInput($dumper->dump($definition));
        $process->mustRun();

        return response()->file($path . '/' . $workflowName . '.' . $format);
    }

    public function setStatus(Request $request, int $id): RedirectResponse
    {
        $claim = Claim::find($id);
        /** @var Claim $claim */


        $claim->workflow_apply($request->input('t'));
        $claim->save();

        return redirect()->route('claims.index', ['claim' => $id]);
    }
}
