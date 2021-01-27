<?php

use App\Models\Task;

return [
    'straight' => [
        'type' => 'state_machine',
        'audit_trail' => [
            'enabled' => true
        ],
        'marking_store' => [
            'property' => 'status',
            'type' => 'single_state',
        ],
        'supports' => [Task::class],
        'places' => [
            'open',
            'analysis',
            'check',
            'closed',
            'canceled',
            'in_progress',
            'awaiting_evaluation_confirmation',
            'evaluation_confirmed'
        ],
        'transitions' => [
            'open_analysis' => [
                'from' => 'open',
                'to' => 'analysis',
            ],
            'open_canceled' => [
                'from' => 'open',
                'to' => 'canceled',
            ],
            'analysis_in_progress' => [
                'from' => 'analysis',
                'to' => 'in_progress',
            ],
            'analysis_canceled' => [
                'from' => 'analysis',
                'to' => 'canceled',
            ],
            'analysis_awaiting_evaluation_confirmation' => [
                'from' => 'analysis',
                'to' => 'awaiting_evaluation_confirmation',
            ],
            'in_progress_check' => [
                'from' => 'in_progress',
                'to' => 'check',
            ],
            'in_progress_analysis' => [
                'from' => 'in_progress',
                'to' => 'analysis',
            ],
            'check_analysis' => [
                'from' => 'check',
                'to' => 'analysis',
            ],
            'check_closed' => [
                'from' => 'check',
                'to' => 'closed',
            ],
            'awaiting_evaluation_confirmation_canceled' => [
                'from' => 'awaiting_evaluation_confirmation',
                'to' => 'canceled',
            ],
            'awaiting_evaluation_confirmation_evaluation_confirmed' => [
                'from' => 'awaiting_evaluation_confirmation',
                'to' => 'evaluation_confirmed',
            ],
            'awaitingEvaluationConfirmation_analysis' => [
                'from' => 'awaiting_evaluation_confirmation',
                'to' => 'analysis',
            ],
            'evaluation_confirmed_in_progress' => [
                'from' => 'evaluation_confirmed',
                'to' => 'in_progress',
            ],
        ],
    ],
];
