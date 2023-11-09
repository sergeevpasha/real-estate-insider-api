<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationLanguage: string
{
    case ENGLISH = 'en';
    case RUSSIAN = 'ru';


    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}