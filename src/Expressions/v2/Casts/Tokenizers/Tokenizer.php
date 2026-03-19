<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use InvalidArgumentException;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

abstract class Tokenizer implements TokenizerExpression
{
    use Stringable;

    protected bool $allowTokenFilters = true;

    protected array $parameters = [];

    protected array $config = [];

    protected string $namespace = 'pdb';

    public function __construct(
        protected string | Expression $column,
    ) {}

    public function alphaNumOnly(bool $value = true): static
    {
        $this->assertFilters();

        $this->config[] = $value ? "'alpha_num_only=true'" : "'alpha_num_only=false'";

        return $this;
    }

    public function asciiFolding(bool $value = true): static
    {
        $this->assertFilters();

        $this->config[] = $value ? "'ascii_folding=true'" : "'ascii_folding=false'";

        return $this;
    }

    public function lowercase(bool $value = true): static
    {
        $this->assertFilters();

        $this->config[] = $value ? "'lowercase=true'" : "'lowercase=false'";

        return $this;
    }

    public function trim(bool $value = true): static
    {
        $this->assertFilters();

        $this->config[] = $value ? "'trim=true'" : "'trim=false'";

        return $this;
    }

    public function removeLong(int $value): static
    {
        $this->assertFilters();

        $this->config[] = "'remove_long=$value'";

        return $this;
    }

    public function removeShort(int $value): static
    {
        $this->assertFilters();

        $this->config[] = "'remove_short=$value'";

        return $this;
    }

    public function stopwordsLanguage(array | Stopwords $value): static
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

        $this->config[] = "'stopwords_language=$languages'";

        return $this;
    }

    public function stemmer(Stemmer $value): static
    {
        $this->assertFilters();

        $this->config[] = "'stemmer=$value->name'";

        return $this;
    }

    public function alias(string $value): static
    {
        $this->config[] = "'alias=$value'";

        return $this;
    }

    public function getValue(Grammar $grammar): string
    {
        $name = $this->tokenizerName();
        $column = $this->stringize($grammar, $this->column);

        $parameters = $this->combine($this->parameters);
        $filters = $this->combine($this->config);

        return match (true) {
            $parameters && $filters => "$column::$name($parameters, $filters)",
            $parameters && ! $filters => "$column::$name($parameters)",
            ! $parameters && $filters => "$column::$name($filters)",
            default => "$column::$name",
        };
    }

    protected function tokenizerName(): string
    {
        if (str_contains($name = $this->name(), '.')) {
            return $name;
        }

        return "$this->namespace.$name";
    }

    protected function assertFilters(): void
    {
        if (! $this->allowTokenFilters) {
            throw new InvalidArgumentException('Token filters are not allowed for this tokenizer');
        }
    }

    protected function combine(array $args): ?string
    {
        return count($args) > 0 ? implode(', ', $args) : null;
    }
}
