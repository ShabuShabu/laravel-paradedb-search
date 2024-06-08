<?php

namespace ShabuShabu\ParadeDB\Indices;

use Illuminate\Support\Facades\DB;

class Hnsw
{
    final protected function __construct(
        protected string $table,
        protected string $column,
        protected string $schema,
        protected DistanceMetric $distanceMetric = DistanceMetric::denseL2,
        protected int $maxConnections = 16,
        protected int $efConstruction = 64
    ) {
    }

    public static function index(string $table, string $column, string $schema = 'public'): static
    {
        return new static($table, $column, $schema);
    }

    public function distanceMetric(DistanceMetric $metric): static
    {
        $this->distanceMetric = $metric;

        return $this;
    }

    public function maxConnections(int $maxConnections): static
    {
        $this->maxConnections = $maxConnections;

        return $this;
    }

    public function efConstruction(int $value): static
    {
        $this->efConstruction = $value;

        return $this;
    }

    protected function indexName(): string
    {
        return "{$this->table}_{$this->column}_hnsw_index";
    }

    public function create(bool $drop = false): void
    {
        if ($drop) {
            $this->drop();
        }

        DB::statement(
            <<<QUERY
            CREATE INDEX {$this->indexName()}
            ON $this->schema.$this->table
            USING hnsw ($this->column {$this->distanceMetric->value})
            WITH (m = $this->maxConnections, ef_construction = $this->efConstruction);
            QUERY
        );
    }

    public function drop(): void
    {
        DB::statement(
            <<<QUERY
            DROP INDEX {$this->indexName()};
            QUERY
        );
    }
}
