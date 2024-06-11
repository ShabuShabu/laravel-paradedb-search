# ParadeDB Search for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shabushabu/laravel-paradedb-search.svg?style=flat-square)](https://packagist.org/packages/shabushabu/laravel-paradedb-search)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/shabushabu/laravel-paradedb-search/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/shabushabu/laravel-paradedb-search/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/shabushabu/laravel-paradedb-search/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/shabushabu/laravel-paradedb-search/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/shabushabu/laravel-paradedb-search.svg?style=flat-square)](https://packagist.org/packages/shabushabu/laravel-paradedb-search)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require shabushabu/laravel-paradedb-search
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-paradedb-search-config"
```

This is the contents of the published config file:

```php
return [
    'table_suffix' => '_idx',
];
```

## Usage

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

```php
use App\Models\Product;
use ShabuShabu\ParadeDB\ParadeQL\Builder;

Product::search()
    ->query(
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

### ParadeDB methods

```php
use App\Models\Product;
use ShabuShabu\ParadeDB\Query\Expressions\Rank;
use ShabuShabu\ParadeDB\Query\Expressions\Boolean;
use ShabuShabu\ParadeDB\Query\Expressions\FuzzyTerm;

Product::search()
    ->select(['*', new Rank('id')])
    ->query(new Boolean(
        should: [
            new FuzzyTerm(field: 'description', value: 'keyboard'),
            new FuzzyTerm(field: 'category', value: 'electronics'),
        ]   
    ))
    ->limit(20)
    ->offset(20)
    ->fullText();
```

### Hybrid search

```php
use App\Models\Product;use ShabuShabu\ParadeDB\ParadeQL\Builder;use ShabuShabu\ParadeDB\Query\Expressions\Distance;

Product::search(
    ->query(
        Builder::make()
            ->where('description', 'keyboard')
            ->orWhere('category', 'electronics')
    )
    ->similarity(
        column: 'embedding',
        operator: Distance::l2,
        value: "'[1,2,3]'"
    )
    ->hybrid();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Boris Glumpler](https://github.com/boris-glumpler)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
