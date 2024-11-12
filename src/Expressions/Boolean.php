<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Closure;
use Illuminate\Database\Grammar;
use Illuminate\Support\Arr;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\TantivyQL\Query;

class Boolean implements ParadeExpression
{
    use Stringable;

    final public function __construct(
        private null | string | array | ParadeExpression | Query $must = null,
        private null | string | array | ParadeExpression | Query $should = null,
        private null | string | array | ParadeExpression | Query $mustNot = null,
    ) {}

    public function must(Closure | string | array | ParadeExpression | Query $query, bool $when = true): static
    {
        return $this->addQuery('must', $query, $when);
    }

    public function should(Closure | string | array | ParadeExpression | Query $query, bool $when = true): static
    {
        return $this->addQuery('should', $query, $when);
    }

    public function mustNot(Closure | string | array | ParadeExpression | Query $query, bool $when = true): static
    {
        return $this->addQuery('mustNot', $query, $when);
    }

    protected function addQuery(string $type, Closure | string | array | ParadeExpression | Query $query, bool $when): static
    {
        if (! $when) {
            return $this;
        }

        if (! is_array($this->$type)) {
            $this->$type = array_filter(Arr::wrap($this->$type));
        }

        foreach (Arr::wrap(value($query)) as $condition) {
            $this->$type[] = $condition;
        }

        return $this;
    }

    public function getValue(Grammar $grammar): string
    {
        $must = $this->process($grammar, $this->must);
        $should = $this->process($grammar, $this->should);
        $mustNot = $this->process($grammar, $this->mustNot);

        return "paradedb.boolean(must => $must, should => $should, must_not => $mustNot)";
    }

    protected function process(Grammar $grammar, null | string | array | ParadeExpression | Query $expressions): string
    {
        return is_null($expressions)
            ? 'ARRAY[]::paradedb.searchqueryinput[]'
            : $this->wrapArray(
                $this->normalizeQueries($grammar, $expressions)
            );
    }

    public static function query(): static
    {
        return new static;
    }
}
