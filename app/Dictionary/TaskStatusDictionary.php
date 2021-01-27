<?php

namespace App\Dictionary;

class TaskStatusDictionary extends BaseDictionary
{
    public const
        OPEN = 'open',
        CLOSED = 'closed',
        ANALYSIS = 'analysis',
        IN_PROGRESS = 'in_progress',
        AWAITING_EVALUATION_CONFIRMATION = 'awaiting_evaluation_confirmation',
        EVALUATION_CONFIRMED = 'evaluation_confirmed',
        CHECK = 'check',
        CANCELED = 'canceled',
        ESTIMATE = 'estimate',
        ASSIGNEE = 'assignee';

    public static array $props = [
        self::OPEN => 'Открытый',
        self::CLOSED => 'Закрытый',
        self::ANALYSIS => 'Анализ',
        self::CHECK => 'Проверка',
        self::CANCELED => 'Отменить',
        self::AWAITING_EVALUATION_CONFIRMATION => 'Ожидание подтверждение оценки',
        self::EVALUATION_CONFIRMED => 'Оценка подтверждена',
        self::IN_PROGRESS => 'В работе',
    ];
}
