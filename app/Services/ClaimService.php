<?php

namespace App\Services;

use App\Dictionary\ClaimStatusDictionary;
use App\Http\Requests\Claim\ClaimCreateRequest;
use App\Models\Claim;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Workflow\Transition;

class ClaimService
{

    public function getList(): Collection|array
    {
        return Claim::query()->orderBy('created_at', 'desc')->get();
    }

    public function getById(int $id): Builder|array|Collection|Model
    {
        return Claim::query()->findOrFail($id);
    }

    public function createClaim(ClaimCreateRequest $request): bool
    {
        Claim::query()->create([
            'name' => $request->post('name'),
            'description' => $request->post('description'),
            'status' => ClaimStatusDictionary::NEW
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
        /** @var Claim $claim */
        $claim = $this->getById($id);
        //применям переход на новое состояние, если будет не позволено выбросит исключение

        $claim->workflowApply($transition);
        $claim->save();

        return true;
    }

    /**
     * Собираем массив где ключ перерод а значение место
     * $task->workflow_transitions() возвращает все доступные переходы с текущего состояния
     * @param Claim $claim
     * @return array
     */
    public function getStatuses(Claim $claim): array
    {
        $places = [];
        $dist = ClaimStatusDictionary::getCollection();

        foreach ($claim->workflowTransitions() as $transition) {
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
