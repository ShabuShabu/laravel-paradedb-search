<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\TantivyQL\Operators;

use BackedEnum;
use Illuminate\Support\Str;

trait CanQuery
{
    public static function all(): array
    {
        return collect(self::cases())
            ->map(fn (BackedEnum $enum) => $enum->value)
            ->all();
    }

    public static function format(): string
    {
        return collect(self::cases())
            ->map(fn (BackedEnum $enum) => Str::wrap($enum->value, '`'))
            ->join(', ', ' and ');
    }

    public static function contains(string | BackedEnum $operator): bool
    {
        if ($operator instanceof BackedEnum) {
            $operator = (string) $operator->value;
        }

        return in_array($operator, self::all(), true);
    }
}
