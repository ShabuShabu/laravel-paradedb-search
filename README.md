<p align="center"><img src="laravel-paradedb-search.png" alt="ParadeDB Search for Laravel"></p>

# ParadeDB Search for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shabushabu/laravel-paradedb-search.svg?style=flat-square)](https://packagist.org/packages/shabushabu/laravel-paradedb-search)
[![Total Downloads](https://img.shields.io/packagist/dt/shabushabu/laravel-paradedb-search.svg?style=flat-square)](https://packagist.org/packages/shabushabu/laravel-paradedb-search)

Integrates the `pg_search` Postgres extension by [ParadeDB](https://docs.paradedb.com/search/quickstart) into [Laravel](https://laravel.com)

## Supported minimum versions

| PHP | Laravel | PostgreSQL | pg_search |
|-----|---------|------------|-----------|
| 8.2 | 11.0    | 16         | 0.7.5     |

## Installation

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
    'index_suffix' => '_idx',
];
```

## Usage

### Preparing your model

Just add the `Searchable` trait to your model to enable search:

```php
use Illuminate\Database\Eloquent\Model;
use ShabuShabu\ParadeDB\Concerns\Searchable;

class Product extends Model
{
    use Searchable;
    
    // the rest of the model...
}
```

### ParadeQL

ParadeDB Search for Laravel comes with a fluent builder for ParadeQL, a simple query language.

This builder can be passed as a condition to a search `where` method or used within the various ParadeDB expressions.

#### Basic query

```php
use ShabuShabu\ParadeDB\ParadeQL\Builder;

Builder::make()->where('description', 'keyboard')->get();

// results in: description:keyboard
```

#### Add an IN condition

```php
Builder::make()
    ->where('description', ['keyboard', 'toy'])
    ->get();

// results in: description:IN [keyboard, toy]
```

#### Add an AND NOT condition

```php
Builder::make()
    ->where('category', 'electronics')
    ->whereNot('description', 'keyboard')
    ->get();

// results in: category:electronics AND NOT description:keyboard
```

#### Boost a condition

```php
Builder::make()->where('description', 'keyboard', boost: 1)->get();

// results in: description:keyboard^1
```

#### Apply the slop operator

```php
Builder::make()->where('description', 'ergonomic keyboard', slop: 1)->get();

// results in: description:"ergonomic keyboard"~1
```

#### More complex example with a sub condition

```php
Builder::make()
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
use ShabuShabu\ParadeDB\ParadeQL\Operators\Filter;

Builder::make()->whereFilter('rating', Filter::equals, 4)->get();

// results in: rating:4
```

#### Apply a boolean filter

```php
use ShabuShabu\ParadeDB\ParadeQL\Operators\Filter;

Builder::make()->whereFilter('is_available', '=', false)->get();

// results in: is_available:false
```

#### Apply a basic range filter

```php
use ShabuShabu\ParadeDB\ParadeQL\Operators\Filter;

Builder::make()->whereFilter('rating', '>', 4)->get();

// results in: rating:>4
```

#### Apply an inclusive range filter

```php
use ShabuShabu\ParadeDB\ParadeQL\Operators\Range;

Builder::make()->whereFilter('rating', Range::includeAll, [2, 5])->get();

// results in: rating:[2 TO 5]
```

#### Apply an exclusive range filter

```php
use ShabuShabu\ParadeDB\ParadeQL\Operators\Range;

Builder::make()->whereFilter('rating', Range::excludeAll, [2, 5])->get();

// results in: rating:{2 TO 5}
```

### ParadeDB functions

For more complex operations, it will be necessary to use some of the provided [ParadeDB functions](https://docs.paradedb.com/search/full-text/complex), all of which have corresponding query expressions:

#### Get all the records

```php
use ShabuShabu\ParadeDB\Query\Expressions\All;

Product::search()->where(new All())->get();
```

#### Get none of the records

```php
use ShabuShabu\ParadeDB\Query\Expressions\Blank;

Product::search()->where(new Blank())->get();
```

#### Boost a query

```php
use ShabuShabu\ParadeDB\Query\Expressions\All;
use ShabuShabu\ParadeDB\Query\Expressions\Boost;

Product::search()->where(new Boost(new All(), 3.9))->get();
```

#### Add a constant score

```php
use ShabuShabu\ParadeDB\Query\Expressions\All;
use ShabuShabu\ParadeDB\Query\Expressions\ConstScore;

Product::search()->where(new ConstScore(new All(), 3.9))->get();
```

#### Perform a disjunction max query

```php
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\DisjunctionMax;

Product::search()->where(
    new DisjunctionMax(Builder::make()->where('description', 'keyboard'))
)->get();
```

#### Search for a fuzzy term

```php
use ShabuShabu\ParadeDB\Query\Expressions\FuzzyTerm;

Product::search()->where(new FuzzyTerm('description', 'keyboard'))->get();
```

#### Highlight search terms

```php
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Highlight;
use ShabuShabu\ParadeDB\Query\Expressions\DisjunctionMax;

Product::search()
    ->select(['*', new Highlight('id', 'name')])
    ->where(new DisjunctionMax(Builder::make()->where('description', 'keyboard')))
    ->get();
```

#### Search for a phrase

```php
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Phrase;

Product::search()
    ->where(new Phrase('description', ['robot', 'building', 'kits']))
    ->get();
```

#### Perform a phrase prefix query

```php
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\PhrasePrefix;

Product::search()
    ->where(new PhrasePrefix('description', ['robot', 'building', 'kits', 'am']))
    ->get();
```

#### Search within a given range

```php
use ShabuShabu\ParadeDB\Query\Expressions\Range;
use ShabuShabu\ParadeDB\Query\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Query\Expressions\Ranges\Bounds;

Product::search()
    ->stableSort()
    ->where(new Range('rating', new Int4(1, 3, Bounds::includeStartExcludeEnd)))
    ->get();
```

Here are the supported range types (all within the `ShabuShabu\ParadeDB\Query\Expressions\Ranges` namespace), plus their corresponding Postgres type:

- `Int4::class;` or `int4range`
- `Int8::class;` or `int8range`
- `Numeric::class;` or `numrange`
- `Date::class;` or `daterange`
- `Timestamp::class;` or `tsrange`
- `TimestampTz::class;` or `tstzrange`

#### Perform a regex query

```php
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Regex;

Product::search()
    ->where(new Regex('description', '(team|kits|blabla)'))
    ->get();
```

#### Search for a term

```php
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Term;

Product::search()
    ->where(new Term('description', 'building'))
    ->get();
```

#### Search for a set of terms

```php
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Term;
use ShabuShabu\ParadeDB\Query\Expressions\TermSet;

Product::search()
    ->where(new TermSet([
        new Term('description', 'building'),
        new Term('description', 'things'),
    ]))
    ->get();
```

#### Perform a complex boolean query

```php
use App\Models\Product;
use ShabuShabu\ParadeDB\Query\Expressions\Range;
use ShabuShabu\ParadeDB\Query\Expressions\Boolean;
use ShabuShabu\ParadeDB\Query\Expressions\FuzzyTerm;
use ShabuShabu\ParadeDB\Query\Expressions\Ranges\TimestampTz;

Product::search()
    ->where(new Boolean(
        must: [
            new Range('created_at', new TimestampTz(null, now())),
        ],
        should: [
            new Boost(new FuzzyTerm('name', 'keyboard'), 2),
            new FuzzyTerm('description', 'keyboard'),
        ],
        mustNot: [
            new Range('deleted_at', new TimestampTz(null, now())),
        ],
    ))
    ->get();
```

#### Sort by rank

```php
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Term;
use ShabuShabu\ParadeDB\Query\Expressions\Rank;

Product::search()
    ->select(['*', new Rank('id')])
    ->where(new Term('description', 'building'))
    ->get();
```

#### Pagination

It's also possible to paginate the results. Both the `paginate` and `simplePaginate` methods use  the underlying `limit` & `offset` functionality, so will be more performant:

```php
use App\Models\Product;
use App\Models\Product;
use ShabuShabu\ParadeDB\ParadeQL\Builder;

Product::search()
    ->where(Builder::make()->where('description', 'keyboard'))
    ->paginate(20);
```

### Hybrid search

Whenever a similarity query is provided, the package will automatically perform a [hybrid search](https://docs.paradedb.com/search/hybrid/basic). Please note that a ParadeDB query is still required!

```php
use App\Models\Product;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Distance;
use ShabuShabu\ParadeDB\Query\Expressions\Similarity;

Product::search()
    ->where(Builder::make()->where('description', 'keyboard'))
    ->where(new Similarity('embedding', Distance::l2, [1, 2, 3]))
    ->get();
```

### Modifying the underlying query

Occasionally, it will be necessary to modify the base query, for example to eager-load some relationships. This can be accomplished like so:

```php
use App\Models\Product;
use Illuminate\Database\Eloquent;
use ShabuShabu\ParadeDB\ParadeQL\Builder;

Product::search()
    ->modifyQueryUsing(fn (Eloquent\Builder $builder) => $builder->with('tags'))
    ->where(Builder::make()->where('description', 'keyboard'))
    ->get();
```

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

## Todo

- [ ] Publish to Packagist

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
