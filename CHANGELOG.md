# Changelog

All notable changes to this project will be documented in this file. See [conventional commits](https://www.conventionalcommits.org/) for commit guidelines.

---

## [0.12.1](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.12.0..v0.12.1) - 2025-12-25

### Refactoring

- added an expression for snippet positions - ([0df8fd5](https://github.com/ShabuShabu/laravel-paradedb-search/commit/0df8fd524d53a69f6fe24443fc785a74ba51fb7a)) - Boris Glumpler

---

## [0.12.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.11.0...v0.12.0) (2025-05-03)

### Features

* upgraded package dependencies - ([6f865dc](https://github.com/ShabuShabu/laravel-paradedb-search/commit/6f865dca4f695e05c061c215f9377a612b3fba75))

---

## [0.11.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.10.0...v0.11.0) (2025-02-09)

### Features

* added Laravel 12 compatibility ([c683bea](https://github.com/ShabuShabu/laravel-paradedb-search/commits/c683beab6f7ca0b02bbbb9fba8e3a1b880379f2d))

---

## [0.10.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.9.1...v0.10.0) (2025-02-09)

### Features

* added pg_search v0.15.1 compatibility ([097d060](https://github.com/ShabuShabu/laravel-paradedb-search/commits/097d060d8c2fe0bdfc4bfed987d05ac04d1acb0d))

---

### [0.9.1](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.9.0...v0.9.1) (2024-11-23)

### Features

* added paradedb:tokenizers command ([6e173dc](https://github.com/ShabuShabu/laravel-paradedb-search/commits/6e173dc73fa573755f13fe6b83ed7c5d3d5ea029))

---

## [0.9.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.8.0...v0.9.0) (2024-11-23)

### Features

* added compatibility with pg_search v0.13.0 ([8328b21](https://github.com/ShabuShabu/laravel-paradedb-search/commits/8328b2195df825c451a2ee96e1a058e39d56fe5d))

---

## [0.8.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.7.0...v0.8.0) (2024-11-20)

### Bug Fixes

* use regular drop index now ([e4825c4](https://github.com/ShabuShabu/laravel-paradedb-search/commits/e4825c4daf48f6b7b94ece87b86329fb3502601f))

---

## [0.7.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.6.0...v0.7.0) (2024-11-16)

### Features

* added ability to add partial bm25 indices ([fc2f1da](https://github.com/ShabuShabu/laravel-paradedb-search/commits/fc2f1daaadb82e5eaee1b7c9e549636937b5142d))
* implemented all new v0.12.0 expressions ([1b10a68](https://github.com/ShabuShabu/laravel-paradedb-search/commits/1b10a6855130592a54b1155fb01c4382e9e8ace4))

---

## [0.6.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.5.1...v0.6.0) (2024-06-19)

### Features

* added new Search::addSelect method ([e9bba0e](https://github.com/ShabuShabu/laravel-paradedb-search/commits/e9bba0edc86862625838400351cec541d9bbe38d))
* added TermSet::add method for fluid query creation ([4e7a716](https://github.com/ShabuShabu/laravel-paradedb-search/commits/4e7a7162cfbe2a6032fda999f803ddf2e1c7ba5c))
* boolean queries can now be constructed in a fluid manner ([c52ae96](https://github.com/ShabuShabu/laravel-paradedb-search/commits/c52ae96e4419ae7bea3e1c2127253943acd735ac))

---

### [0.5.1](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.5.0...v0.5.1) (2024-06-18)

### Features

* added missing paradeql range operators ([cb7b512](https://github.com/ShabuShabu/laravel-paradedb-search/commits/cb7b5129105c2137234c346ee6be3adec70faa36))

---

## [0.5.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.4.0...v0.5.0) (2024-06-16)

### Features

* added a command to create and drop the paradedb test table ([a02c36f](https://github.com/ShabuShabu/laravel-paradedb-search/commits/a02c36ff1d4d2308027d477214168414ffed0cf5))
* added a paginate method ([638947d](https://github.com/ShabuShabu/laravel-paradedb-search/commits/638947d9ef7c6fd8af16b1d6785b86b20b64d5a8))

### Bug Fixes

* boolean defaults and fuzzy term parameter name are fixed ([14ecf03](https://github.com/ShabuShabu/laravel-paradedb-search/commits/14ecf039ba9f4d42fa2d13add564bf9697ae2359))
* ensured ranges are properly formatted ([bf67e16](https://github.com/ShabuShabu/laravel-paradedb-search/commits/bf67e16df2905510751a29c9b3b2a773beecf5f0))
* setting columns for hybrid searches would throw errors for expressions ([b6dc80a](https://github.com/ShabuShabu/laravel-paradedb-search/commits/b6dc80a82eaf28c710a2048bcf3ee92c40ba2295))

---

## [0.4.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.3.3...v0.4.0) (2024-06-14)

### Features

* allow for the base query to be modified ([58beb1b](https://github.com/ShabuShabu/laravel-paradedb-search/commits/58beb1be4fd64077e775b0db0a00e78fda15de68))

---

### [0.3.3](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.3.2...v0.3.3) (2024-06-14)

### Bug Fixes

* switched to Model::newModelQuery() ([59e9b4a](https://github.com/ShabuShabu/laravel-paradedb-search/commits/59e9b4a40165b57d7dabe975a1ee77698c40021e))

---

### [0.3.2](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.3.1...v0.3.2) (2024-06-14)

### Bug Fixes

* whereFilter value can be a string now ([a1cc657](https://github.com/ShabuShabu/laravel-paradedb-search/commits/a1cc6571660d0b5a73d008b2aa1c37b58059c19b))

---

### [0.3.1](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.3.0...v0.3.1) (2024-06-14)

### Features

* added custom index names to search ([5eff892](https://github.com/ShabuShabu/laravel-paradedb-search/commits/5eff892ec1d1e775e8dc0b0c25e2163f885f248d))

---

## [0.3.0](https://github.com/ShabuShabu/laravel-paradedb-search/compare/v0.2.0...v0.3.0) (2024-06-14)

### Features

* added ability to give bm25 indices a custom name ([370df0e](https://github.com/ShabuShabu/laravel-paradedb-search/commits/370df0e3dd709034d7bbf91fd3590d917f2fa8c3))

---

## 0.2.0 (2024-06-13)

### Features

* added a command to create gh discussions ([f03b4c1](https://github.com/ShabuShabu/laravel-paradedb-search/commits/f03b4c1f1e7933d15c7a8cfd1458b93312183614))
* added hybrid search ([a5b9478](https://github.com/ShabuShabu/laravel-paradedb-search/commits/a5b94783e08892ea11e48303787f1bc9c1b78a57))
* added slop operator to paradeql builder ([7f64b6f](https://github.com/ShabuShabu/laravel-paradedb-search/commits/7f64b6fcb544231e975a0a01beca5be332ecc7c7))
* first pass at a ParadeQL query builder ([e89d61a](https://github.com/ShabuShabu/laravel-paradedb-search/commits/e89d61a03c31a27b4e818efb7a72719dc048ff16))
* implemented paradedb search methods ([44abe0a](https://github.com/ShabuShabu/laravel-paradedb-search/commits/44abe0ade75cac6e834fbb8e3d3c22c06907ff0e))
* introduced enums for filter and range operators ([f38ff18](https://github.com/ShabuShabu/laravel-paradedb-search/commits/f38ff18b136ed006767ecd109d8ba14219ef7bba))

### Bug Fixes

* added missing real type ([bcc08b4](https://github.com/ShabuShabu/laravel-paradedb-search/commits/bcc08b45be6c122dc12d871ba61bf97a44059519))
* changed to RefreshDatabase trait in testing, closes [#1](https://github.com/ShabuShabu/laravel-paradedb-search/issues/1) ([a2b5333](https://github.com/ShabuShabu/laravel-paradedb-search/commits/a2b5333b5e9b8dcd8c8a343a902fd8929592abd5))