<?php

namespace App\Enums;

class FileStatusEnum
{

    const AVAILABLE = 1;
    const UNAVAILABLE = 0;

    public static function toArray()
    {
        return [
            self::AVAILABLE,
            self::UNAVAILABLE,
        ];
    }

}
