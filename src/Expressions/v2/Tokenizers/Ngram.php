<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

use Illuminate\Contracts\Database\Query\Expression;

final class Ngram extends Tokenizer
{
    public function __construct(
        string | Expression $column,
        int $min,
        int $max,
    ) {
        parent::__construct($column);

        $this->parameters = [$min, $max];
    }

    public function prefixOnly(bool $value = true): self
    {
        $this->config[] = $value ? "'prefix_only=true'" : "'prefix_only=false'";

        return $this;
    }

    public function positions(bool $value = true): self
    {
        $this->config[] = $value ? "'positions=true'" : "'positions=false'";

        return $this;
    }

    public function name(): string
    {
        return 'ngram';
    }
}
