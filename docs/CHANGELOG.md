# CHANGELOG

Notice: This library follows [SEMVER 2.0.0](https://semver.org/spec/v2.0.0.html) convention.

## Version 3.0.1 2020-01-24

This is a maintenance release.

- Add example for `obtain` method.
- Upgrade from `phpstan/phpstan-shim: ^0.11` to `phpstan/phpstan-shim: ^0.12`.
- Update license year to 2020.
- Fix links on README.
- Update Travis-CI and Scrutinizer-CI scripts.

## Version 3.0.0 2019-10-24

You should not have any trouble upgrading to from version `2.0.0` to `3.0.0` unless you are creating a concrete
class that implements `ExpressionExtractorInterface`. 

- [BC] Change interface of `ExpressionExtractorInterface` to add a new method `obtain(DOMDocument $document): array`
  that returns the extracted values, this change remove this responsability from `extract` method.
- [BC] Change `DiscoverExtractor::format()`, second argument must be `string`, was `mixed`.
- Update continuous integration and development environment, mayor changes:
    - Travis build on PHP version `7.4snapshot`.
    - Scrutinizer decides which PHP version to run.
    - Remove `overtrue/phplint`.

## Version 2.0.0 2019-03-28

- Allows to create an expression with format fixes from specific types
- Change `ExpressionExtractorInterface` to add `public function format(array $values): string`
- Give a unique name for extractors, so when discovering by type can obtain an item
- Change `ExpressionExtractorInterface` to add `public function uniqueName(): string`
    - `Comprobante33` uses `CFDI33`
    - `Comprobante32` uses `CFDI32`
    - `Retenciones10` uses `RET10`
- Rename `ExpressionExtractor` to `DiscoverExtractor`, it makes more sense

## Version 1.0.0 2019-03-27

- Create this package as is a common use between other packages
- Include Retenciones e información de pagos (RET10)
- Implement more tests
