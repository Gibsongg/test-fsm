<?php

namespace App\Listeners;

use App\Models\Claim;
use Illuminate\Events\Dispatcher;
use ZeroDaHero\LaravelWorkflow\Events\GuardEvent;

/**
 * Class WorkflowClaimListener
 * @package App\Listeners
 * @see https://symfony.com/doc/current/workflow.html#using-events
 */
class WorkflowClaimListener
{

    /**
     * Вытягиваем metadata из конфигурации бизнес процесса
     * @param Claim $model
     * @param $key
     * @return string|null
     */
    public function getMetadata(Claim $model, $key): ?string
    {
        foreach ($model->workflowGet()->getDefinition()->getTransitions() as $transition) {
            if ($transition->getName() === 'overdue') {
                return $model->workflowGet()->getMetadataStore()->getTransitionMetadata($transition)[$key] ?? null;
            }
        }

        return null;
    }


    /**
     * Блокируем переход "overdue" если заявка актуальная
     * @param GuardEvent $event
     */
    public function overdueHide(GuardEvent $event): void
    {
        /** @var Claim $model $model */
        $model = $event->getSubject();
        $day = (int)$this->getMetadata($model, 'days_limit');

        //Блокируем переход "overdue" пока заявка актуальна по времени
        if ($model->isOverdue($day)) {
            $event->setBlocked(true);
        }
    }

    /**
     * Блокирование переходов если заявка просроченна.
     * @param GuardEvent $event
     */
    public function blockTransition(GuardEvent $event): void
    {
        /** @var Claim $model $model */
        $model = $event->getSubject();
        $day = (int)$this->getMetadata($model, 'days_limit');

        //Блокируем переходы "inProgress_cancel", "inProgress_complete" если заявка просрочена
        if (!$model->isOverdue($day)) {
            $event->setBlocked(true);
        }
    }


    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            'workflow.claim.guard.overdue',
            [self::class, 'overdueHide']
        );

        $events->listen(
            ['workflow.claim.guard.inProgress_cancel', 'workflow.claim.guard.inProgress_complete'],
            [self::class, 'blockTransition']
        );
    }
}
