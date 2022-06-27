# CHANGELOG

## Acerca de SemVer

Usamos [Versionado Semántico 2.0.0](SEMVER.md) por lo que puedes usar esta librería sin temor a romper tu aplicación.

## Cambios no liberados en una versión

Pueden aparecer cambios no liberados que se integran a la rama principal, pero no ameritan una nueva liberación de
versión, aunque sí su incorporación en la rama principal de trabajo, generalmente se tratan de cambios en el desarrollo.

## Listado de cambios

### Version 3.2.0 2022-06-27

#### Soporte de RET 2.0

Se agrega el soporte de RET 2.0 con fundamento en el Anexo 20.

#### Documentación

Se corrigen los textos que tenían una versión incorrecta y se refieren a CFDI 4.0.

### Version 3.1.0 2022-06-16

#### Soporte de CFDI 4.0

Se agrega el soporte de CFDI 4.0 con fundamento en el Anexo 20.
Para ello, la *Especificación técnica del código de barras bidimensional a incorporar en la representación impresa*
se separa a un estándar interno llamado `CfdiStandard20170701`

#### Refactorización de métodos compartidos

Las expresiones de CFDI 3.3 y CFDI 4.0 son idénticas, así como la forma de formatear datos como RFC, Sello, Total, etc. 
por lo que se refactorizan las clases para poner los métodos comunes en traits.

#### Codificación de caracteres especiales

Se corrige el problema encontrado al formar expresiones en la codificación de los valores de las expresiones,
al parecer, los valores necesitan expresarse con codificación XML, a pesar de que estén formando parte de una URL.
Por lo tanto, los RFC que contienen `&` se deben especificar como `&amp;`.

Se hicieron las pruebas con las siguientes URL:

- `https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=790BA0FF-26DD-4C97-AA84-7A2E9A132730&re=GG&amp;950901AD7&rr=AER970617UH7&tt=1550025.37&fe=SoCSeA==`
- `https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=5555BFA3-7107-4715-A2E1-31F05E86961B&re=SAZD861013FU2&rr=CAÑA360510556&tt=0.01&fe=l+AO4g==`

La misma regla aplica para el *registro de identificación fiscal del extranjero* utilizado en un
*CFDI de Retenciones e Información de Pagos*.

Se actualiza el año del archivo de licencia.

Se actualizaron algunos temas relacionados con el entorno de desarrollo:

- Se agrega el script `tests/expression.php` para obtener la expresión de un CFDI.
- Se corren las pruebas usando PHP 7.3, 7.4, 8.0 y 8.1.
- Se agregó la herramienta `psalm`.
- Se agregó la herramienta `infection`.
- Se excluyó `.phive/` del paquete distribuible.
- En la acción de construcción de GitHub, se crearon trabajos para las tareas.
- Se actualizaron las versiones de las dependencias.

Los siguientes cambios se hicieron en sus fechas correspondientes, pero hasta ahora se agregan a una versión.

#### Cambios realizados desde 2021-09-01

- La nueva versión mínima de PHP 7.3.
- La nueva versión mínima de PHPUnit es 9.5.
- Actualización del entorno de desarrollo.
- Migración de Travis-CI a GitHub Workflows. ¡Muchas gracias Travis!

#### Cambios realizados desde 2021-01-11

Ninguno de estos cambios introducen alguna modificación en el código, solo en las pruebas y en el entorno de desarrollo.
Por esto no se libera una nueva versión y solamente se actualiza la rama principal.

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
  that returns the extracted values, this change remove this responsibility from `extract` method.
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
