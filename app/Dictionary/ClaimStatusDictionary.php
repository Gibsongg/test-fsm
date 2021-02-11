<?php

namespace App\Dictionary;

class ClaimStatusDictionary extends BaseDictionary
{
    public const
        NEW = 'new',
        PROCESSING = 'processing',
        INPROGRESS = 'inProgress',
        COMPLETE = 'complete',
        CANCEL = 'cancel',
        TIMEOUT = 'timeout';

    public static array $props = [
        self::NEW => 'Новая заявка',
        self::PROCESSING => 'В обработке',
        self::INPROGRESS => 'В работе',
        self::COMPLETE => 'Завершено',
        self::CANCEL => 'Отменено',
        self::TIMEOUT => 'Просрочено',
    ];
}
