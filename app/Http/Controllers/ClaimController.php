<?php

namespace App\Http\Controllers;

use App\Dictionary\ClaimStatusDictionary;
use App\Dictionary\TaskStatusDictionary;
use App\Http\Requests\Claim\ClaimCreateRequest;
use App\Http\Requests\Task\TaskCreate;
use App\Http\Requests\Task\TaskCreateRequest;
use App\Models\Claim;
use App\Models\Task;
use App\Services\ClaimService;
use App\Services\TaskService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Config;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Workflow\Dumper\GraphvizDumper;
use Symfony\Component\Workflow\Dumper\StateMachineGraphvizDumper;
use Symfony\Component\Workflow\Transition;
use Workflow;
use Storage;
use Symfony\Component\Process\Process;

class ClaimController extends Controller
{

    protected ClaimService $claimService;

    public function __construct(ClaimService $claimService)
    {
        $this->claimService = $claimService;
    }


    public function index(): Factory|View|Application
    {
        $data = $this->claimService->getList();

        return view('claim.index', ['claims' => $data]);

    }

    public function create(): Factory|View|Application
    {
        $model = new Claim();

        return view('claim.create', ['model' => $model]);
    }

    public function store(ClaimCreateRequest $request): RedirectResponse
    {
        $this->claimService->createClaim($request);
        return redirect()->route('claims.index');
    }

    public function edit(int $id): Factory|View|Application
    {
        /** @var Claim $claim */
        $claim = $this->claimService->getById($id);
        $statuses = $this->claimService->getStatuses($claim);

        return view('claim.edit', [
            'claim' => $claim,
            'statuses' => $statuses,
            'status' => ClaimStatusDictionary::getCollection()->get($claim->status)
        ]);
    }

    public function diagram(): BinaryFileResponse
    {
        $workflowName = 'claim';
        $format = 'png';

        $path = storage_path();

        $subject = new Claim();
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
        $this->claimService->setStatus($id, $request->get('transition'));
        return redirect()->route('claims.edit', ['claim' => $id]);
    }
}
