# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

## [0.5.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.2.0...v0.5.0) (2024-06-16)


### Features

* added a command to create and drop the paradedb test table ([a02c36f](https://github.com/ShabuShabu/laravel-paradedb-search/commits/a02c36ff1d4d2308027d477214168414ffed0cf5))
* added a paginate method ([638947d](https://github.com/ShabuShabu/laravel-paradedb-search/commits/638947d9ef7c6fd8af16b1d6785b86b20b64d5a8))
* added ability to give bm25 indices a custom name ([370df0e](https://github.com/ShabuShabu/laravel-paradedb-search/commits/370df0e3dd709034d7bbf91fd3590d917f2fa8c3))
* added custom index names to search ([5eff892](https://github.com/ShabuShabu/laravel-paradedb-search/commits/5eff892ec1d1e775e8dc0b0c25e2163f885f248d))
* allow for base query to be modified ([58beb1b](https://github.com/ShabuShabu/laravel-paradedb-search/commits/58beb1be4fd64077e775b0db0a00e78fda15de68))


### Bug Fixes

* boolean defaults and fuzzy term parameter name ([14ecf03](https://github.com/ShabuShabu/laravel-paradedb-search/commits/14ecf039ba9f4d42fa2d13add564bf9697ae2359))
* ensured ranges are properly formatted ([bf67e16](https://github.com/ShabuShabu/laravel-paradedb-search/commits/bf67e16df2905510751a29c9b3b2a773beecf5f0))
* setting columns for hybrid searches would throw errors for expressions ([b6dc80a](https://github.com/ShabuShabu/laravel-paradedb-search/commits/b6dc80a82eaf28c710a2048bcf3ee92c40ba2295))
* switched to Model::newModelQuery() ([59e9b4a](https://github.com/ShabuShabu/laravel-paradedb-search/commits/59e9b4a40165b57d7dabe975a1ee77698c40021e))
* whereFilter value can be a string now ([a1cc657](https://github.com/ShabuShabu/laravel-paradedb-search/commits/a1cc6571660d0b5a73d008b2aa1c37b58059c19b))
