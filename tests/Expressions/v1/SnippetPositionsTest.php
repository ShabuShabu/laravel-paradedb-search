<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\SnippetPositions;

pest()->group('v1');

it('gets the snippet positions')
    ->expect(new SnippetPositions('description'))
    ->toBeExpression("paradedb.snippet_positions(\"description\")");
