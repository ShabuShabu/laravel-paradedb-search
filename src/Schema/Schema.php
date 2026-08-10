<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Schema;

use Illuminate\Contracts\Support\Arrayable;
use Throwable;

abstract readonly class Schema implements Arrayable
{
    final public function __construct(
        protected int $version,
    ) {}

    /**
     * @throws Throwable
     */
    public static function v(int $version): static
    {
        return new static($version);
    }

    protected function migrate(int $version): array
    {
        if (! method_exists($this, $method = "v$version")) {
            return [];
        }

        $this->$method($index = new Index);

        return $index->rawColumns();
    }

    /**
     * @throws Throwable
     */
    public function toArray(): array
    {
        $columns = collect(range(1, $this->version))
            ->reduce(fn (array $cols, int $version) => [
                ...$cols,
                ...$this->migrate($version),
            ], []);

        return array_values(array_filter($columns));
    }
}
