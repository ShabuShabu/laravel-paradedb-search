<?php

namespace ShabuShabu\ParadeDB\ParadeQL;

use Illuminate\Support\Str;
use InvalidArgumentException;

final class InvalidFilter extends InvalidArgumentException
{
    public static function unknownFilterOperator(string $operator, array $operators): self
    {
        return new self(
            "Operator `$operator` is not a valid filter operator. Valid operators are ".self::formatList($operators)
        );
    }

    public static function unknownRangeOperator(string $operator, array $operators): self
    {
        return new self(
            "Operator `$operator` is not a valid range operator. Valid operators are ".self::formatList($operators)
        );
    }

    public static function malformedRange(array $value): self
    {
        $message = match (true) {
            count($value) > 2 => 'A range filter must be an array of exactly two values',
            ! is_int($value[0]) || ! is_int($value[1]) => 'A range filter must consist only of integers',
            $value[0] >= $value[1] => 'Range filter values must be in order from smallest to highest',
        };

        return new self($message);
    }

    protected static function formatList(array $values): string
    {
        return collect($values)
            ->map(fn (string $value) => Str::wrap($value, '`'))
            ->join(', ', ' and ');
    }
}
