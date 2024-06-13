<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions\Ranges;

readonly class Timestamp extends DateTime
{
    protected function format(): string
    {
        return 'Y-m-d H:i:s';
    }

    protected function castAs(): string
    {
        return 'tsrange';
    }
}
