<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions\Ranges;

readonly class Date extends DateTime
{
    protected function format(): string
    {
        return 'Y-m-d';
    }

    protected function castAs(): string
    {
        return 'daterange';
    }
}
