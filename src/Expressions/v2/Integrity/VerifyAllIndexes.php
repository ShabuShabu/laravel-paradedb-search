<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Integrity;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\v2\Integrity\Concerns\HasSampleRate;

final readonly class VerifyAllIndexes implements ParadeExpression
{
    use Stringable;
    use HasSampleRate;

    public function __construct(
        protected ?string $schemaPattern = null,
        protected ?string $indexPattern = null,
        protected ?bool $heapAllIndexed = null,
        protected ?float $sampleRate = null,
        protected ?bool $reportProgress = null,
        protected ?bool $onErrorStop = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $this->assertSampleRate();

        $params = $this->toParams([
            'schema_pattern' => $this->cast($grammar, $this->schemaPattern),
            'index_pattern' => $this->cast($grammar, $this->indexPattern),
            'heapallindexed' => $this->cast($grammar, $this->heapAllIndexed),
            'sample_rate' => $this->cast($grammar, $this->sampleRate),
            'report_progress' => $this->cast($grammar, $this->reportProgress),
            'on_error_stop' => $this->cast($grammar, $this->onErrorStop),
        ]);

        return "pdb.verify_all_indexes($params)";
    }
}
