<p align="center"><img src="laravel-paradedb-search.png" alt="ParadeDB Search for Laravel"></p>

# ParadeDB Search for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shabushabu/laravel-paradedb-search.svg?style=flat-square)](https://packagist.org/packages/shabushabu/laravel-paradedb-search)
[![Total Downloads](https://img.shields.io/packagist/dt/shabushabu/laravel-paradedb-search.svg?style=flat-square)](https://packagist.org/packages/shabushabu/laravel-paradedb-search)

Integrates the `pg_search` Postgres extension by [ParadeDB](https://docs.paradedb.com/search/quickstart) into [Laravel](https://laravel.com)

## Supported minimum versions

| PHP | Laravel | PostgreSQL | pg_search |
|-----|---------|------------|-----------|
| 8.4 | 12.0    | 17.0       | 0.25.0    |

## Installation

> [!CAUTION]
> Please note that this is a fairly new package and, even though it is well tested, it should be considered pre-release software

Before installing this package, you should install and enable the [pg_search](https://github.com/paradedb/paradedb/tree/dev/pg_search) extension.

You can then install the package via composer:

```bash
composer require shabushabu/laravel-paradedb-search
```

You can also publish the config file:

```bash
php artisan vendor:publish --tag="paradedb-search-config"
```

These are the contents of the published config file:

```php
return [
    'index_suffix' => env('PG_SEARCH_INDEX_SUFFIX', 'idx'),
    'highlighting_tag' => env('PG_SEARCH_HIGHLIGHTING_TAG', '<b></b>'),
    'remove_scopes' => [
        'fallback' => null,
    ],
];
```

## Usage

This is the documentation for the upcoming v1.0 release (develop branch). The documentation for the v1 API can be found [here](V1.md). Please note that the v2 API should be used **wherever possible**!

It is recommended to first familiarize yourself with the [extension docs](https://docs.paradedb.com/welcome/introduction) as most of the examples found there translate directly to the expressions used in this package!

### Operators

The operators supported by `pg_search` are already registered for you automatically. Additionally, we also register all supported `pgvector` operators.

Please see the following enums

- `\ShabuShabu\ParadeDB\Operators\FullText`
- `\ShabuShabu\ParadeDB\Operators\Distance`

### Creating an index

There are currently 3 ways to create a `bm25` index in a migration:

#### Using an array

This is the most basic way to create an index.

```php
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\UnicodeWords;

$table->bm25(
    columns: [
        'id',
        new UnicodeWords('name')->removeEmojis(),
        'description',
    ],
);
```

#### Using a closure

When using a closure, you can create your index using a Laravel-like syntax.

```php
use ShabuShabu\ParadeDB\Schema\Index;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\UnicodeWords;

$table->bm25(function (Index $index) {
    $index->id();
    $index->unicodeWords('name')->removeEmojis();
    $index->column('description');
});
```

#### Using a `Schema` class

Schemas allow you to have all your `bm25` adjustments in a single place. Columns will be built from all the existing versions up until the version specified.

```php
namespace Database\Schemas;

use ShabuShabu\ParadeDB\Schema\Index;
use ShabuShabu\ParadeDB\Schema\Schema;

class PostSchema extends Schema
{
    protected function v1(Index $index): void
    {
        $index->id();
        $index->unicodeWords('name');
        $index->column('description');
        $index->column('created_at');
    }
    
    protected function v2(Index $index): void
    {
        $index->unicodeWords('name')->removeEmojis();
        $index->remove('created_at');
    }
    
    protected function v3(Index $index): void
    {
        $index->literal('tags');
    }
}
```

```php
use Database\Schemas\PostSchema;

$table->bm25(
    PostSchema::v(3),
);
```

This will be equivalent to:

```php
$table->bm25(function (Index $index) {
    $index->id();
    $index->unicodeWords('name')->removeEmojis();
    $index->column('description');
    $index->literal('tags');
});
```

Which in turn is equivalent to this:

```php
$table->bm25([
    'id',
    new UnicodeWords('name')->removeEmojis(),
    'description',
    new Literal('tags'),
]);
```

#### Parameters & tokenizers

If the `key_field` is anything other than `id`, then you can specify it in the second argument together with any other parameters you might need:

```php
$table->bm25(
    columns: ['id', 'description'],
    parameters: ['key_field' => 'uuid'],
);
```

The following tokenizers are currently supported under this namespace: `\ShabuShabu\ParadeDB\Expressions\v2\Tokenizers`.

- `ChineseCompatible`
- `ICU`
- `Jieba`
- `Lindera`
- `Literal`
- `LiteralNormalized`
- `Ngram`
- `RegexPattern`
- `Simple`
- `SourceCode`
- `UnicodeWords`
- `Whitespace`

Please refer to the [pg_search docs](https://docs.paradedb.com/documentation/tokenizers/overview) for more information.

#### Composite types

If you have more than 32 columns to index, then you will need to create a composite type in a migration:

```php
Schema::createCompositeType('item_fields', [
    new Literal('name'),
    'description text',
    new Type('category', 'text'),
]);

// or

Schema::createCompositeType('item_fields', function (Composite $type) {
    $type->literal('name');
    $type->column('description', 'text');
    $type->column('category', 'text');
});
```

You can use this type in your index like this:

```php
use ShabuShabu\ParadeDB\Expressions\v2\Casts\Row;

$table->bm25(
    columns: [
        new Row('item_fields', ['name', 'description', 'category'])
    ]   
);

// or

$table->bm25(function (Index $index) {
    $index->row('item_fields', ['name', 'description', 'category']);
});
```

### Starting your search

It is recommended to always start your search using the available `search` macro like so:

```php
Product::search()
    ->where('description', '@@@', 'shoes')
    ->get();
```

This macro will automatically remove any global scopes that are registered in the config file as most of these will prevent the `bm25` index from being used.

This macro might also be used for other purposes in the future.

### Advanced query functions

See the relevant documentation for the [pg_search docs](https://docs.paradedb.com/documentation/query-builder/overview).

#### All

```php
use ShabuShabu\ParadeDB\Expressions\v2\All;

Product::search()
    ->where('id', '@@@', new All())
    ->get();
```

#### More like this

```php
use ShabuShabu\ParadeDB\Expressions\v2\MoreLikeThis;

Product::search()
    ->where('id', '@@@', new MoreLikeThis(3))
    ->get();
```

#### Phrase prefix

```php
use ShabuShabu\ParadeDB\Expressions\v2\PhrasePrefix;

Product::search()
    ->where('description', '@@@', new PhrasePrefix(['running', 'sh']))
    ->get();
```

#### Query parser

```php
use ShabuShabu\ParadeDB\Expressions\v2\Parse;

Product::search()
    ->where('description', '@@@', new Parse('description:(sleek shoes) AND rating:>3'))
    ->get();
```

The `Parse` expression also accepts a TantivyQL query builder (see below).

#### Range term

```php
use ShabuShabu\ParadeDB\Expressions\v2\RangeTerm;

Product::search()
    ->where('weight_range', '@@@', new RangeTerm(1))
    ->get();
```

The `RangeTerm` expression also accepts a `ShabuShabu\ParadeDB\Expressions\Ranges\RangeExpression`.

#### Regex

```php
use ShabuShabu\ParadeDB\Expressions\v2\Regex;

Product::search()
    ->where('description', '@@@', new Regex('key.*'))
    ->get();
```

#### Regex phrase

```php
use ShabuShabu\ParadeDB\Expressions\v2\RegexPhrase;

Product::search()
    ->where('description', '@@@', new RegexPhrase(['ru.*', 'shoes']))
    ->get();
```

### Aggregates

Various aggregates can be retrieved using the `bm25` index, like counts. Please see the [pg_search docs](https://docs.paradedb.com/documentation/aggregates/overview) for more information.

```php
use ShabuShabu\ParadeDB\Expressions\v2\All;
use ShabuShabu\ParadeDB\Expressions\v2\Agg;

Product::search()
    ->select(new Agg([
        'value_count' => ['field' => 'id'],
    ]))
    ->where('id', '@@@', new All)
    ->agg();
```

The `agg` macro will return a collection of results, with the aggregate values already decoded.

By default, only the `agg` key will be decoded. If you requested multiple aggregates or aliased the column to something else, then you can specify the keys like so:

```php
use ShabuShabu\ParadeDB\Expressions\v2\All;
use ShabuShabu\ParadeDB\Expressions\v2\Agg;
use Tpetry\QueryExpressions\Language\Alias;

Product::search()
    ->select([
        new Alias(new Agg(...), 'first'),
        new Alias(new Agg(...), 'second')
    ])
    ->where('id', '@@@', new All)
    ->agg(['first', 'second']);
```

### TantivyQL

ParadeDB Search for Laravel comes with a fluent builder for TantivyQL, a simple string-based query language.

This builder can be used within various v1 ParadeDB as well as the v2 `Parse` expressions.

### Basic query

```php
use ShabuShabu\ParadeDB\TantivyQL\Query;

Query::string()->where('description', 'keyboard')->get();

// results in: description:keyboard
```

### Add an IN condition

```php
Query::string()
    ->where('description', ['keyboard', 'toy'])
    ->get();

// results in: description:IN [keyboard, toy]
```

### Add an AND NOT condition

```php
(string) Query::string()
    ->where('category', 'electronics')
    ->whereNot('description', 'keyboard');

// results in: category:electronics AND NOT description:keyboard
```

### Boost a condition

```php
Query::string()
    ->where('description', 'keyboard', boost: 1)
    ->get();

// results in: description:keyboard^1
```

### Apply the slop operator

```php
Query::string()
    ->where('description', 'ergonomic keyboard', slop: 1)
    ->get();

// results in: description:"ergonomic keyboard"~1
```

### More complex example with a sub condition

```php
Query::string()
    ->where('description', ['keyboard', 'toy'])
    ->where(
        fn (Builder $builder) => $builder
            ->where('category', 'electronics')
            ->orWhere('tag', 'office')
    )
    ->get();

// results in: description:IN [keyboard, toy] AND (category:electronics OR tag:office)
```

### Apply a simple filter

```php
use ShabuShabu\ParadeDB\TantivyQL\Operators\Filter;

Query::string()
    ->whereFilter('rating', Filter::equals, 4)
    ->get();

// results in: rating:4
```

### Apply a boolean filter

```php
Query::string()
    ->whereFilter('is_available', '=', false)
    ->get();

// results in: is_available:false
```

### Apply a basic range filter

```php
Query::string()
    ->whereFilter('rating', '>', 4)
    ->get();

// results in: rating:>4
```

### Apply an inclusive range filter

```php
use ShabuShabu\ParadeDB\TantivyQL\Operators\Range;

Query::string()
    ->whereFilter('rating', Range::includeAll, [2, 5])
    ->get();

// results in: rating:[2 TO 5]
```

### Apply an exclusive range filter

```php
use ShabuShabu\ParadeDB\TantivyQL\Operators\Range;

Query::string()
    ->whereFilter('rating', Range::excludeAll, [2, 5])
    ->get();

// results in: rating:{2 TO 5}
```

### A word of caution

While it is possible to combine ParadeDB queries with regular Eloquent queries, you will incur some performance penalties.

For optimal performance it is recommended to let the `bm25` index do as much work as possible!

## Commands

This package comes with various Artisan commands to help you manage your `pg_search` instance.

### Index integrity

Allows you to verify a single or all indexes, as well as list your indexes and segments.

```bash
php artisan paradedb:integrity
```

### Test table

Either create or drop the built-in test table:

```bash
php artisan paradedb:test-table
```

### Tokenizers

List all available tokenizers:

```bash
php artisan paradedb:tokenizers
```

### Version info

List some versioning info:

```bash
php artisan paradedb:version
```

## Testing

The tests require a PostgreSQL database, which can easily be set up by running the following script:

```bash
composer testdb
```

> [!WARNING]
> Please note that both [pg_search](https://github.com/paradedb/paradedb/tree/dev/pg_search#installation) and [pgvector](https://github.com/pgvector/pgvector#installation) extensions need to be available already.

Then run the tests:

```bash
composer test
```

Or with test coverage:

```bash
composer test-coverage
```

Or with type coverage:

```bash
composer type-coverage
```

Or run PHPStan: 

```bash
composer analyse
```

### ParadeDB test table

There is also a command that allows you to create and drop the built-in test table

```bash
php artisan paradedb:test-table create
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Taylor Otwell](https://github.com/taylorotwell) for creating Laravel
- [ParadeDB](https://github.com/paradedb) for creating `pg_search`
- [ShabuShabu](https://github.com/ShabuShabu)
- [All Contributors](../../contributors)

## Disclaimer

This is a 3rd party package and ShabuShabu is not affiliated with either Laravel or ParadeDB.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
