<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\MoreLikeThis;

pest()->group('v2', 'expressions');

it('finds similar documents by integer')
    ->expect(new MoreLikeThis(3))
    ->toBeExpression('pdb.more_like_this(3)');

it('finds similar documents by integer restricted to certain fields')
    ->expect(new MoreLikeThis(3, ['description']))
    ->toBeExpression("pdb.more_like_this(3, fields => ARRAY['description'])");

it('finds similar documents by string')
    ->expect(new MoreLikeThis('019d13dd-6962-71a5-aea5-b4a3877ad543'))
    ->toBeExpression("pdb.more_like_this('019d13dd-6962-71a5-aea5-b4a3877ad543')");

it('finds similar documents by custom document')
    ->expect(new MoreLikeThis([
        'description' => 'Sleek running shoes',
        'category' => 'footwear'
    ]))
    ->toBeExpression('pdb.more_like_this(\'{"description":"Sleek running shoes","category":"footwear"}\')');

it('finds similar documents with options')
    ->expect(new MoreLikeThis(
        document: 3,
        minDocFrequency: 1,
        maxDocFrequency: 3,
        minTermFrequency: 2,
        maxQueryTerms: 10,
        minWordLength: 5,
        maxWordLength: 7,
        boostFactor: 2,
        stopWords: ['the', 'a']
    ))
    ->toBeExpression("pdb.more_like_this(3, min_doc_frequency => 1, max_doc_frequency => 3, min_term_frequency => 2, max_query_terms => 10, min_word_length => 5, max_word_length => 7, boost_factor => 2, stop_words => ARRAY['the', 'a'])");

it('panics for present fields for a custom document', function () {
    (new MoreLikeThis(['description' => 'Sleek running shoes', 'category' => 'footwear'], ['description']))->getValue(grammar());
})->throws(InvalidArgumentException::class, 'You can only pass a list of fields when document is a string or an integer.');