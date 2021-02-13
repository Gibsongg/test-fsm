<?php

use App\Models\Job;

return [
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
