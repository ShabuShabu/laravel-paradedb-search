<p align="center"><img src="laravel-paradedb-search.png" alt="ParadeDB Search for Laravel"></p>

# ParadeDB Search for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shabushabu/laravel-paradedb-search.svg?style=flat-square)](https://packagist.org/packages/shabushabu/laravel-paradedb-search)
[![Total Downloads](https://img.shields.io/packagist/dt/shabushabu/laravel-paradedb-search.svg?style=flat-square)](https://packagist.org/packages/shabushabu/laravel-paradedb-search)

Integrates the `pg_search` Postgres extension by [ParadeDB](https://docs.paradedb.com/search/quickstart) into [Laravel](https://laravel.com)

## Supported minimum versions

| PHP | Laravel | PostgreSQL | pg_search |
|-----|---------|------------|-----------|
| 8.2 | 11.0    | 16         | 0.12.0    |

## Installation

> [!CAUTION]
> Please note that this is a new package and, even though it is well tested, it should be considered pre-release software

Before installing the package you should install and enable the [pg_search](https://github.com/paradedb/paradedb/tree/dev/pg_search) extension.

You can then install the package via composer:

```bash
composer require shabushabu/laravel-paradedb-search
```

You can also publish the config file:

```bash
php artisan vendor:publish --tag="laravel-paradedb-search-config"
```

These are the contents of the published config file:

```php
return [
    'index_suffix' => env('PG_SEARCH_INDEX_SUFFIX', '_idx'),
];
```

## Usage

### Add a bm25 index

Each model that you want to be searchable needs a corresponding `bm25` index. These can be generated within a migration like so:

```php
use ShabuShabu\ParadeDB\Indices\Bm25;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', static function (Blueprint $table) {
            // all your product fields
        });

        Bm25::index('products')
            ->addNumericFields(['amount'])
            ->addBooleanFields(['is_available'])
            ->addDateFields(['created_at', 'deleted_at'])
            ->addJsonFields(['options'])
            ->addRangeFields(['size'])
            ->addTextFields([
                'name',
                'currency',
                'description' => [
                    'tokenizer' => [
                        'type' => 'default',
                    ],
                ],
            ])
            ->create(drop: true);
    }
    
    public function down(): void
    {
        Bm25::index('products')->drop();
    }
};
```

### Add a partial bm25 index

```php
Bm25::index('teams')
    ->partialBy('max_members > 2')
    // ...
    ->create();
```

### TantivyQL

ParadeDB Search for Laravel comes with a fluent builder for TantivyQL, a simple string-based query language.

This builder can be passed as a condition to a search `where` method or used within the various ParadeDB expressions.

#### Basic query

```php
use ShabuShabu\ParadeDB\TantivyQL\Query;

Query::string()->where('description', 'keyboard')->get();

// results in: description:keyboard
```

#### Add an IN condition

```php
Query::string()
    ->where('description', ['keyboard', 'toy'])
    ->get();

// results in: description:IN [keyboard, toy]
```

#### Add an AND NOT condition

```php
(string) Query::string()
    ->where('category', 'electronics')
    ->whereNot('description', 'keyboard');

// results in: category:electronics AND NOT description:keyboard
```

#### Boost a condition

```php
Query::string()
    ->where('description', 'keyboard', boost: 1)
    ->get();

// results in: description:keyboard^1
```

#### Apply the slop operator

```php
Query::string()
    ->where('description', 'ergonomic keyboard', slop: 1)
    ->get();

// results in: description:"ergonomic keyboard"~1
```

#### More complex example with a sub condition

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

#### Apply a simple filter

```php
use ShabuShabu\ParadeDB\TantivyQL\Operators\Filter;

Query::string()
    ->whereFilter('rating', Filter::equals, 4)
    ->get();

// results in: rating:4
```

#### Apply a boolean filter

```php
use ShabuShabu\ParadeDB\TantivyQL\Operators\Filter;

Query::string()
    ->whereFilter('is_available', '=', false)
    ->get();

// results in: is_available:false
```

#### Apply a basic range filter

```php
use ShabuShabu\ParadeDB\TantivyQL\Operators\Filter;

Query::string()
    ->whereFilter('rating', '>', 4)
    ->get();

// results in: rating:>4
```

#### Apply an inclusive range filter

```php
use ShabuShabu\ParadeDB\TantivyQL\Operators\Range;

Query::string()
    ->whereFilter('rating', Range::includeAll, [2, 5])
    ->get();

// results in: rating:[2 TO 5]
```

#### Apply an exclusive range filter

```php
use ShabuShabu\ParadeDB\TantivyQL\Operators\Range;

Query::string()
    ->whereFilter('rating', Range::excludeAll, [2, 5])
    ->get();

// results in: rating:{2 TO 5}
```

### Performing a basic search

To search, you just use the custom `@@@` operator in a regular Eloquent query.

```php
Product::query()
    ->where('description', '@@@', 'shoes')
    ->get();
```

See: https://docs.paradedb.com/documentation/full-text/overview

### ParadeDB functions

For more complex operations, it might be necessary to use some of the provided [ParadeDB functions](https://docs.paradedb.com/search/full-text/complex), all of which have corresponding query expressions:

#### JSON

The right side of the `@@@` operator also accepts JSON query objects, similar to how Elasticsearch Query DSL works.

```php
use ShabuShabu\ParadeDB\Expressions\JsonB;

Product::query()
    ->where('id', '@@@', new JsonB([
        'fuzzy_term' => [
            'field' => 'description',
            'value' => 'shoez'
        ]       
    ]))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/overview

#### Get all the records

```php
use ShabuShabu\ParadeDB\Expressions\All;
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\Boolean;

Product::query()
    ->where('id', '@@@', new Boolean(
        should: new All(),
        mustNot: new Term('description', 'shoes')
    ))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/compound/all

#### Check that a field exists

```php
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\Exists;
use ShabuShabu\ParadeDB\Expressions\Boolean;

Product::query()
    ->where('id', '@@@', new Boolean(
        must: [
            new Term('description', 'shoes'),
            new Exists('rating')
        ],
    ))
    ->limit(5)
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/term/exists

#### Get none of the records

```php
use ShabuShabu\ParadeDB\Expressions\Blank;

Product::query()
    ->where('id', '@@@', new Blank())
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/compound/empty

#### Boost a query

```php
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\Boost;
use ShabuShabu\ParadeDB\Expressions\Boolean;

Product::query()
    ->where('id', '@@@', new Boolean(
        should: [
            new Term('description', 'shoes'),
            new Boost(new Term('description', 'running'), 2.0)
        ]
    ))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/compound/boost

#### Add a constant score

```php
use ShabuShabu\ParadeDB\Expressions\All;
use ShabuShabu\ParadeDB\Expressions\Score;
use ShabuShabu\ParadeDB\Expressions\Boolean;
use ShabuShabu\ParadeDB\Expressions\ConstScore;

Product::query()
    ->select(['*', new Score()])
    ->where('id', '@@@', new Boolean(
        should: [
            new ConstScore(new Term('description', 'shoes'), 1.0),
            new Term('description', 'running'),
        ]   
    ))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/compound/const

#### Perform a disjunction max query

```php
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\Score;
use ShabuShabu\ParadeDB\Expressions\DisjunctionMax;

Product::query()
    ->select(['*', new Score()])
    ->where('id', '@@@', new DisjunctionMax([
        new Term('description', 'shoes'),
        new Term('description', 'running'),
    ]))
    ->get();
```

The `DisjunctionMax` constructor also accepts an array of queries, so using the fluid interface might be more convenient for multiple queries:

```php
use ShabuShabu\ParadeDB\TantivyQL\Query;
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\Score;
use ShabuShabu\ParadeDB\Expressions\DisjunctionMax;
    
Product::query()
    ->select(['*', new Score()])
    ->where('id', '@@@', DisjunctionMax::query()
        ->add(Query::string()->where('description', 'shoes'))
        ->add('description:running')
        ->tieBreaker(1.2)
    )
    ->get();
```

This also allows you to conditionally add queries:

```php
use ShabuShabu\ParadeDB\TantivyQL\Query;
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\Score;
use ShabuShabu\ParadeDB\Expressions\DisjunctionMax;

Product::query()
    ->select(['*', new Score()])
    ->where('id', '@@@', DisjunctionMax::query()
        ->add(Query::string()->where('description', 'shoes'))
        ->add('description:running', when: false)
    )
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/compound/disjunction_max

#### Search for a fuzzy term

```php
use ShabuShabu\ParadeDB\Expressions\FuzzyTerm;

Product::query()
    ->where('id', '@@@', new FuzzyTerm('description', 'shoez'))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/term/fuzzy_term

#### Search for a fuzzy phrase

```php
use ShabuShabu\ParadeDB\Expressions\FuzzyPhrase;

Product::query()
    ->where('id', '@@@', new FuzzyPhrase('description', 'ruining shoez'))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/phrase/fuzzy_phrase

### Parse a Tantivy query string

Useful for directly searching for user-supplied queries.

```php
use ShabuShabu\ParadeDB\Expressions\Parse;

Product::query()
    ->where('id', '@@@', new Parse('description:"running shoes" OR category:footwear'))
    ->get();
```

Additionally, `ParadeDB Search for Laravel` comes with its own Tantivy Query Language Builder:

```php
use ShabuShabu\ParadeDB\TantivyQL\Query;
use ShabuShabu\ParadeDB\Expressions\Parse;

Product::query()
    ->where('id', '@@@', new Parse(
        Query::string()
            ->where('description', 'running shoes')
            ->orWhere('category', 'footwear')
    ))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/compound/parse

### Parse a Tantivy query string for a given field

Like `ShabuShabu\ParadeDB\Expressions\Parse` above, but it takes a query string without fields and searches for the given field.

```php
use ShabuShabu\ParadeDB\Expressions\ParseWithField;

Product::query()
    ->where('id', '@@@', new ParseWithField(
        field: 'description', 
        query: 'speaker bluetooth', 
        conjunctionMode: true,
    ))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/compound/parse#parse-with-field

#### Highlight search terms

```php
use ShabuShabu\ParadeDB\Expressions\Snippet;

Product::query()
    ->select(['id', new Snippet('description')])
    ->where('description', '@@@', 'shoes')
    ->limit(5)
    ->get();
```

See: https://docs.paradedb.com/documentation/full-text/highlighting

#### Search for a phrase

```php
use ShabuShabu\ParadeDB\Expressions\Phrase;

Product::query()
    ->where('id', '@@@', new Phrase(
        field: 'description',
        phrases: ['sleek', 'shoes'],
        slop: 1,
    ))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/phrase/phrase

#### Perform a phrase prefix query

```php
use ShabuShabu\ParadeDB\Expressions\PhrasePrefix;

Product::query()
    ->where('id', '@@@', new PhrasePrefix('description', ['running', 'sh']))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/phrase/phrase_prefix

#### Search within a given range

```php
use ShabuShabu\ParadeDB\Expressions\Range;
use ShabuShabu\ParadeDB\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Expressions\Ranges\Bounds;

Product::query()
    ->where('id', '@@@', new Range('rating', new Int4(1, 3, Bounds::includeStartExcludeEnd)))
    ->get();
```

Here are the supported range types (all within the `ShabuShabu\ParadeDB\Query\Expressions\Ranges` namespace), plus their corresponding Postgres type:

- `Int4::class;` or `int4range`
- `Int8::class;` or `int8range`
- `Numeric::class;` or `numrange`
- `Date::class;` or `daterange`
- `Timestamp::class;` or `tsrange`
- `TimestampTz::class;` or `tstzrange`

See: https://docs.paradedb.com/documentation/advanced/term/range

#### Find ranges for a given value

```php
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\Boolean;
use ShabuShabu\ParadeDB\Expressions\RangeTerm;

Product::query()
    ->where('id', '@@@', new Boolean(
        must: [
            new RangeTerm('weight_range', 1),
            new Term('category', 'footwear')
        ]
    ))
    ->get();
```

Ranges can also be compared to other ranges:

```php
use ShabuShabu\ParadeDB\Expressions\RangeTerm;
use ShabuShabu\ParadeDB\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Expressions\RangeRelation;

Product::query()
    ->where('id', '@@@', new RangeTerm(
        field: 'weight_range',
        term: new Int4(10, 12),
        relation: RangeRelation::intersects,
    ))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/term/range_term

#### Perform a regex query

```php
use ShabuShabu\ParadeDB\Expressions\Regex;

Product::query()
    ->where('id', '@@@', new Regex('description', '(plush|leather)'))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/term/regex

#### Search for a term

```php
use ShabuShabu\ParadeDB\Expressions\Term;

Product::query()
    ->where('id', '@@@', new Term('rating', 4))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/term/term

#### Search for a set of terms

```php
use ShabuShabu\ParadeDB\Expressions\Term;use ShabuShabu\ParadeDB\Expressions\TermSet;

Product::query()
    ->where('id', '@@@', new TermSet([
        new Term('description', 'shoes'),
        new Term('description', 'running'),
    ]))
    ->get();
```

The above query can also be written in a fluid manner:

```php
Product::query()
    ->where('id', '@@@', TermSet::query()
        ->add(new Term('description', 'shoes'))
        ->add(new Term('description', 'running'))
    )
    ->get();
```

The `term` method allows you to conditionally add terms:

```php
$when = false;

Product::query()
    ->where('id', '@@@', TermSet::query()
        ->add(new Term('description', 'shoes'), $when)
    )
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/term/term_set

#### Perform a complex boolean query

```php
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\Boolean;
use ShabuShabu\ParadeDB\Expressions\FuzzyTerm;
use ShabuShabu\ParadeDB\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Expressions\Ranges\Bounds;

Product::query()
    ->where('id', '@@@', new Boolean(
        should: new Term('description', 'headphones'),
        must: [
            new Term('category', 'electronics'),
            new FuzzyTerm('description', 'bluetooht'),
        ],
        mustNot: new Range('rating', new Int4(null, 2, Bounds::excludeAll)),
    ))
    ->get();
```

Boolean queries can also be constructed in a fluid manner:

```php
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\Boolean;
use ShabuShabu\ParadeDB\Expressions\FuzzyTerm;
use ShabuShabu\ParadeDB\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Expressions\Ranges\Bounds;

Product::query()
    ->where('id', '@@@', Boolean::query()
        ->should(new Term('description', 'headphones'))
        ->must(new Term('category', 'electronics'))
        ->must(new FuzzyTerm('description', 'bluetooht'))
        ->mustNot(new Range('rating', new Int4(null, 2, Bounds::excludeAll)))
    )
    ->get();
```

The two queries above are identical. The fluent methods allow you to conditionally add queries, though:

```php
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\Boolean;
use ShabuShabu\ParadeDB\Expressions\FuzzyTerm;
use ShabuShabu\ParadeDB\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Expressions\Ranges\Bounds;

$when = false;

Product::query()
    ->where('id', '@@@', Boolean::query()
        ->should(new Term('description', 'headphones'))
        ->must(new Term('category', 'electronics'))
        ->must(new FuzzyTerm('description', 'bluetooht'), $when)
        ->mustNot(new Range('rating', new Int4(null, 2, Bounds::excludeAll)))
    )
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/compound/boolean

#### Sort by rank

```php
use ShabuShabu\ParadeDB\Expressions\Score;

Product::query()
    ->addSelect(new Score())
    ->where('description', '@@@', 'shoes')
    ->orderBy(new Score())
    ->limit(5)
    ->get();
```

See: https://docs.paradedb.com/documentation/full-text/scoring

#### Find similar documents

When you pass a document ID, aka an Eloquent model key, then documents related to the given document are found.

```php
use ShabuShabu\ParadeDB\Expressions\MoreLikeThis;

Product::query()
    ->where('id', '@@@', new MoreLikeThis(
        idOrFields: 3,
        minTermFrequency: 1,
    ))
    ->get();
```

Alternatively, you can pass in document fields instead of an id to search against:

```php
use ShabuShabu\ParadeDB\Expressions\MoreLikeThis;

Product::query()
    ->where('id', '@@@', new MoreLikeThis(
        idOrFields: ['description' => 'shoes'], 
        minDocFrequency: 0, 
        maxDocFrequency: 100, 
        minTermFrequency: 1,
    ))
    ->get();
```

See: https://docs.paradedb.com/documentation/advanced/specialized/more_like_this

#### Using the query builder macro

In quite a lot of cases, the column you search against will be `id`. For this reason, you can also use the provided `whereSearch` macro.

```php
use ShabuShabu\ParadeDB\Expressions\MoreLikeThis;

Product::query()
    ->whereSearch(new MoreLikeThis(idOrFields: 3, minTermFrequency: 1))
    ->get();
```

### Hybrid search

`pg_search` also allows you to perform hybrid full-text/similarity searches. For this to work you will need to install [pgvector](https://github.com/pgvector/pgvector). Please note that `ParadeDB Search for Laravel` registers all custom `pgvector` operators already for you.

```php
use Tpetry\QueryExpressions\Value\Value;use ShabuShabu\ParadeDB\Expressions\Rank;use ShabuShabu\ParadeDB\Expressions\Score;use Tpetry\QueryExpressions\Language\Alias;use ShabuShabu\ParadeDB\Operators\Distance;use ShabuShabu\ParadeDB\Expressions\Similarity;use Tpetry\QueryExpressions\Operator\Arithmetic\Add;use Tpetry\QueryExpressions\Operator\Arithmetic\Divide;use Tpetry\QueryExpressions\Function\Conditional\Coalesce;

Product::query()
    ->withExpression('semantic_search', Product::query()
        ->select([
            'id',
            new Alias(new Rank(
                new Similarity('embedding', Distance::cosine, [1, 2, 3])
            ), 'rank'),
        ])
        ->orderBy(new Similarity('embedding', Distance::cosine, [1, 2, 3]))
        ->limit(20)
    )
    ->withExpression('bm25_search', Product::query()
        ->select([
            'id', 
            new Alias(new Rank(new Score()), 'rank'),
        ])
        ->where('description', '@@@', 'keyboard')
        ->limit(20)
    )
    ->select([
        new Alias(new Coalesce(['semantic_search.id', 'bm25_search.id']), 'id'),
        new Alias(new Add(
            new Coalesce([
                new Divide(new Value(1.0), new Add(new Value(60), 'semantic_search.rank')),
                new Value(0.0),
            ]),
            new Coalesce([
                new Divide(new Value(1.0), new Add(new Value(60), 'bm25_search.rank')),
                new Value(0.0),
            ]),
        ), 'score'),
        'products.description',
        'products.embedding'
    ])
    ->from('semantic_search')
    ->join('bm25_search', 'semantic_search.id', '=', 'bm25_search.id', 'full outer')
    ->join('products', 'products.id', '=', new Coalesce(['semantic_search.id', 'bm25_search.id']))
    ->orderByDesc('score')
    ->orderBy('description')
    ->limit(5);
```

See: https://docs.paradedb.com/documentation/guides/hybrid

### A word of caution

While it is possible to combine ParadeDB queries with regular Eloquent queries, you will incur some performance penalties.

For optimal performance it is recommended to let the `bm25` index do as much work as possible, so wherever possible you should use the [built-in filters](https://docs.paradedb.com/search/full-text/bm25#efficient-filtering) as well as [limit & offset](https://docs.paradedb.com/search/full-text/bm25#limit-and-offset)!

### Getting help

If your issue has something to do with this package, then please use the issues and discussions!

If your issue is related to `pg_search`, tho, then please create a discussion in the ParadeDB repo.

To make this a bit easier, you can use the `paradedb:help` command that ships with this package:

```bash
php artisan paradedb:help
```

Please note that this command is just an implementation of the `paradedb.help()` function. Please use this command wisely!

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
