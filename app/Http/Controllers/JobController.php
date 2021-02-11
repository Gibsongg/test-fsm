<?php

namespace App\Http\Controllers;

use App\Http\Requests\Job\JobCreate;
use App\Http\Requests\Task\TaskCreateRequest;
use App\Models\Claim;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Config;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Workflow\Dumper\StateMachineGraphvizDumper;
use Workflow;
use Storage;
use Symfony\Component\Process\Process;

class JobController extends Controller
{

    protected JobService $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }


    public function index(): Factory|View|Application
    {
        $data = $this->jobService->getList();

        return view('job.index', ['jobs' => $data]);

    }

    public function create(): Factory|View|Application
    {
        $model = new Claim();

        return view('job.create', ['model' => $model]);
    }

    public function store(JobCreate $request): RedirectResponse
    {
        $this->jobService->createJob($request);
        return redirect()->route('jobs.index');
    }

    public function edit(int $id): Factory|View|Application
    {
        /** @var Job $job */
        $job = $this->jobService->getById($id);
        $statuses = $this->jobService->getStatuses($job);

        return view('job.edit', [
            'job' => $job,
            'statuses' => $statuses,
            'status' => $this->jobService->getStatus($job)
        ]);
    }

    public function diagram(): BinaryFileResponse
    {
        $workflowName = 'job';
        $format = 'png';

        $path = storage_path();

        $subject = new Job();
        /** @var \Symfony\Component\Workflow\Workflow $workflow */
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

    public function setStatus(Request $request, int $id): RedirectResponse
    {
        $this->jobService->setStatus($id, $request->get('transition'));
        return redirect()->route('jobs.edit', ['job' => $id]);
    }
}
