<?php

namespace ShabuShabu\ParadeDB\Concerns;

use ShabuShabu\ParadeDB\Query\Search;

trait CanSearch
{
    public static function search(): Search
    {
        return new Search(new static);
    }
}
