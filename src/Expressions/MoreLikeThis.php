<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use Illuminate\Support\Str;
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

        $docValue = match (true) {
            is_int($this->idOrFields) => $this->asInt($this->idOrFields),
            is_array($this->idOrFields) => Str::wrap(
                $grammar->escape(json_encode($this->idOrFields, JSON_THROW_ON_ERROR)),
                "'"
            ),
        };

        $minDocFrequency = $this->asInt($this->minDocFrequency);
        $maxDocFrequency = $this->asInt($this->maxDocFrequency);
        $minTermFrequency = $this->asInt($this->minTermFrequency);
        $maxQueryTerms = $this->asInt($this->maxQueryTerms);
        $minWordLength = $this->asInt($this->minWordLength);
        $maxWordLength = $this->asInt($this->maxWordLength);
        $boostFactor = $this->asReal($this->boostFactor);

        if ($this->stopWords && ! array_is_list($this->stopWords)) {
            throw new RuntimeException('Stop words must be a list of strings');
        }

        $stopWords = $this->stopWords === null
            ? 'NULL::text[]'
            : collect($this->stopWords)
                ->filter(fn (mixed $word) => is_string($word))
                ->map(fn (string $word) => $grammar->escape($word))
                ->values()
                ->all();

        return "paradedb.more_like_this($docField => $docValue, min_doc_frequency => $minDocFrequency, max_doc_frequency => $maxDocFrequency, min_term_frequency => $minTermFrequency, max_query_terms => $maxQueryTerms, min_word_length => $minWordLength, max_word_length => $maxWordLength, boost_factor => $boostFactor, stop_words => $stopWords)";
    }
}
