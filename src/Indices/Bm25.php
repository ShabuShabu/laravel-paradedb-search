<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Indices;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use JsonException;
use stdClass;

/**
 * @method static addNumericFields(array $config)
 * @method static addTextFields(array $config)
 * @method static addJsonFields(array $config)
 * @method static addBooleanFields(array $config)
 * @method static addDateFields(array $config)
 */
class Bm25
{
    protected array $fields = [
        'text' => [],
        'numeric' => [],
        'boolean' => [],
        'json' => [],
        'date' => [],
    ];

    final protected function __construct(
        protected string $table,
        protected string $suffix,
        protected string $schema,
        protected string $id,
    ) {
    }

    public static function index(string $table, string $schema = 'public', string $id = 'id'): static
    {
        $suffix = config('paradedb-search.table_suffix', '_idx');

        return new static($table, $suffix, $schema, $id);
    }

    protected function addFields(string $name, array $config): static
    {
        if (! array_key_exists($name, $this->fields)) {
            throw new InvalidArgumentException("Field '$name' does not exist");
        }

        $this->fields[$name] = $config;

        return $this;
    }

    /**
     * @throws JsonException
     */
    protected function encodeConfig(array $config): string
    {
        return collect($config)->mapWithKeys(
            fn (mixed $value, int|string $key) => is_int($key)
                ? [$value => new stdClass()]
                : [$key => $value]
        )->pipe(
            fn ($encoded) => json_encode($encoded, JSON_THROW_ON_ERROR)
        );
    }

    public function create(bool $drop = false): void
    {
        if ($drop) {
            $this->drop();
        }

        $fields = collect($this->fields)->map(
            fn (array $config) => blank($config) ? '{}' : $this->encodeConfig($config),
        );

        DB::statement(
            <<<QUERY
            CALL paradedb.create_bm25(
                index_name => '$this->table$this->suffix',
                schema_name => '$this->schema',
                table_name => '$this->table',
                key_field => '$this->id',
                text_fields => '{$fields->get('text')}',
                numeric_fields => '{$fields->get('numeric')}',
                boolean_fields => '{$fields->get('boolean')}',
                json_fields => '{$fields->get('json')}',
                datetime_fields => '{$fields->get('date')}'
            );
            QUERY
        );
    }

    public function drop(): void
    {
        DB::statement(
            <<<QUERY
            CALL paradedb.drop_bm25(
                index_name => '$this->table$this->suffix',
                schema_name => '$this->schema'
            );
            QUERY
        );
    }

    public function __call(string $method, array $arguments): static
    {
        preg_match('/add(.*)Fields/', $method, $matches);

        return $this->addFields(strtolower($matches[1]), $arguments[0]);
    }
}
