<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v1;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\v1\Concerns\Stringable;

final readonly class Help implements Expression
{
    use Stringable;

    public function __construct(
        private string $subject,
        private string $body,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'subject' => $this->toString($grammar, $this->subject),
            'body' => $this->toString($grammar, $this->body),
        ]);

        return "paradedb.help($params)";
    }
}
