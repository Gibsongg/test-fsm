<?php

namespace App\Listeners;

use App\Mail\WorkflowInProgressMail;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use ZeroDaHero\LaravelWorkflow\Events\EnteredEvent;

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

    public function inProgress(EnteredEvent $event): void
    {
        Mail::to('clouds@bk.ru')->send(new WorkflowInProgressMail($event->getSubject()));
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
            'workflow.straight.entered.in_progress',
            [self::class, 'inProgress']
        );
    }
}
