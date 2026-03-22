<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Literal;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Stemmer;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Stopwords;

pest()->group('v2', 'tokenizers');

it('generates the correct literal tokenizer')
    ->expect(new Literal('description'))
    ->toBeExpression('"description"::pdb.literal');

it('panics for a token filter', function (string $method, mixed $arg) {
    if ($arg) {
        (new Literal('description'))->$method($arg);
    } else {
        (new Literal('description'))->$method();
    }
})->with([
    ['alphaNumOnly', null],
    ['asciiFolding', null],
    ['lowercase', null],
    ['trim', null],
    ['removeLong', 2],
    ['removeShort', 2],
    ['stopwordsLanguage', Stopwords::danish],
    ['stemmer', Stemmer::danish],
])->throws(InvalidArgumentException::class, 'Token filters are not allowed for this tokenizer');
