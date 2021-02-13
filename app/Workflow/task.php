<?php

use App\Models\Task;

return [
    'task' => [
        'type' => 'state_machine',
        'marking_store' => [
            'property' => 'status',
        ],
        'supports' => [Task::class],
        'places' => [
            'open',
            'analysis',
            'check',
            'closed' => [
                'metadata' => [
                    'bg_color' => '#28a745'
                ]
            ],
            'canceled' => [
                'metadata' => [
                    'label' => 'Отменен',
                    'bg_color' => '#dc3545',
                ]
            ],
            'in_progress',
            'awaiting_evaluation_confirmation',
            'evaluation_confirmed',
            'expired'
        ],
        'transitions' => [
            'open_analysis' => [
                'from' => 'open',
                'to' => 'analysis',
                'metadata' => [
                    'arrow_color' => '#17a2b8',
                    'color' => '#17a2b8',
                ]
            ],
            'open_canceled' => [
                // 'guard' => 'subject.status = 1',
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
                'guard' => 'subject.isOverdue(5)',
                'from' => 'in_progress',
                'to' => 'check',
                'metadata' => [
                    'label' => "in_progress_check \n * (если актуально по времени)",
                    'arrow_color' => 'brown',
                ]
            ],
            'in_progress_expired' => [
                'guard' => '!subject.isOverdue(5)',
                'from' => 'in_progress',
                'to' => 'expired',
                'metadata' => [
                    'label' => "in_progress_expired \n * (если просрочено)",
                    'arrow_color' => 'brown',
                ]
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
                'metadata' => [
                    'arrow_color' => 'brown',
                    'label' => "evaluation_confirmed_in_progress \n *(событие Отправка письма)"
                ]
            ],
        ],
    ]
];
