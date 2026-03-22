<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Snippets;

pest()->group('v2', 'expressions');

it('gets the snippets')
    ->expect(new Snippets('description'))
    ->toBeExpression('pdb.snippets(field => "description")');

it('gets the snippets with options')
    ->expect(new Snippets(
        field: 'description',
        startTag: '<pre>',
        endTag: '</pre>',
        maxNumChars: 15,
        limit: 0,
        offset: 10,
        sortBy: 'position',
    ))
    ->toBeExpression("pdb.snippets(field => \"description\", start_tag => '<pre>', end_tag => '</pre>', max_num_chars => 15, limit => 0, offset => 10, sort_by => 'position')");
