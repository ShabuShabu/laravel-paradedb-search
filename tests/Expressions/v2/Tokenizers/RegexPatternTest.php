<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\RegexPattern;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Stopwords;

pest()->group('v2', 'tokenizers');

it('generates the correct ngram tokenizer')
    ->expect(new RegexPattern('description', '(?i)\bh\w*'))
    ->toBeExpression("\"description\"::pdb.regex_pattern('(?i)\bh\w*')");

it('generates the correct ngram tokenizer with filters')
    ->expect((new RegexPattern('description', '(?i)\bh\w*'))->stopwordsLanguage(Stopwords::danish))
    ->toBeExpression("\"description\"::pdb.regex_pattern('(?i)\bh\w*', 'stopwords_language=danish')");
