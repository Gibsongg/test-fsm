<?php

namespace App\Listeners;

use App\Mail\WorkflowInProgressMail;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;
use ZeroDaHero\LaravelWorkflow\Events\TransitionEvent;

/**
 * Class WorkflowTaskListener
 * @package App\Listeners
 * @see https://symfony.com/doc/current/workflow.html#using-events
 */
class WorkflowTaskListener
{

    public function inProgress(TransitionEvent $event): void
    {
        Mail::to('example@address.test')->send(new WorkflowInProgressMail($event->getSubject()));
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            'workflow.task.transition.evaluation_confirmed_in_progress',
            [self::class, 'inProgress']
        );
    }
}
