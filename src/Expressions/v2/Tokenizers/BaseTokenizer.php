<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

use InvalidArgumentException;
use Illuminate\Database\Grammar;
use Illuminate\Contracts\Database\Query\Expression;

abstract class BaseTokenizer implements Tokenizer
{
    protected bool $allowTokenFilters = true;

    protected array $parameters = [];

    protected array $tokenFilters = [];

    public function __construct(
        protected string|Expression $column,
    ) {}

    public function alphaNumOnly(bool $value = true): static
    {
        $this->assertFilters();

        $this->tokenFilters[] = $value ? "'alpha_num_only=true'" : "'alpha_num_only=false'";

        return $this;
    }

    public function asciiFolding(bool $value = true): static
    {
        $this->assertFilters();

        $this->tokenFilters[] = $value ? "'ascii_folding=true'" : "'ascii_folding=false'";

        return $this;
    }

    public function lowercase(bool $value = true): static
    {
        $this->assertFilters();

        $this->tokenFilters[] = $value ? "'lowercase=true'" : "'lowercase=false'";

        return $this;
    }

    public function trim(bool $value = true): static
    {
        $this->assertFilters();

        $this->tokenFilters[] = $value ? "'trim=true'" : "'trim=false'";

        return $this;
    }

    public function removeLong(int $value): static
    {
        $this->assertFilters();

        $this->tokenFilters[] = "'remove_long=$value'";

        return $this;
    }

    public function removeShort(int $value): static
    {
        $this->assertFilters();

        $this->tokenFilters[] = "'remove_short=$value'";

        return $this;
    }

    public function stopwordsLanguage(array|Stopwords $value): static
    {
        $this->assertFilters();

        if (is_array($value)) {
            $languages = implode(',', array_unique(array_map(
                static fn (string | Stopwords $enum) => $enum instanceof Stopwords ? $enum->name : $enum,
                $value
            )));
        } else {
            $languages = $value->name;
        }

        $this->tokenFilters[] = "'stopwords_language=$languages'";

        return $this;
    }

    public function stemmer(Stemmer $value): static
    {
        $this->assertFilters();

        $this->tokenFilters[] = "'stemmer=$value->name'";

        return $this;
    }

    public function getValue(Grammar $grammar): string
    {
        $name = $this->name();
        $column = $this->stringize($grammar, $this->column);

        $parameters = $this->combine($this->parameters);
        $filters = $this->combine($this->tokenFilters);

        return match(true) {
            $parameters && $filters => "$column::$name($parameters, $filters)",
            $parameters && !$filters => "$column::$name($parameters)",
            !$parameters && $filters => "$column::$name($filters)",
            default => "$column::$name",
        };
    }

    protected function assertFilters(): void
    {
        if (!$this->allowTokenFilters) {
            throw new InvalidArgumentException('Token filters are not allowed for this tokenizer');
        }
    }

    protected function stringize(Grammar $grammar, string | Expression $expression): string
    {
        return match ($grammar->isExpression($expression)) {
            true => $grammar->getValue($expression),
            false => $expression,
        };
    }

    protected function combine(array $args): ?string
    {
        return count($args) > 0 ? implode(', ', $args) : null;
    }
}