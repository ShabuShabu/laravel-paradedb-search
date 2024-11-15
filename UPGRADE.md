# Upgrade Guide

## Upgrading To 0.7.0 From 0.6.*

### Minimum Versions

The following required dependency versions have been updated:

- The minimum `pg_search` version is now v0.12.0

### Renamed namespaces

- The `ShabuShabu\ParadeDB\ParadeQL` namespace was renamed to `ShabuShabu\ParadeDB\TantivyQL`
- Expressions can now be found directly in the `ShabuShabu\ParadeDB\Expressions` namespace

### Deletions

- Due to the use of the `@@@` operator in v0.12.0, the `ShabuShabu\ParadeDB\Query\Search` class is not needed anymore and was deleted
- The `FullTextSearch` and `HybridSearch` expressions have been removed

### Changes

- Order of `ShabuShabu\ParadeDB\Expressions\Boolean` parameters was changed to `should`, `must` and `mustNot` to reflect the order in `pg_search` itself.