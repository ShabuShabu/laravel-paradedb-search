<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Facades;

use Illuminate\Support\Facades\Facade;
use ShabuShabu\ParadeDB\Search;

/**
 * @see Search
 */
class ParadeDB extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Search::class;
    }
}
