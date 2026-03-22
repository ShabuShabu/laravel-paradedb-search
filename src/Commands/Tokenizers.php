<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class Tokenizers extends Command
{
    protected $signature = 'paradedb:tokenizers';

    protected $description = 'Lists all available tokenizers';

    public function __invoke(): int
    {
        $tokenizers = DB::table(new \ShabuShabu\ParadeDB\Expressions\v1\Tokenizers)
            ->get('tokenizer as name');

        info('These tokenizers are available:');

        table(
            headers: ['Name'],
            rows: $tokenizers
                ->map(fn (object $row) => (array) $row)
                ->all()
        );

        return self::SUCCESS;
    }
}
