<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Assert;
use ShabuShabu\ParadeDB\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('toBeExistingSchema', function () {
    $query = DB::table('information_schema.schemata')
        ->where('schema_name', $this->value);

    Assert::assertTrue($query->count() > 0);

    return $this;
});
