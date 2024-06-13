<?php

declare(strict_types=1);

use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Assert;
use ShabuShabu\ParadeDB\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('toBeSchema', function () {
    $count = DB::table('information_schema.schemata')
        ->where('schema_name', $this->value)
        ->count();

    Assert::assertTrue($count > 0);

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
