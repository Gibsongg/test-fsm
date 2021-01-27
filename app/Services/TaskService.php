<?php

namespace App\Services;

use App\Dictionary\TaskStatusDictionary;
use App\Http\Requests\Task\TaskCreate;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Workflow\Transition;

class TaskService
{

    public function getList(): Collection|array
    {
        return Task::orderBy('created_at', 'desc')->get();
    }

    public function getById(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function createTask(TaskCreate $request): bool
    {
        Task::create([
            'name' => $request->post('name'),
            'description' => $request->post('description'),
            'status' => TaskStatusDictionary::OPEN
        ]);

        return true;
    }

    public function saveTask()
    {

    }

    /**
     * Меняем статус
     * @param int $id
     * @param string $transition
     * @return bool
     */
    public function setStatus(int $id, string $transition): bool
    {
        $task = $this->getById($id);
        //применям переход на новое состояние, если будет не позволено выбросит исключение
        $task->workflow_apply($transition);
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

        foreach ($task->workflow_transitions() as $transition) {
            /** @var Transition $transition */
            //берем первый элемент массива, но если у нас workflow то массив может быть более 1 элемента
            $firstPlace = $transition->getTos()[0];

            if ($dist->has($firstPlace)) {
                $places[$transition->getName()] = $dist->get($firstPlace);
            }
        }

        return $places;
    }
}
