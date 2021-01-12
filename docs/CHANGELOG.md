# CHANGELOG

## Acerca de SemVer

Usamos [Versionado Semántico 2.0.0](SEMVER.md) por lo que puedes usar esta librería sin temor a romper tu aplicación.

## Cambios no liberados en una versión

Pueden aparecer cambios no liberados que se integran a la rama principal pero no ameritan una nueva liberación de
versión aunque sí su incorporación en la rama principal de trabajo, generalmente se tratan de cambios en el desarrollo.

## Listado de cambios

### UNRELEASED 2021-01-11

Ninguno de estos cambios introducen alguna modificación en el código, solo en las pruebas y en el entorno de desarrollo.
Por esto no se libera una nueva versión y solo se actualiza la rama principal.

- Actualización del año en la licencia, feliz 2021 desde PhpCfdi.
- Se actualiza la documentación a español.
- Se incluye PHP 8.0 en la construcción de Travis-CI.
- Arreglar los problemas encontrados por PHPStan.
- Se actualizan los archivos de configuración.
- Se actualizan los pasos de construcción en Travis-CI y Scrutinizer.
- Composer: Se actualizan los comandos de desarrollo para que usen el comando con el que composer fue invocado.

### Version 3.0.1 2020-01-24

This is a maintenance release.

- Add example for `obtain` method.
- Upgrade from `phpstan/phpstan-shim: ^0.11` to `phpstan/phpstan-shim: ^0.12`.
- Update license year to 2020.
- Fix links on README file.
- Update Travis-CI and Scrutinizer-CI scripts.

### Version 3.0.0 2019-10-24

You should not have any trouble upgrading to from version `2.0.0` to `3.0.0` unless you are creating a concrete
class that implements `ExpressionExtractorInterface`. 

- [BC] Change interface of `ExpressionExtractorInterface` to add a new method `obtain(DOMDocument $document): array`
  that returns the extracted values, this change remove this responsability from `extract` method.
- [BC] Change `DiscoverExtractor::format()`, second argument must be `string`, was `mixed`.
- Update continuous integration and development environment, mayor changes:
    - Travis builds on PHP version `7.4snapshot`.
    - Scrutinizer decides which PHP version to run.
    - Remove `overtrue/phplint`.

### Version 2.0.0 2019-03-28

- Allows to create an expression with format fixes from specific types
- Change `ExpressionExtractorInterface` to add `public function format(array $values): string`
- Give a unique name for extractors, so when discovering by type can obtain an item
- Change `ExpressionExtractorInterface` to add `public function uniqueName(): string`
    - `Comprobante33` uses `CFDI33`
    - `Comprobante32` uses `CFDI32`
    - `Retenciones10` uses `RET10`
- Rename `ExpressionExtractor` to `DiscoverExtractor`, it makes more sense

### Version 1.0.0 2019-03-27

- Create this package as is a common use between other packages
- Include Retenciones e información de pagos (RET10)
- Implement more tests
