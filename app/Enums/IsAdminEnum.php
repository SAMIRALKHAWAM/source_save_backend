<?php

namespace App\Enums;

class IsAdminEnum
{

    const ADMIN = 1;
    const NOT_ADMIN = 0;

    public static function toArray(){
        return [
            self::ADMIN,
            self::NOT_ADMIN,
        ];
    }
}
