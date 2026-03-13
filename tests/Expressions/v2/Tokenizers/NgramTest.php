<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Ngram;

it('generates the correct ngram tokenizer')
    ->expect(new Ngram('description', 2, 3))
    ->toBeExpression('description::pdb.ngram(2, 3)');

it('generates the correct ngram tokenizer with filters')
    ->expect((new Ngram('description', 2, 3))->prefixOnly()->positions())
    ->toBeExpression("description::pdb.ngram(2, 3, 'prefix_only=true', 'positions=true')");