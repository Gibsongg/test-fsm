<?php

namespace App\Services;

use App\Http\Requests\Job\JobCreate;
use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
     * Собираем массив где ключ перерод а значение лейбл перехода
     * $task->workflowTransitions() возвращает все доступные переходы с текущего состояния
     * @param Job $job
     * @return array
     */
    public function getStatuses(Job $job): array
    {
        $transitions = [];
        foreach ($job->workflowTransitions() as $transition) {
            $transitions[$transition->getName()] = $job->workflowGet()->getMetadataStore()->getTransitionMetadata($transition)['label'] ?? $transition->getName();
        }
        return $transitions;
    }

    public function getStatus(Job $job): array
    {
        $places = [];

        foreach ($job->status as $place => $token) {
            $places[] = $job->workflowGet()->getMetadataStore()->getPlaceMetadata($place)['label'] ?? $place;
        }

        return $places;
    }
}
