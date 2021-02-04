<?php

namespace ZeroDaHero\LaravelWorkflow\Traits;

use Symfony\Component\Workflow\Marking;
use Workflow;
use Symfony\Component\Workflow\Workflow as WorkflowSymfony;

/**
 * @author Boris Koumondji <brexis@yahoo.fr>
 */
trait WorkflowTrait
{
    public function workflowApply($transition, $workflow = null): Marking
    {
        return $this->workflowGet($workflow)->apply($this, $transition);
    }

    public function workflowCan($transition, $workflow = null): bool
    {
        return $this->workflowGet($workflow)->can($this, $transition);
    }

    public function workflowTransitions($workflow = null): array
    {
        return $this->workflowGet($workflow)->getEnabledTransitions($this);
    }

    public function workflowGet($workflow = null): WorkflowSymfony
    {
        return Workflow::get($this, $workflow);
    }

}
