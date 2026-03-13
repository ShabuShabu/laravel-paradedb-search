<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

use Illuminate\Contracts\Database\Query\Expression;

final class Ngram extends BaseTokenizer
{
    public function __construct(
        protected string|Expression $column,
        protected int $min,
        protected int $max,
    ) {
        parent::__construct($column);

        $this->parameters = [$this->min, $this->max];
    }

    public function prefixOnly(bool $value = true): self
    {
        $this->tokenFilters[] = $value ? "'prefix_only=true'" : "'prefix_only=false'";

        return $this;
    }

    public function positions(bool $value = true): self
    {
        $this->tokenFilters[] = $value ? "'positions=true'" : "'positions=false'";

        return $this;
    }

    public function name(): string
    {
        return 'pdb.ngram';
    }
}