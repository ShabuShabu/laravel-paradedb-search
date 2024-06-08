<?php

namespace ShabuShabu\ParadeDB\Concerns;

use Illuminate\Database\Eloquent\Model;
use ShabuShabu\ParadeDB\Query\Search;

trait CanSearch
{
    public static function search(): Search
    {
        /** @var Model $model */
        $model = new static;

        return new Search(
            table: $model->getTable(),
            builder: $model->newQuery(),
        );
    }
}
