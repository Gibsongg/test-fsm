<?php

namespace App\Services;

use App\Dictionary\TaskStatusDictionary;
use App\Http\Requests\Task\TaskCreate;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Workflow\Transition;

class TaskService
{
    public function getList(): Collection|array
    {
        return Task::query()->orderBy('created_at', 'desc')->get();
    }

    public function getById(int $id): Builder|array|Collection|Model
    {
        return Task::query()->findOrFail($id);
    }

    public function createTask(TaskCreate $request): bool
    {
        Task::query()->create([
            'name' => $request->post('name'),
            'description' => $request->post('description'),
            'status' => TaskStatusDictionary::OPEN
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
        /** @var Task $task */
        $task = $this->getById($id);
        //применям переход на новое состояние, если будет не позволено выбросит исключение
        $task->workflowApply($transition);
        $task->save();

        return true;
    }

    /**
     * Собираем массив где ключ перерод а значение место
     * $task->workflow_transitions() возвращает все доступные переходы с текущего состояния
     * @param Task $task
     * @return array
     */
    public function getStatuses(Task $task): array
    {
        $places = [];
        $dist = TaskStatusDictionary::getCollection();

        foreach ($task->workflowTransitions() as $transition) {
            /** @var Transition $transition */

            //берем первый элемент массива, но если у нас workflow то массив может быть более 1 элемента
            $firstPlace = $transition->getTos()[0];

            $places[$transition->getName()] =  $dist->get($firstPlace) ?? $firstPlace;
        }

        return $places;
    }
}
