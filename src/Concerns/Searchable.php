<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Concerns;

use ShabuShabu\ParadeDB\Query\Search;

trait Searchable
{
    public static function search(): Search
    {
        return new Search(model: new static);
    }
}
