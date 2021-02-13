<?php
use App\Dictionary\ClaimStatusDictionary as Dist;
use App\Models\Claim;

return [
    'claim' => [
        'type' => 'state_machine',
        'marking_store' => [
            'property' => 'status',
            'type' => 'single_state',
        ],
        'supports' => [Claim::class],
        'places' => [
            Dist::NEW => [
                'metadata' => [
                    'label' => Dist::labelByKey(Dist::NEW)
                ]
            ],
            Dist::PROCESSING => [
                'metadata' => [
                    'label' => Dist::labelByKey(Dist::PROCESSING)
                ]
            ],
            Dist::INPROGRESS => [
                'metadata' => [
                    'label' => Dist::labelByKey(Dist::INPROGRESS)
                ]
            ],
            Dist::COMPLETE => [
                'metadata' => [
                    'label' => Dist::labelByKey(Dist::COMPLETE)

                ]
            ],
            Dist::CANCEL => [
                'metadata' => [
                    'label' => Dist::labelByKey(Dist::CANCEL)
                ]
            ],
            Dist::TIMEOUT => [
                'metadata' => [
                    'label' => Dist::labelByKey(Dist::TIMEOUT)
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
                    'days_limit' => 5,
                    'arrow_color' => 'brown',
                ]
            ]
        ]
    ]
];
