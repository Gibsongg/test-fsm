<?php

use App\Dictionary\ClaimStatusDictionary;
use App\Models\Claim;
use App\Models\Job;
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
    ],
    'claim' => [
        'type' => 'state_machine',
        'marking_store' => [
            'property' => 'status',
            'type' => 'single_state',
        ],
        'supports' => [Claim::class],
        'places' => [
            ClaimStatusDictionary::NEW => [
                'metadata' => [
                    'label' => ClaimStatusDictionary::labelByKey(ClaimStatusDictionary::NEW)
                ]
            ],
            ClaimStatusDictionary::PROCESSING => [
                'metadata' => [
                    'label' => ClaimStatusDictionary::labelByKey(ClaimStatusDictionary::PROCESSING)
                ]
            ],
            ClaimStatusDictionary::INPROGRESS => [
                'metadata' => [
                    'label' => ClaimStatusDictionary::labelByKey(ClaimStatusDictionary::INPROGRESS)
                ]
            ],
            ClaimStatusDictionary::COMPLETE => [
                'metadata' => [
                    'label' => ClaimStatusDictionary::labelByKey(ClaimStatusDictionary::COMPLETE)

                ]
            ],
            ClaimStatusDictionary::CANCEL => [
                'metadata' => [
                    'label' => ClaimStatusDictionary::labelByKey(ClaimStatusDictionary::CANCEL)
                ]
            ],
            ClaimStatusDictionary::TIMEOUT => [
                'metadata' => [
                    'label' => ClaimStatusDictionary::labelByKey(ClaimStatusDictionary::TIMEOUT)
                ]
            ]
        ],
        'transitions' => [
            'new_processing' => [
                'from' => 'new',
                'to' => ['processing'],
                'metadata' => [
                    'label' => 'В обработку'
                ]
            ],
            'processing_inProgress' => [
                'from' => 'processing',
                'to' => ['inProgress'],
                'metadata' => [
                    'label' => 'В работу'
                ]
            ],
            'inProgress_complete' => [
                'from' => 'inProgress',
                'to' => 'complete',
                'metadata' => [
                    'label' => 'Завершить'
                ]
            ],
            'inProgress_cancel' => [
                'from' => 'inProgress',
                'to' => 'cancel',
                'metadata' => [
                    'label' => 'Отменить'
                ]
            ],
            'overdue' => [
                'from' => ['inProgress', 'processing'],
                'to' => 'timeout',
                'metadata' => [
                    'label' => "Просрочено\n(> 5 дней)",
                    'days_limit' => 5
                ]
            ]
        ]
    ],
    'job' => [
        'type' => 'workflow',
        'marking_store' => [
            'property' => 'status',
            'type' => 'multiple_state',
        ],
        'supports' => [Job::class],
        'initial_places' => ['new'],
        'places' => [
            'new' => [
                'metadata' => [
                    'label' => 'Новая заявка'
                ]
            ],
            'consideration' => [
                'metadata' => [
                    'label' => 'Рассмотрение'
                ]
            ],
            'cancel' => [
                'metadata' => [
                    'label' => 'Отмена'
                ]
            ],
            'team_building' => [
                'metadata' => [
                    'label' => 'Формирование команды'
                ]
            ],
            'team_formed' => [
                'metadata' => [
                    'label' => 'Команда сформирована'
                ]
            ],
            'documents' => [
                'metadata' => [
                    'label' => 'Подготовка документов'
                ]
            ],
            'signature' => [
                'metadata' => [
                    'label' => 'Документы подписаны'
                ]
            ],
            'development' => [
                'metadata' => [
                    'label' => 'Работа бригады'
                ]
            ],
            'check' => [
                'metadata' => [
                    'label' => 'Проверка'
                ]
            ],
            'approve' => [
                'metadata' => [
                    'label' => 'Принято'
                ]
            ],
            'rejected' => [
                'metadata' => [
                    'label' => 'Отклонено'
                ]
            ],
        ],
        'transitions' => [
            'new_consideration' => [
                'from' => ['new'],
                'to' => ['consideration'],
                'metadata' => [
                    'label' => 'Обработать'
                ]
            ],
            'consideration_next' => [
                'from' => ['consideration'],
                'to' => ['team_building', 'documents'],
                'metadata' => [
                    'label' => 'Отправить в работу'
                ]
            ],
            'consideration_rejected' => [
                'from' => ['consideration'],
                'to' => ['rejected'],
                'metadata' => [
                    'label' => 'Отменить'
                ]
            ],
            'documents_signature' => [
                'from' => ['documents'],
                'to' => ['signature'],
                'metadata' => [
                    'label' => 'Подписать документы'
                ]
            ],
            'development' => [
                'from' => ['team_formed', 'signature'],
                'to' => ['development'],
                'metadata' => [
                    'label' => 'Начать выполнение работ',
                    'arrow_color' => 'brown'
                ]
            ],
            'team_building_team_formed' => [
                'from' => ['team_building'],
                'to' => ['team_formed'],
                'metadata' => [
                    'label' => 'Команду собрали'
                ]
            ],
            'build_check' => [
                'from' => ['development'],
                'to' => ['check'],
                'metadata' => [
                    'label' => 'Проверка работ'
                ]
            ],
            'check_approve' => [
                'from' => ['check'],
                'to' => ['approve'],
                'metadata' => [
                    'label' => 'Работы приняты'
                ]
            ],
            'check_reject' => [
                'from' => ['check'],
                'to' => ['rejected'],
                'metadata' => [
                    'label' => 'Работы отклонены'
                ]
            ],
            'reject_development' => [
                'from' => ['rejected'],
                'to' => ['development'],
                'metadata' => [
                    'label' => 'Доработка'
                ]
            ],
            'documents_cancel' => [
                'from' => ['documents'],
                'to' => ['cancel'],
                'metadata' => [
                    'label' => 'Отменить контракт',
                    'arrow_color' => 'brown'
                ]
            ],
        ]
    ]
];

