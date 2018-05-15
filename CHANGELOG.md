# Changelog

## 1.0.0 - TBD
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
