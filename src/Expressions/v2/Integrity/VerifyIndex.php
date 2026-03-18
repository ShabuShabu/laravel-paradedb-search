<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Integrity;

use InvalidArgumentException;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\v2\Integrity\Concerns\HasSampleRate;

final readonly class VerifyIndex implements ParadeExpression
{
    use Stringable;
    use HasSampleRate;

    public function __construct(
        protected string $index,
        protected ?bool $heapAllIndexed = null,
        protected ?float $sampleRate = null,
        protected ?bool $reportProgress = null,
        protected ?bool $verbose = null,
        protected ?bool $onErrorStop = null,
        protected ?array $segmentIds = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $this->assertSampleRate();
        $this->assertSegmentIds();

        $params = $this->toParams([
            'index' => $grammar->wrap($this->index),
            'heapallindexed' => $this->cast($grammar, $this->heapAllIndexed),
            'sample_rate' => $this->cast($grammar, $this->sampleRate),
            'report_progress' => $this->cast($grammar, $this->reportProgress),
            'verbose' => $this->cast($grammar, $this->verbose),
            'on_error_stop' => $this->cast($grammar, $this->onErrorStop),
            'segment_ids' => $this->segmentIds
                ? $this->wrapArray(collect($this->segmentIds))
                : null,
        ]);

        return "pdb.verify_index($params)";
    }

    protected function assertSegmentIds(): void
    {
        if (is_null($this->segmentIds)) {
            return;
        }

        $segmentIdCount = count($this->segmentIds);

        if ($segmentIdCount <= 0 || $segmentIdCount !== count(array_filter($this->segmentIds, 'is_int'))) {
            throw new InvalidArgumentException('Segment ids must be an array of integers and contain at least one id');
        }
    }
}
