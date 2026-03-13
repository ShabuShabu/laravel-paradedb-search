<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Literal;

it('generates the correct literal tokenizer')
    ->expect(new Literal('description'))
    ->toBeExpression('description::pdb.literal');

it('panics for a token filter', function () {
    (new Literal('description'))->asciiFolding();
})->throws(InvalidArgumentException::class, 'Token filters are not allowed for this tokenizer');
