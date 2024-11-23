<?php

namespace App\Enums;

class FileUpdateTypeEnum
{

    const NORMAl = 'normal';
    const FULL_UPDATE = 'update';

    public static function toArray()
    {
        return [
            self::NORMAl,
            self::FULL_UPDATE,
        ];
    }

}
