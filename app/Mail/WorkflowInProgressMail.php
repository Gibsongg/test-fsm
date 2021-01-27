<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkflowInProgressMail extends Mailable
{
    use Queueable, SerializesModels;

    protected Task $task;
    protected string $place;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }


    public function build(): self
    {
        return $this->view('emails.task.in-progress', ['task' => $this->task]);
    }
}
