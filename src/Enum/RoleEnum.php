<?php

namespace App\Enum;

enum Role: string
{
    case ADMIN = 'admin';
    case AGRICULTURE = 'agriculture';
    case CLIENT = 'client';
    case ASSOCIATION = 'association';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::AGRICULTURE => 'Agriculture',
            self::CLIENT => 'Client',
            self::ASSOCIATION => 'Association',
        };
    }
}