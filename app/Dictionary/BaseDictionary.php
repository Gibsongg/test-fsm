<?php

namespace App\Dictionary;

use Illuminate\Support\Collection;

class BaseDictionary
{
    public static array $props;

    public static function getCollection(): Collection
    {
        return collect(static::$props);
    }

    public static function labelByKey(string $key): string
    {
        return static::$props[$key];
    }
}
