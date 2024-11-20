<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Indices;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use JsonException;
use stdClass;

/**
 * @method static addTextFields(array $config)
 * @method static addNumericFields(array $config)
 * @method static addBooleanFields(array $config)
 * @method static addDateFields(array $config)
 * @method static addJsonFields(array $config)
 * @method static addRangeFields(array $config)
 */
class Bm25
{
    protected array $fields = [
        'text' => [],
        'numeric' => [],
        'boolean' => [],
        'date' => [],
        'json' => [],
        'range' => [],
    ];

    protected ?string $indexName = null;

    protected string $predicates = '';

    final protected function __construct(
        protected string $table,
        protected string $schema,
        protected string $id,
    ) {}

    public static function index(string $table, string $schema = 'public', string $id = 'id'): static
    {
        return new static($table, $schema, $id);
    }

    public function partialBy(string $predicates): static
    {
        $this->predicates = $predicates;

        return $this;
    }

    protected function addFields(string $name, array $config): static
    {
        if (! array_key_exists($name, $this->fields)) {
            throw new InvalidArgumentException("Field `$name` does not exist");
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
            fn (mixed $value, int | string $key) => is_int($key)
                ? [$value => new stdClass]
                : [$key => $value]
        )->pipe(
            fn (mixed $encoded) => json_encode($encoded, JSON_THROW_ON_ERROR)
        );
    }

    public function name(string $name): static
    {
        $this->indexName = $name;

        return $this;
    }

    protected function indexName(): string
    {
        return $this->indexName ?? $this->table . config('paradedb-search.index_suffix', '_idx');
    }

    public function create(bool $drop = false): bool
    {
        if ($drop) {
            $this->drop();
        }

        $fields = collect($this->fields)->map(
            fn (array $config) => blank($config) ? '{}' : $this->encodeConfig($config),
        );

        return DB::statement(
            <<<'QUERY'
            CALL paradedb.create_bm25(
                index_name => :index,
                schema_name => :schema,
                table_name => :table,
                key_field => :key,
                text_fields => :text,
                numeric_fields => :numeric,
                boolean_fields => :boolean,
                datetime_fields => :date,
                json_fields => :json,
                range_fields => :range,
                predicates => :predicates
            );
            QUERY,
            [
                'index' => $this->indexName(),
                'schema' => $this->schema,
                'table' => $this->table,
                'key' => $this->id,
                'text' => $fields->get('text'),
                'numeric' => $fields->get('numeric'),
                'boolean' => $fields->get('boolean'),
                'date' => $fields->get('date'),
                'json' => $fields->get('json'),
                'range' => $fields->get('range'),
                'predicates' => $this->predicates,
            ]
        );
    }

    public function drop(): bool
    {
        return DB::statement(sprintf('drop index if exists %s;', $this->indexName()));
    }

    public function __call(string $method, array $arguments): static
    {
        preg_match('/add(.*)Fields/', $method, $matches);

        return $this->addFields(strtolower($matches[1]), $arguments[0]);
    }
}
