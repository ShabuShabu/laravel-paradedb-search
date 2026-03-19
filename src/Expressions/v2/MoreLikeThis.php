<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use JsonException;
use InvalidArgumentException;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class MoreLikeThis implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private int|string|array $document,
        private ?array $fields = null,
        private ?int $minDocFrequency = null,
        private ?int $maxDocFrequency = null,
        private ?int $minTermFrequency = null,
        private ?int $maxQueryTerms = null,
        private ?int $minWordLength = null,
        private ?int $maxWordLength = null,
        private ?float $boostFactor = null,
        private ?array $stopWords = null,
    ) {}

    /**
     * @throws JsonException
     */
    public function getValue(Grammar $grammar): string
    {
        if (is_array($this->document) && is_array($this->fields)) {
            throw new InvalidArgumentException('You can only pass a list of fields when document is a string or an integer.');
        }

        $baseParams = [
            'min_doc_frequency' => $this->cast($grammar, $this->minDocFrequency),
            'max_doc_frequency' => $this->cast($grammar, $this->maxDocFrequency),
            'min_term_frequency' => $this->cast($grammar, $this->minTermFrequency),
            'max_query_terms' => $this->cast($grammar, $this->maxQueryTerms),
            'min_word_length' => $this->cast($grammar, $this->minWordLength),
            'max_word_length' => $this->cast($grammar, $this->maxWordLength),
            'boost_factor' => $this->cast($grammar, $this->boostFactor),
            'stop_words' => is_array($this->stopWords) ? $this->asArray($grammar, $this->stopWords) : null,
        ];

        if (is_array($this->document)) {
            $params = $this->toParams([
                'document' => $grammar->escape(json_encode($this->document, JSON_THROW_ON_ERROR)),
                ...$baseParams,
            ]);

            return "pdb.more_like_this($params)";
        }

        $keyValue = $this->cast($grammar, $this->document);

        $params = $this->toParams([
            'fields' => is_array($this->fields) ? $this->asArray($grammar, $this->fields) : null,
            ...$baseParams,
        ]);

        return "pdb.more_like_this($keyValue, $params)";
    }
}
