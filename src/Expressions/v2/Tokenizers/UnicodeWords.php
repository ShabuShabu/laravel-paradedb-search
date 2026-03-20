<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

final class UnicodeWords extends Tokenizer
{
    public function name(): string
    {
        return 'unicode_words';
    }

    public function removeEmojis(bool $value = true): self
    {
        $this->config[] = $value ? "'remove_emojis=true'" : "'remove_emojis=false'";

        return $this;
    }
}
