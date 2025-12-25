<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\SnippetPositions;

it('gets the snippet positions')
    ->expect(new SnippetPositions('description'))
    ->toBeExpression("paradedb.snippet_positions(column => 'description')");
