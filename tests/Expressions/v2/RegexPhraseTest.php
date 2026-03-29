<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);
use ShabuShabu\ParadeDB\Expressions\v2\RegexPhrase;

pest()->group('v2', 'expressions');

it('generates a correct regex phrase expression')
    ->expect(new RegexPhrase(['ru.*', 'shoes']))
    ->toBeExpression("pdb.regex_phrase(regexes => ARRAY['ru.*', 'shoes'])");

it('generates a correct regex phrase expression with options')
    ->expect(new RegexPhrase(['ru.*', 'shoes'], 1, 16384))
    ->toBeExpression("pdb.regex_phrase(regexes => ARRAY['ru.*', 'shoes'], slop => 1, max_expansions => 16384)");
