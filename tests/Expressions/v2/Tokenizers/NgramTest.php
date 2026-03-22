<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Ngram;

pest()->group('v2', 'tokenizers');

it('generates the correct ngram tokenizer')
    ->expect(new Ngram('description', 2, 3))
    ->toBeExpression('"description"::pdb.ngram(2, 3)');

it('generates the correct ngram tokenizer with filters')
    ->expect((new Ngram('description', 2, 3))->prefixOnly()->positions())
    ->toBeExpression("\"description\"::pdb.ngram(2, 3, 'prefix_only=true', 'positions=true')");

it('generates the correct ngram tokenizer with false filters')
    ->expect((new Ngram('description', 2, 3))->prefixOnly(false)->positions(false))
    ->toBeExpression("\"description\"::pdb.ngram(2, 3, 'prefix_only=false', 'positions=false')");
