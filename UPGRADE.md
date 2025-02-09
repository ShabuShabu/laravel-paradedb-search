# Upgrade Guide

## Upgrading To 0.10.0 From 0.9.*

### Minimum Versions

The following required dependency versions have been updated:

- The minimum `pg_search` version is now `v0.15.1`

### Deletions

- The `FuzzyPhrase` expression has been removed and is replaced by the new `FullText` expression

## Upgrading To 0.9.0 From 0.8.*

### Dependencies

- `tpetry/laravel-postgresql-enhanced` is now a dependency

### Minimum Versions

The following required dependency versions have been updated:

- The minimum `pg_search` version is now `v0.13.0`
- `tpetry/laravel-postgresql-enhanced` @ `v2.0`

### Deletions

- `ShabuShabu\ParadeDB\Indices\Bm25` has been deleted as it's now possible to create the index using native Laravel schema methods.

## Upgrading To 0.8.0 From 0.7.*

### Minimum Versions

The following required dependency versions have been updated:

- The minimum `pg_search` version is now `v0.12.2`

## Upgrading To 0.7.0 From 0.6.*

0.7.0 is a big upgrade and care should be taken to check your code for incompatibilities. The introduction of the `@@@` operator opens up a lot of possibilities and allowed us to delete quite a bit of code.

### Minimum Versions

The following required dependency versions have been updated:

- The minimum `pg_search` version is now `v0.12.0`

### Renamed namespaces

- The `ShabuShabu\ParadeDB\ParadeQL` namespace was renamed to `ShabuShabu\ParadeDB\TantivyQL`
- Expressions can now be found directly in the `ShabuShabu\ParadeDB\Expressions` namespace

### Deletions

- Due to the use of the `@@@` operator in `v0.12.0`, the `ShabuShabu\ParadeDB\Query\Search` class is not needed anymore and was deleted
- The `FullTextSearch` and `HybridSearch` expressions have been removed

### Changes

- Order of `ShabuShabu\ParadeDB\Expressions\Boolean` parameters was changed to `should`, `must` and `mustNot` to reflect the order in `pg_search` itself.

### Misc

- Please note that it is necessary to restart Postgres after upgrading `pg_search`