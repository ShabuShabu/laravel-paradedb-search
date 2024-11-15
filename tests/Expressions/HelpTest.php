<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Help;

it('asks for help')
    ->expect(new Help('Need help!', 'Not exactly sure what the problem is, tho...'))
    ->toBeExpression("paradedb.help(subject => 'Need help!', body => 'Not exactly sure what the problem is, tho...')");
