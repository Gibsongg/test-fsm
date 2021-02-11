<?php

namespace App\Listeners;

use App\Mail\WorkflowInProgressMail;
use App\Models\Job;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;
use ZeroDaHero\LaravelWorkflow\Events\EnteredEvent;
use ZeroDaHero\LaravelWorkflow\Events\EnterEvent;
use ZeroDaHero\LaravelWorkflow\Events\GuardEvent;
use ZeroDaHero\LaravelWorkflow\Events\TransitionEvent;

/**
 * Class WorkflowTaskListener
 * @package App\Listeners
 * @see https://symfony.com/doc/current/workflow.html#using-events
 */
class WorkflowJobListener
{

    /**
     * Блокируем переход "development" если не выполнено два условия
     * @param GuardEvent $event
     */
    public function development(GuardEvent $event): void
    {
        /** @var Job $model $model */
        $model = $event->getSubject();

        $marking = $model->workflowGet()->getMarking($model)->getPlaces();

        if (!array_key_exists('team_formed', $marking) || !array_key_exists('signature', $marking)) {
            $event->setBlocked(true);
        }
    }

    /**
     * Перед переходом в месте "cancel" снимаем выделение со всех параллельных мест. Когда срабатывает сам переход то
     * то запись помечается актуальным значеним
     * @param EnterEvent $event
     */
    public function cancel(EnterEvent $event): void
    {
        //Убираем все лишние состояния до обновления метки состояния
        foreach ($event->getMarking()->getPlaces() as $place => $token) {
            //тут можно проверить, если запись содержала метку "Команда сформирована" то можно отправить сообщение
            $event->getMarking()->unmark($place);

        }
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            ['workflow.job.guard.development'],
            [self::class, 'development']
        );

        $events->listen(
            ['workflow.job.enter.cancel'],
            [self::class, 'cancel']
        );
    }
}
