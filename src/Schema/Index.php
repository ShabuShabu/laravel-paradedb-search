<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Schema;

use ShabuShabu\ParadeDB\Expressions\v2\Casts\Row;

class Index
{
    use HasTokenizers;

    public function column(string $name): string
    {
        return $this->columns[$name] = $name;
    }

    public function id(string $name = 'id'): string
    {
        return $this->column($name);
    }

    public function remove(string $name): null
    {
        return $this->columns[$name] = null;
    }

    public function row(string $type, array $columns): Row
    {
        return $this->columns[$type] = new Row($type, $columns);
    }
}
