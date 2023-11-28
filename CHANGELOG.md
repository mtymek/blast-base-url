# Changelog

## 2.2.0 - 2023-11-28

### Added

- Added PHP 8.2 and PHP 8.3 support.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Removed PHP 7.4 and 8.0 support.

### Fixed

- Nothing.

## 2.1.0 - 2021-11-16

### Added

- Added PHP 8.1 support

## 2.0.0 - 2021-08-04

### Changed

- Upgraded to PHP 7.4 and 8.0

## 1.0.0 - TBD
* Updated middleware class to be PSR-15-compatible.
* Added `ConfigProvider` and Zend Expressive component definition to composer.json file.
* Added support for PHP 7.1 and 7.2
* Dropped support for PHP 5
* Updated PHPUnit to v6

## 0.2.0 - 26.06.2016

* Added compatibility with ServiceManager v3 (doesn't show deprectation notice anymore)
* Updated setup documentation to ensure default basePath plugin is overriden
* [#8](https://github.com/mtymek/blast-base-url/pull/11) removes code that finds "path" part of the URI, as it was
duplicating similar functionality from `zend-diactoros`.

## 0.1.2 - 01.06.2016

Fixed

* [#8](https://github.com/mtymek/blast-base-url/pull/8) ensures that leading slash is preserved when application is served
  directly from root domain.
* [#9](https://github.com/mtymek/blast-base-url/pull/9) reorders instructions to simplify code flow.


## v0.1.1 - 25.01.2016

* ...
