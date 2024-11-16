<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\MoreLikeThis;

it('finds similar documents by id')
    ->expect(new MoreLikeThis(3))
    ->toBeExpression('paradedb.more_like_this(document_id => 3)');

it('finds similar documents by id with options')
    ->expect(new MoreLikeThis(
        idOrFields: 3,
        minDocFrequency: 1,
        maxDocFrequency: 2,
        minTermFrequency: 3,
        maxQueryTerms: 4,
        minWordLength: 5,
        maxWordLength: 6,
        boostFactor: 1.5,
        stopWords: ['and', 'or'],
    ))
    ->toBeExpression("paradedb.more_like_this(document_id => 3, min_doc_frequency => 1, max_doc_frequency => 2, min_term_frequency => 3, max_query_terms => 4, min_word_length => 5, max_word_length => 6, boost_factor => 1.5, stop_words => '[\"and\",\"or\"]'::json)");

it('finds similar documents by fields')
    ->expect(new MoreLikeThis(['description' => 'shoes']))
    ->toBeExpression("paradedb.more_like_this(document_fields => '{\"description\":\"shoes\"}')");
