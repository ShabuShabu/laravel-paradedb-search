<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Schema;

use ShabuShabu\ParadeDB\Expressions\v2\Support\Type;

class Composite
{
    use HasTokenizers;

    public function column(string $name, string $type): Type
    {
        return $this->columns[$name] = new Type($name, $type);
    }
}
