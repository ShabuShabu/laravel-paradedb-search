<?php

namespace ShabuShabu\ParadeDB\Query;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\ParadeExpression;

class Search
{
    protected Builder $query;

    protected ?int $limit = null;

    protected ?int $offset = null;

    protected ?string $alias = null;

    protected ?bool $stableSort = null;

    protected array $columns = ['*'];

    public function __construct(
        protected Model $model
    ) {
    }

    public function select(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function query(Builder|ParadeExpression $query): static
    {
        $this->query = $query;

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

    public function get(): Collection
    {
        $search = new Expressions\Search(
            index: $this->model->getTable().config('paradedb-search.table_suffix'),
            query: $this->query,
            limit: $this->limit,
            offset: $this->offset,
            alias: $this->alias,
            stableSort: $this->stableSort,
        );

        $query = $this->model->newQuery();

        return $query
            ->select($this->columns)
            ->from($search->getValue($query->grammar))
            ->get();
    }
}
