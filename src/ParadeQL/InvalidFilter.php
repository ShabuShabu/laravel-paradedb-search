<?php

namespace ShabuShabu\ParadeDB\ParadeQL;

use InvalidArgumentException;
use ShabuShabu\ParadeDB\ParadeQL\Operators\Filter;
use ShabuShabu\ParadeDB\ParadeQL\Operators\Range;

final class InvalidFilter extends InvalidArgumentException
{
    public static function unknownFilterOperator(string $operator): self
    {
        return new self(
            "Operator `$operator` is not a valid filter operator. Valid operators are ".Filter::format()
        );
    }

    public static function unknownRangeOperator(string $operator): self
    {
        return new self(
            "Operator `$operator` is not a valid range operator. Valid operators are ".Range::format()
        );
    }

    public static function malformedRange(array $value): self
    {
        $message = match (true) {
            count($value) > 2 => 'A range filter must be an array of exactly two values',
            ! is_int($value[0]) || ! is_int($value[1]) => 'A range filter must consist only of integers',
            $value[0] >= $value[1] => 'Range filter values must be in order from lowest to highest',
            default => 'The range filter is invalid',
        };

        return new self($message);
    }
}
