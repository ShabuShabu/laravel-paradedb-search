<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ShabuShabu\ParadeDB\Expressions;

class Tokenizers extends Command
{
    protected $signature = 'paradedb:tokenizers';

    protected $description = 'Lists all available tokenizers';

    public function __invoke(): int
    {
        $tokenizers = DB::table(new Expressions\Tokenizers)
            ->get('tokenizer as name');

        $this->components->info('These tokenizers are available:');

        $this->table(
            ['Name'],
            $tokenizers->map(fn (object $row) => (array) $row)->toArray()
        );

        return self::SUCCESS;
    }
}
