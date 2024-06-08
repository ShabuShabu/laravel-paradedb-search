<?php

namespace ShabuShabu\ParadeDB\Query;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\FullTextSearch;
use ShabuShabu\ParadeDB\Query\Expressions\HybridSearch;
use ShabuShabu\ParadeDB\Query\Expressions\ParadeExpression;

class Search
{
    protected Builder|ParadeExpression $paradeQuery;

    protected ?int $limit = null;

    protected ?int $offset = null;

    protected ?string $alias = null;

    protected ?bool $stableSort = null;

    protected int $bm25Limit = 100;

    protected int|float $bm25Weight = 0.5;

    protected int $similarityLimit = 100;

    protected int|float $similarityWeight = 0.5;

    protected ?string $similarityQuery = null;

    protected array $columns = ['*'];

    public function __construct(
        protected string $table,
        protected Eloquent\Builder $builder,
    ) {
    }

    public function select(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function query(Builder|ParadeExpression $query): static
    {
        $this->paradeQuery = $query;

        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function alias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function stableSort(bool $stable = true): static
    {
        $this->stableSort = $stable;

        return $this;
    }

    public function fullText(): Collection
    {
        return $this->execute(new FullTextSearch(
            index: $this->indexName(),
            query: $this->paradeQuery,
            limit: $this->limit,
            offset: $this->offset,
            alias: $this->alias,
            stableSort: $this->stableSort,
        ));
    }

    public function similarityQuery(string|Expression $column, SimOp $operator, string|Expression $value): static
    {
        $column = $column instanceof Expression
            ? (string) $column->getValue($this->builder->grammar)
            : $column;

        $value = $value instanceof Expression
            ? (string) $value->getValue($this->builder->grammar)
            : $value;

        $this->similarityQuery = "$column $operator->value $value";

        return $this;
    }

    public function bm25Limit(int $limit): static
    {
        $this->bm25Limit = $limit;

        return $this;
    }

    public function bm25Weight(int|float $weight): static
    {
        $this->bm25Weight = $weight;

        return $this;
    }

    public function similarityLimit(int $limit): static
    {
        $this->similarityLimit = $limit;

        return $this;
    }

    public function similarityWeight(int|float $weight): static
    {
        $this->similarityWeight = $weight;

        return $this;
    }

    public function hybrid(): Collection
    {
        return $this->execute(new HybridSearch(
            index: $this->indexName(),
            bm25Query: $this->paradeQuery,
            similarityQuery: $this->similarityQuery,
            bm25Limit: $this->bm25Limit,
            bm25Weight: $this->bm25Weight,
            similarityLimit: $this->similarityLimit,
            similarityWeight: $this->similarityWeight,
        ));
    }

    public function get(): Collection
    {
        return $this->similarityQuery
            ? $this->hybrid()
            : $this->fullText();
    }

    protected function execute(FullTextSearch|HybridSearch $search): Collection
    {
        return $this->builder
            ->select($this->columns)
            ->from($search->getValue($this->builder->grammar))
            ->get();
    }

    protected function indexName(): string
    {
        return $this->table.config('paradedb-search.table_suffix');
    }
}
