<?php

namespace App\Http\Controllers;

use App\Dictionary\TaskStatusDictionary;
use App\Http\Requests\Task\TaskCreate;
use App\Http\Requests\Task\TaskCreateRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Config;

use \RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Symfony\Component\Workflow\Dumper\StateMachineGraphvizDumper;
use Workflow;
use Storage;
use Symfony\Component\Process\Process;

class TaskController extends Controller
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
        $data = $this->taskService->getList();

        return view('task.index', ['tasks' => $data]);
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

    public function edit(int $id): Factory|View|Application
    {
        $task = $this->taskService->getById($id);
        $statuses = $this->taskService->getStatuses($task);

        return view('task.edit', [
            'task' => $task,
            'statuses' => $statuses,
            'status' => TaskStatusDictionary::getCollection()->get($task->status)
        ]);
    }

    public function setStatus(Request $request, int $id): RedirectResponse
    {
        $this->taskService->setStatus($id, $request->get('transition'));
        return redirect()->route('tasks.edit', ['task' => $id]);
    }

    /**
     * Отрисовка рабочего процесса и вывод файла в браузер
     * @return BinaryFileResponse
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function diagram(): BinaryFileResponse
    {
        $workflowName = 'task';
        $format = 'png';

        $path = storage_path();

        $subject = new Task();
        $workflow = Workflow::get($subject, $workflowName);
        $definition = $workflow->getDefinition();

        //$dumper = new GraphvizDumper();
        $dumper = new StateMachineGraphvizDumper();
        $dotCommand = ['dot', "-T$format", '-o', "$workflowName.$format"];

        $process = new Process($dotCommand);
        $process->setWorkingDirectory($path);
        $process->setInput($dumper->dump($definition));
        $process->mustRun();

        return response()->file($path . '/' . $workflowName . '.' . $format);
    }
}
