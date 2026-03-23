<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

interface TokenizerExpression extends ParadeExpression
{
    public function name(): string;

    public function useAsType(): static;
}
