<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers\Jieba;

it('generates the correct jieba tokenizer')
    ->expect(new Jieba('description'))
    ->toBeExpression('"description"::pdb.jieba');

it('generates the correct jieba tokenizer with filters')
    ->expect((new Jieba('description'))->lowercase()->trim())
    ->toBeExpression("\"description\"::pdb.jieba('lowercase=true', 'trim=true')");