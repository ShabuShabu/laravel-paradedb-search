<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Schema;

use Illuminate\Contracts\Database\Query\Expression;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\ChineseCompatible;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Dictionary;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\ICU;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Jieba;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Lindera;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Literal;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\LiteralNormalized;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Ngram;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\RegexPattern;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Simple;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\SourceCode;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\UnicodeWords;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Whitespace;

trait HasTokenizers
{
    protected array $columns = [];

    public function chineseCompatible(string $name, null | string | Expression $column = null): ChineseCompatible
    {
        return $this->columns[$name] = new ChineseCompatible($column ?? $name);
    }

    public function icu(string $name, null | string | Expression $column = null): ICU
    {
        return $this->columns[$name] = new ICU($column ?? $name);
    }

    public function jieba(string $name, null | string | Expression $column = null): Jieba
    {
        return $this->columns[$name] = new Jieba($column ?? $name);
    }

    public function lindera(string $name, Dictionary $dictionary, null | string | Expression $column = null): Lindera
    {
        return $this->columns[$name] = new Lindera($column ?? $name, $dictionary);
    }

    public function literal(string $name, null | string | Expression $column = null): Literal
    {
        return $this->columns[$name] = new Literal($column ?? $name);
    }

    public function literalNormalized(string $name, null | string | Expression $column = null): LiteralNormalized
    {
        return $this->columns[$name] = new LiteralNormalized($column ?? $name);
    }

    public function ngram(string $name, int $min, int $max, null | string | Expression $column = null): Ngram
    {
        return $this->columns[$name] = new Ngram($column ?? $name, $min, $max);
    }

    public function regexPattern(string $name, string $pattern, null | string | Expression $column = null): RegexPattern
    {
        return $this->columns[$name] = new RegexPattern($column ?? $name, $pattern);
    }

    public function simple(string $name, null | string | Expression $column = null): Simple
    {
        return $this->columns[$name] = new Simple($column ?? $name);
    }

    public function sourceCode(string $name, null | string | Expression $column = null): SourceCode
    {
        return $this->columns[$name] = new SourceCode($column ?? $name);
    }

    public function unicodeWords(string $name, null | string | Expression $column = null): UnicodeWords
    {
        return $this->columns[$name] = new UnicodeWords($column ?? $name);
    }

    public function whitespace(string $name, null | string | Expression $column = null): Whitespace
    {
        return $this->columns[$name] = new Whitespace($column ?? $name);
    }

    public function columns(): array
    {
        return array_values(array_filter($this->rawColumns()));
    }

    public function rawColumns(): array
    {
        return $this->columns;
    }
}
