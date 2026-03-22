<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ShabuShabu\ParadeDB\Expressions\v2\Integrity;

use function Laravel\Prompts\table;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class IndexIntegrity extends Command
{
    protected $signature = 'paradedb:integrity {action? : Either verify, verify-all, indexes or segments}';

    protected $description = 'Check the integrity of your bm25 indexes';

    public function __invoke(): int
    {
        $action = $this->argument('action') ?? $this->choice(
            'What action would you like to perform?',
            ['verify', 'verify-all', 'indexes', 'segments'],
            'verify',
        );

        return match ($action) {
            'verify' => $this->verify(),
            'verify-all' => $this->verifyAll(),
            'indexes' => $this->indexes(),
            'segments' => $this->segments(),
            default => self::INVALID,
        };
    }

    protected function verify(): int
    {
        info('You selected to verify an index.');

        if (! is_string($index = $this->selectIndex())) {
            return $index;
        }

        $heapAllIndexed = $this->heapAllIndexed();
        $sampleRate = $this->sampleRate();
        $reportProgress = $this->reportProgress();
        $onErrorStop = $this->onErrorStop();

        $verbose = confirm(
            label: 'Do you want to enable verbose logging?',
            default: false,
        );

        $segments = DB::table(new Integrity\IndexSegments($index))->pluck('segment_idx');

        $segmentIds = $segments->isNotEmpty()
            ? multiselect(
                label: 'Which segment ids would you like to verify?',
                options: $segments
            )
            : null;

        $checks = DB::table(new Integrity\VerifyIndex(
            index: $index,
            heapAllIndexed: $heapAllIndexed,
            sampleRate: $sampleRate,
            reportProgress: $reportProgress,
            verbose: $verbose === true ? true : null,
            onErrorStop: $onErrorStop,
            segmentIds: $segmentIds,
        ))->get();

        table(
            headers: ['Check name', 'Passed', 'Details'],
            rows: $checks->map(fn (object $check) => [
                $check->check_name,
                $check->passed,
                $check->details,
            ])->all(),
        );

        return self::SUCCESS;
    }

    protected function verifyAll(): int
    {
        info('You selected to verify all indexes.');

        $schemaPattern = text(
            label: 'Enter a schema name or pattern:',
            hint: 'You can use SQL LIKE syntax! Leave empty to verify all schemas.',
        );

        $indexPattern = text(
            label: 'Enter an index name or pattern:',
            hint: 'You can use SQL LIKE syntax! Leave empty to verify all indexes.',
        );

        $heapAllIndexed = $this->heapAllIndexed();
        $sampleRate = $this->sampleRate();
        $reportProgress = $this->reportProgress();
        $onErrorStop = $this->onErrorStop();

        $checks = DB::table(new Integrity\VerifyAllIndexes(
            schemaPattern: blank($schemaPattern) ? null : $schemaPattern,
            indexPattern: blank($indexPattern) ? null : $indexPattern,
            heapAllIndexed: $heapAllIndexed,
            sampleRate: $sampleRate,
            reportProgress: $reportProgress,
            onErrorStop: $onErrorStop,
        ))->get();

        table(
            headers: ['Schema name', 'Index name', 'Check name', 'Passed', 'Details'],
            rows: $checks->map(fn (object $check) => [
                $check->schemaname,
                $check->indexname,
                $check->check_name,
                $check->passed,
                $check->details,
            ])->all(),
        );

        return self::SUCCESS;
    }

    protected function indexes(): int
    {
        info('You selected to view the indexes.');

        $indexes = DB::table(new Integrity\Indexes)->get();

        table(
            headers: ['Schema name', 'Table name', 'Index name', 'Index rel id', 'Num segments', 'Total docs'],
            rows: $indexes->map(fn (object $index) => [
                $index->schemaname,
                $index->tablename,
                $index->indexname,
                $index->indexrelid,
                $index->num_segments,
                $index->total_docs,
            ])->all(),
        );

        return self::SUCCESS;
    }

    protected function segments(): int
    {
        info('You selected to view the segments of an index.');

        if (! is_string($index = $this->selectIndex())) {
            return $index;
        }

        $segments = DB::table(new Integrity\IndexSegments($index))->get();

        table(
            headers: ['Partition name', 'Segment idx', 'Segment id', 'Num docs', 'Num deleted', 'Max docs'],
            rows: $segments->map(fn (object $index) => [
                $index->partition_name,
                $index->segment_idx,
                $index->segment_id,
                $index->num_docs,
                $index->num_deleted,
                $index->max_doc,
            ])->all(),
        );

        return self::SUCCESS;
    }

    protected function selectIndex(): int | string
    {
        $indexes = DB::table(new Integrity\Indexes)->pluck('indexname');

        if ($indexes->isEmpty()) {
            warning('It looks like you have no indexes.');

            return self::INVALID;
        }

        if ($indexes->count() === 1) {
            return $indexes->first();
        }

        return select(
            label: 'Which index would you like to select?',
            options: $indexes,
        );
    }

    protected function onErrorStop(): ?true
    {
        $result = confirm(
            label: 'Do you want to stop verification immediately when the first error is found?',
            default: false,
        );

        return $result === true ? true : null;
    }

    protected function reportProgress(): ?true
    {
        $result = confirm(
            label: 'Do you want to enable progress reporting to see status updates?',
            default: false,
        );

        return $result === true ? true : null;
    }

    protected function sampleRate(): ?float
    {
        $result = text(
            label: 'Enter a sample rate or leave empty.',
            hint: 'Must be a float between 0.0 and 1.0.',
        );

        return is_numeric($result) ? (float) $result : null;
    }

    protected function heapAllIndexed(): ?true
    {
        $result = confirm(
            label: 'Do you want to verify that all indexed entries still exist in the heap table?',
            default: false,
        );

        return $result === true ? true : null;
    }
}
