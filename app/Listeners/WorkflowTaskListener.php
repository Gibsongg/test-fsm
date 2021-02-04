<?php

namespace App\Listeners;

use App\Mail\WorkflowInProgressMail;
use App\Models\Claim;
use App\Models\Task;
use Carbon\Carbon;
use http\Exception\RuntimeException;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Workflow\TransitionBlocker;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\WorkflowInterface;
use ZeroDaHero\LaravelWorkflow\Events\EnteredEvent;
use ZeroDaHero\LaravelWorkflow\Events\GuardEvent;
use ZeroDaHero\LaravelWorkflow\Events\LeaveEvent;
use function Webmozart\Assert\Tests\StaticAnalysis\null;

/**
 * Class WorkflowTaskListener
 * @package App\Listeners
 * @see https://symfony.com/doc/current/workflow.html#using-events
 */
class WorkflowTaskListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function check(GuardEvent $event): void
    {
        /** @var Task $model $model */
        $model = $event->getSubject();
        $hours = $event->getMetadata('hour_limit', $event->getTransition());

        $date = $model->created_at->addHours($hours);
        $currentDate = Carbon::now();

        $event->setBlocked($currentDate > $date);
    }

    public function expired(GuardEvent $event): void
    {
        /** @var Task $model $model */
        $model = $event->getSubject();
        $hours = $event->getMetadata('hour_limit', $event->getTransition());

        $date = $model->created_at->addHours($hours);
        $currentDate = Carbon::now();

        if ($currentDate <= $date) {
            $event->setBlocked($currentDate);
        }
    }

    /**
     * Скрываем переход таймаута если время не просрочено
     * @param GuardEvent $event
     */
/*    public function claimOverdue(GuardEvent $event) {
        $model = $event->getSubject();
        $hours = $event->getMetadata('timeout_day', $event->getTransition());

        $date = $model->created_at->addDays($hours);
        $currentDate = Carbon::now();

        if ($currentDate < $date) {
            $event->setBlocked(true);
        }
    }*/


    public function leave(LeaveEvent $event ) {
        //echo '<pre>' . print_r('dfdf', true) . '</pre>';
        /** @var Claim $model */
        $model = $event->getSubject();

        $model->workflow_apply('overdue');
        $model->save();
        redirect()->route('claims.index');
    }

    public function onOverdue(GuardEvent $event) {
        /** @var Claim $model */
        $model = $event->getSubject();
        $hours = $event->getMetadata('timeout_day', $event->getTransition());
        $date = $model->created_at->addDays($hours);

        $model->workflow_force('timeout');
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            'workflow.claim.guard.overdue',
            [self::class, 'onOverdue']
        );

/*        $events->listen(
            'workflow.claim.leave.processing',
            [self::class, 'leave']
        );*/


  /*      $events->listen(
            'workflow.straight.guard.in_progress_check',
            [self::class, 'check']
        );*/

   /*     $events->listen(
            'workflow.claim.announce.overdue',
            [self::class, 'onOverdue']
        );*/


      /*  $events->listen(
            'workflow.straight.transition.overdue',
            [self::class, 'overdue']
        );*/
    }
}
