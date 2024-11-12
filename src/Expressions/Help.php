<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Help implements Expression
{
    use Stringable;

    public function __construct(
        private string $subject,
        private string $body,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $subject = $this->toString($grammar, $this->subject);
        $body = $this->toString($grammar, $this->body);

        return "paradedb.help(subject => $subject, body => $body)";
    }
}
