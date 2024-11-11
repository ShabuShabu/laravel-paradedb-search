<?php

declare(strict_types=1);

use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Assert;
use ShabuShabu\ParadeDB\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('toBeIndex', function (string $table) {
    $result = DB::table(DB::raw('pg_class t, pg_class i, pg_index ix, pg_attribute a'))
        ->distinct()
        ->select('i.relname')
        ->where('t.relname', $table)
        ->where('t.relkind', 'r')
        ->whereColumn('a.attrelid', 't.oid')
        ->whereColumn('i.oid', 'ix.indexrelid')
        ->whereColumn('t.oid', 'ix.indrelid')
        ->where('a.attnum', DB::raw('ANY(ix.indkey)'))
        ->pluck('relname');

    Assert::assertContains($this->value, $result->all());

    return $this;
});

expect()->extend('toBeExpression', function (string $expected) {
    Assert::assertSame($expected, $this->value->getValue(grammar()));

    return $this;
});

function grammar(): Grammar
{
    return DB::connection()->getQueryGrammar();
}
