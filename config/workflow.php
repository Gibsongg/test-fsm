<?php

use App\Models\Claim;
use App\Models\Task;

return [
    'straight' => [
        'type' => 'state_machine',
        'marking_store' => [
            'property' => 'status',
            //'type' => 'single_state',
        ],
        'supports' => [Task::class],
        'places' => [
            'open',
            'analysis',
            'check',
            'closed',
            'canceled' => [
                'metadata' => [
                    'description' => 'descerer',
                    'label' => 'Отменен',
                    'bg_color' => 'red',
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
                    'arrow_color' => 'blue',
                    'color' => 'brown',
                    'description' => 'descerer',
                ]
            ],
            'open_canceled' => [
               // 'guard' => 'subject.status = 1',
                'guard' => 'subject.isTimeout()',
                'from' => 'open',
                'to' => 'canceled',
                'metadata' => ['test' => 1]
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
                'to' => ['check', 'expired'],
                'metadata' => [
                    'label' => "in_progress_check \n * (событие если просрочено)",
                    'arrow_color' => 'brown',
                    'hour_limit' => 12
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
            ],
        ],
    ],
    'claim' => [
        'type' => 'state_machine',
        'marking_store' => [
            'property' => 'status',
            'type' => 'single_state',
        ],
        'supports' => [Claim::class],
        'places' => [
            'new', 'processing', 'inProgress', 'complete', 'cancel', 'timeout'
        ],
        'transitions' => [
            'new_processing' => [
                'from' => 'new',
                'to' => ['processing']
            ],
            'new_cancel' => [
                'from' => 'new',
                'to' => ['cancel']
            ],
            'processing_inProgress' => [
                'from' => 'processing',
                'to' => ['inProgress'],
            ],
            'inProgress_complete' => [
                'from' => 'inProgress',
                'to' => 'complete',
            ],
            'inProgress_cancel' => [
                'from' => 'inProgress',
                'to' => 'cancel',
            ],
            'overdue' => [
                //'from' => ['processing'],
                'from' => ['inProgress', 'processing'],
                'to' => 'timeout',
                'metadata' => [
                    'label' => "overdue\n(> 5 day)",
                    'timeout_day' => 7
                ]
            ]
        ]
    ],
    'test' => [
        'type' => 'state_machine',
        'marking_store' => [
            'type' => 'single_state',
            'property' => 'marking'
        ],
        'supports' => [Claim::class],
        'places' => [
            'a',
            'b'
        ],
        'transitions' => [
            't1' => [
                'from' => 'a',
                'to' => 'a',
            ],
            'b' => [
                'from' => ['a', 'b'],
                'to' => 'b',
            ]
        ]
    ]
];

