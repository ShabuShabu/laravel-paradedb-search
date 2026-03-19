<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Integrity\Concerns;

use InvalidArgumentException;

trait HasSampleRate
{
    protected function assertSampleRate(): void
    {
        if ($this->sampleRate < 0.0 || $this->sampleRate > 1.0) {
            throw new InvalidArgumentException('Sample rate must be between 0.0 and 1.0');
        }
    }
}
