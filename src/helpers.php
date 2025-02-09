<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB;

use Illuminate\Support\Str;
use JsonException;

/**
 * @throws JsonException
 */
function text_config(array $config): string
{
    return Str::wrap(json_encode($config, JSON_THROW_ON_ERROR), "'");
}
