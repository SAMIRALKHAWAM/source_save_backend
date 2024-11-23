<?php

namespace App\Enums;

class GroupStatusEnum
{

    const PENDING = 'pending';
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';

    public static function toArray()
    {
        return [
            self::PENDING,
            self::ACCEPTED,
        ];
    }

    public static function changeStatus()
    {
        return [
            self::REJECTED,
            self::ACCEPTED,
        ];
    }

    public static function allStatus()
    {
        return [
            self::PENDING,
            self::REJECTED,
            self::ACCEPTED,
        ];
    }
}
