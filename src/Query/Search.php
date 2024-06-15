<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;
use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query;
use Illuminate\Pagination\Paginator;
use InvalidArgumentException;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\FullTextSearch;
use ShabuShabu\ParadeDB\Query\Expressions\HybridSearch;
use ShabuShabu\ParadeDB\Query\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Query\Expressions\Similarity;

class Search
{
    protected null | Builder | ParadeExpression $where = null;

    protected ?int $limit = null;

    protected ?int $offset = null;

    protected ?string $alias = null;

    protected ?bool $stableSort = null;

    protected int $bm25Limit = 100;

    protected int | float $bm25Weight = 0.5;

    protected int $similarityLimit = 100;

    protected int | float $similarityWeight = 0.5;

    protected ?Similarity $similarityWhere = null;

    protected array $columns = ['*'];

    protected ?Closure $modifyQueryCallback = null;

    public function __construct(
        protected Eloquent\Model $model,
        protected ?string $indexName = null,
    ) {
    }

    public function select(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function modifyQueryUsing(Closure $callback): static
    {
        $this->modifyQueryCallback = $callback;

        return $this;
    }

    public function where(Builder | ParadeExpression | Similarity $where): static
    {
        if ($where instanceof Similarity) {
            $this->similarityWhere = $where;
        } else {
            $this->where = $where;
        }

        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = max(0, $offset);

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function alias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    public function stableSort(bool $stable = true): static
    {
        $this->stableSort = $stable;

        return $this;
    }

    protected function fullTextSearch(): Eloquent\Builder
    {
        return $this
            ->applyQueryModifications()
            ->select($this->columns)
            ->from(new FullTextSearch(
                index: $this->indexName(),
                query: $this->where,
                limit: $this->limit,
                offset: $this->offset,
                alias: $this->alias,
                stableSort: $this->stableSort,
            ));
    }

    public function bm25Limit(int $limit): static
    {
        $this->bm25Limit = $limit;

        return $this;
    }

    public function bm25Weight(int | float $weight): static
    {
        $this->bm25Weight = $weight;

        return $this;
    }

    public function similarityLimit(int $limit): static
    {
        $this->similarityLimit = $limit;

        return $this;
    }

    public function similarityWeight(int | float $weight): static
    {
        $this->similarityWeight = $weight;

        return $this;
    }

    protected function hybridSearch(): Eloquent\Builder
    {
        $innerQuery = $this
            ->query()
            ->select(['*'])
            ->from(new HybridSearch(
                index: $this->indexName(),
                bm25Query: $this->where,
                similarityQuery: $this->similarityWhere,
                bm25Limit: $this->bm25Limit,
                bm25Weight: $this->bm25Weight,
                similarityLimit: $this->similarityLimit,
                similarityWeight: $this->similarityWeight,
            ));

        $this->columns[] = 'search.rank_hybrid';

        return $this
            ->applyQueryModifications()
            ->select($this->columns)
            ->leftJoinSub($innerQuery, 'search', $this->query()->qualifyColumn('id'), 'search.id');
    }

    public function get(): Collection
    {
        return $this->toQuery()->get();
    }

    public function simplePaginate(?int $perPage = null, string $pageName = 'page', ?int $page = null): PaginatorContract
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $items = $this
            ->offset(($page - 1) * $perPage)
            ->limit($perPage + 1)
            ->get();

        return Container::getInstance()->makeWith(Paginator::class, [
            'items' => $items,
            'perPage' => $perPage,
            'currentPage' => $page,
            'options' => [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ],
        ]);
    }

    public function toQuery(): Eloquent\Builder
    {
        if (! $this->where) {
            throw new InvalidArgumentException(
                'Both hybrid and full-text search require a ParadeDB query'
            );
        }

        return $this->similarityWhere
            ? $this->hybridSearch()
            : $this->fullTextSearch();
    }

    public function toBaseQuery(): Query\Builder
    {
        return $this->toQuery()->toBase();
    }

    protected function indexName(): string
    {
        return $this->indexName ?? $this->model->getTable() . config('paradedb-search.index_suffix', '_idx');
    }

    protected function applyQueryModifications(): Eloquent\Builder
    {
        $callback = $this->modifyQueryCallback ?? static fn (Eloquent\Builder $query) => $query;

        return $callback($this->query());
    }

    protected function query(): Eloquent\Builder
    {
        return $this->model->newModelQuery();
    }
}
