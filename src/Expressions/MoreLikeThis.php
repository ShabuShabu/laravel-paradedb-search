<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use JsonException;
use RuntimeException;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class MoreLikeThis implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private int | array $idOrFields,
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
        $docField = is_int($this->idOrFields) ? 'document_id' : 'document_fields';

        if ($this->stopWords && ! array_is_list($this->stopWords)) {
            throw new RuntimeException('Stop words must be a list of strings');
        }

        $params = $this->toParams([
            $docField => match (true) {
                is_int($this->idOrFields) => $this->cast($grammar, $this->idOrFields),
                is_array($this->idOrFields) => $grammar->escape(json_encode($this->idOrFields, JSON_THROW_ON_ERROR)),
            },
            'min_doc_frequency' => $this->cast($grammar, $this->minDocFrequency),
            'max_doc_frequency' => $this->cast($grammar, $this->maxDocFrequency),
            'min_term_frequency' => $this->cast($grammar, $this->minTermFrequency),
            'max_query_terms' => $this->cast($grammar, $this->maxQueryTerms),
            'min_word_length' => $this->cast($grammar, $this->minWordLength),
            'max_word_length' => $this->cast($grammar, $this->maxWordLength),
            'boost_factor' => $this->cast($grammar, $this->boostFactor),
            'stop_words' => is_array($this->stopWords) ? $this->asArray($grammar, $this->stopWords) : null,
        ]);

        return "paradedb.more_like_this($params)";
    }
}
