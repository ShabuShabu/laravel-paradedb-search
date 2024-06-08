<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;

readonly class Boolean implements ParadeExpression
{
    public function __construct(
        private ?array $must = null,
        private ?array $should = null,
        private ?array $mustNot = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $must = $this->process($this->must, $grammar);
        $should = $this->process($this->should, $grammar);
        $mustNot = $this->process($this->mustNot, $grammar);

        return "paradedb.boolean(must => $must, should => $should, must_not => $mustNot)";
    }

    protected function process(?array $expressions, Grammar $grammar): string
    {
        if (! is_array($expressions) || count($expressions) <= 0) {
            return 'NULL::paradedb.searchqueryinput';
        }

        $conditions = collect($expressions)
            ->ensure(ParadeExpression::class)
            ->map(fn (ParadeExpression $condition) => $condition->getValue($grammar))
            ->join(', ');

        return "ARRAY[$conditions]";
    }
}
