<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestTable extends Command
{
    protected $signature = 'paradedb:test-table {action? : Either create or drop}
                                                {--table= : The table name}
                                                {--schema= : The schema name}';

    protected $description = 'Creates the bm25 test table';

    public function __invoke(): int
    {
        $action = $this->argument('action') ?? $this->choice(
            'What action would you like to perform?',
            ['create', 'drop'],
            'create',
        );

        $table = $this->option('table') ?? $this->ask(
            'What is the name of the test table?',
            'bm25_test_table'
        );

        $schema = $this->option('schema') ?? $this->ask(
            'What is the schema for the table?',
            'paradedb'
        );

        return match ($action) {
            'create' => $this->create($table, $schema),
            'drop' => $this->drop($table, $schema),
            default => $this->abort($action)
        };
    }

    protected function create(string $table, string $schema): int
    {
        if ($this->hasTable("$schema.$table")) {
            $this->components->error(
                "The table <options=bold>$schema.$table</> exists already..."
            );

            return self::INVALID;
        }

        $result = DB::statement(
            <<<'QUERY'
            CALL paradedb.create_bm25_test_table(
                table_name => :table,
                schema_name => :schema
            );
            QUERY,
            [
                'table' => $table,
                'schema' => $schema,
            ]
        );

        if (! $result) {
            $this->components->error(
                "An unidentified error occurred while trying to create <options=bold>$schema.$table</>"
            );

            return self::FAILURE;
        }

        $this->components->info(
            "<options=bold>$schema.$table</> has been successfully created",
        );

        return self::SUCCESS;
    }

    protected function drop(string $table, string $schema): int
    {
        if (! $this->hasTable("$schema.$table")) {
            $this->components->error(
                "The table <options=bold>$schema.$table</> does not exist..."
            );

            return self::INVALID;
        }

        $result = DB::statement(
            sprintf('drop table %s cascade', $this->grammar()->wrapTable($schema . '.' . $table))
        );

        if (! $result) {
            $this->components->error(
                "An unidentified error occurred while trying to drop <options=bold>$schema.$table</>"
            );

            return self::FAILURE;
        }

        $this->components->info(
            "<options=bold>$schema.$table</> has been successfully dropped",
        );

        return self::SUCCESS;
    }

    protected function abort(string $action): int
    {
        $this->components->error(
            "The action <options=bold>$action</> is not supported."
        );

        return self::FAILURE;
    }

    protected function hasTable(string $table): bool
    {
        return Schema::hasTable($table);
    }

    protected function grammar(): Grammar
    {
        return DB::connection()->getQueryGrammar();
    }
}
