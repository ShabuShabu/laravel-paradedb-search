<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Boolean implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private null|string|array|ParadeExpression|Builder $must = null,
        private null|string|array|ParadeExpression|Builder $should = null,
        private null|string|array|ParadeExpression|Builder $mustNot = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $must = $this->process($grammar, $this->must);
        $should = $this->process($grammar, $this->should);
        $mustNot = $this->process($grammar, $this->mustNot);

        return "paradedb.boolean(must => $must, should => $should, must_not => $mustNot)";
    }

    protected function process(Grammar $grammar, null|string|array|ParadeExpression|Builder $expressions): string
    {
        return is_null($expressions)
            ? 'NULL::paradedb.searchqueryinput'
            : $this->wrapArray(
                $this->normalizeQueries($grammar, $expressions)
            );
    }
}
