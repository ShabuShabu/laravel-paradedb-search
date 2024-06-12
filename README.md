<p align="center"><img src="laravel-paradedb-search.png" alt="ParadeDB Search for Laravel"></p>

# ParadeDB Search for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shabushabu/laravel-paradedb-search.svg?style=flat-square)](https://packagist.org/packages/shabushabu/laravel-paradedb-search)
[![Total Downloads](https://img.shields.io/packagist/dt/shabushabu/laravel-paradedb-search.svg?style=flat-square)](https://packagist.org/packages/shabushabu/laravel-paradedb-search)

Integrates the `pg_search` Postgres extension by [ParadeDB](https://paradedb.com) into [Laravel](https://laravel.com)

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

The builder has been modeled to be similar to the Eloquent/Query builder provided by Laravel. 

```php
use App\Models\Product;
use ShabuShabu\ParadeDB\ParadeQL\Builder;

Product::search()
    ->where(
        Builder::make()
            ->where('description', ['keyboard', 'toy'])
            ->where(
                fn (Builder $builder) => $builder
                    ->where('category', 'electronics')
                    ->orWhere('tag', 'office')
            )
    )
    ->limit(20)
    ->get();
```

### ParadeDB functions

For more complex operations, it will be necessary to use some of the provided [ParadeDB functions](https://docs.paradedb.com/search/full-text/complex), all of which have corresponding query expressions, like `FuzzyTerm`.

```php
use App\Models\Product;
use ShabuShabu\ParadeDB\Query\Expressions\Rank;
use ShabuShabu\ParadeDB\Query\Expressions\Boolean;
use ShabuShabu\ParadeDB\Query\Expressions\FuzzyTerm;

Product::search()
    ->select(['*', new Rank('id')])
    ->where(new Boolean(
        should: [
            new FuzzyTerm(field: 'description', value: 'keyboard'),
            new FuzzyTerm(field: 'category', value: 'electronics'),
        ]   
    ))
    ->limit(20)
    ->offset(20)
    ->get();
```

It's also possible to paginate the results:

```php
use App\Models\Product;
use App\Models\Product;
use ShabuShabu\ParadeDB\ParadeQL\Builder;

Product::search()
    ->where(Builder::make()->where('description', 'keyboard'))
    ->simplePaginate(20);
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

### A word of caution

While it is possible to combine ParadeDB queries with regular Eloquent queries, you will incur some performance penalties.

For optimal performance it is recommended to let the `bm25` index do as much work as possible, so wherever possible you should use the [built-in filters](https://docs.paradedb.com/search/full-text/bm25#efficient-filtering) as well as [limit & offset](https://docs.paradedb.com/search/full-text/bm25#limit-and-offset)!

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Todo

- [ ] Sort test run issues when hitting the DB
- [ ] Add the remaining tests
- [ ] Create initial tag v0.1.0
- [ ] Publish to Packagist

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ParadeDB](https://github.com/paradedb) for creating the extension
- [ShabuShabu](https://github.com/ShabuShabu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
