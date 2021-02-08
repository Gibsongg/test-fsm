<?php

namespace App\Services;


use App\Http\Requests\Job\JobCreate;
use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Workflow\Transition;

class JobService
{

    public function getList(): Collection|array
    {
        return Job::query()->orderBy('created_at', 'desc')->get();
    }

    public function getById(int $id): Builder|array|Collection|Model
    {
        return Job::query()->findOrFail($id);
    }

    public function createJob(JobCreate $request): bool
    {
        Job::query()->create([
            'name' => $request->post('name'),
            'description' => $request->post('description'),
            'status' => ['new']
        ]);

        return true;
    }


    /**
     * Меняем статус
     * @param int $id
     * @param string $transition
     * @return bool
     */
    public function setStatus(int $id, string $transition): bool
    {
        /** @var Job $job */
        $job = $this->getById($id);
        //применям переход на новое состояние, если будет не позволено выбросит исключение
        $job->workflowApply($transition);
        $job->save();

        return true;
    }

    /**
     * Собираем массив где ключ перерод а значение место
     * $task->workflow_transitions() возвращает все доступные переходы с текущего состояния
     * @param Job $job
     * @return array
     */
    public function getStatuses(Job $job): array
    {
        $places = [];
        //$dist = ClaimStatusDictionary::getCollection();

        echo '<pre>*' . print_r($job->workflowTransitions(), true) . '*</pre>';

        foreach ($job->workflowTransitions() as $transition) {
        //    echo '<pre>' . print_r($transition, true) . '</pre>';
        //    die;
            /** @var Transition $transition */
            //берем первый элемент массива, но если у нас workflow то массив может быть более 1 элемента
            //$places[] = $transition->getTos()[0];
        }

        return $places;
    }
}
