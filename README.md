# phpcfdi/cfdi-expresiones

[![Source Code][badge-source]][source]
[![Packagist PHP Version Support][badge-php-version]][php-version]
[![Discord][badge-discord]][discord]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Reliability][badge-reliability]][reliability]
[![Maintainability][badge-maintainability]][maintainability]
[![Code Coverage][badge-coverage]][coverage]
[![Violations][badge-violations]][violations]
[![Total Downloads][badge-downloads]][downloads]

> Genera expresiones de CFDI 4.0, CFDI 3.3, CFDI 3.2, RET 1.0 y RET 2.0

:us: The documentation of this project is in spanish as this is the natural language for intended audience.

:mexico: La documentación del proyecto está en español porque ese es el lenguaje principal de los usuarios.

Esta librería contiene objetos de ayuda para crear expresiones de CFDI 3.2, CFDI 3.3, CFDI 4.0, RET 1.0 y RET 2.0 
de acuerdo a la información técnica del SAT en el Anexo 20.

Estas expresiones se utilizan principalmente para dos motivos:

1. Generar el código QR de una representación impresa de un CFDI o RET.
2. Consultar el WebService del SAT de estado de un CFDI.

Ejemplo de expresión para CFDI 3.3 y CFDI 4.0:

Estas especificaciones comparten el mismo estándar.

```text
https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC&re=POT9207213D6&rr=DIM8701081LA&tt=2010.01&fe=/OAgdg==
```

Ejemplo de expresión para CFDI 3.2:

```text
?re=AAA010101AAA&rr=COSC8001137NA&tt=0000001234.567800&id=CEE4BE01-ADFA-4DEB-8421-ADD60F0BEDAC
```

Ejemplo de expresión para RET 1.0:

```text
?&re=XAXX010101000&nr=12345678901234567890%tt=1234567890.123456&id=ad662d33-6934-459c-a128-BDf0393f0f44
```

Ejemplo de expresión para RET 2.0:

```text
https://prodretencionverificacion.clouda.sat.gob.mx/?id=AAAAAAAA-BBBB-CCCC-DDDD-000000000000&re=Ñ&amp;A010101AAA&nr=0000000000000000000X&tt=123456.78&fe=qsIe6w==
```

## Instalación

Usa [composer](https://getcomposer.org/)

```shell
composer require phpcfdi/cfdi-expresiones
```

## Ejemplo básico de uso

```php
<?php
use PhpCfdi\CfdiExpresiones\DiscoverExtractor;

// creamos el extractor
$extractor = new DiscoverExtractor();

// abrimos el documento en un DOMDocument
$document = new DOMDocument();
$document->load('archivo-cfdi.xml');

// obtenemos la expresión
$expression = $extractor->extract($document);

// y también podemos obtener los valores individuales
$values = $extractor->obtain($document);
```

## Soporte

Puedes obtener soporte abriendo un ticket en Github.

Adicionalmente, esta librería pertenece a la comunidad [PhpCfdi](https://www.phpcfdi.com), así que puedes usar los
mismos canales de comunicación para obtener ayuda de algún miembro de la comunidad.

## Compatibilidad

Esta librería se mantendrá compatible con al menos la versión con
[soporte activo de PHP](https://www.php.net/supported-versions.php) más reciente.

También utilizamos [Versionado Semántico 2.0.0](docs/SEMVER.md) por lo que puedes usar esta librería
sin temor a romper tu aplicación.

### Cambiar de la versión 2.0.0 a la versión 3.0.0

La versión `3.0.0` agrega un método a la interfaz `ExpressionExtractorInterface` por lo que es necesario crear una
versión mayor. Puedes actualizar con confianza si no generaste alguna clase que implemente `ExpressionExtractorInterface`.

## Contribuciones

Las contribuciones con bienvenidas. Por favor lee [CONTRIBUTING][] para más detalles
y recuerda revisar el archivo de tareas pendientes [TODO][] y el archivo [CHANGELOG][].

## Copyright and License

The `phpcfdi/cfdi-expresiones` library is copyright © [PhpCfdi](https://www.phpcfdi.com/)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.

[contributing]: https://github.com/phpcfdi/cfdi-expresiones/blob/main/CONTRIBUTING.md
[changelog]: https://github.com/phpcfdi/cfdi-expresiones/blob/main/docs/CHANGELOG.md
[todo]: https://github.com/phpcfdi/cfdi-expresiones/blob/main/docs/TODO.md

[source]: https://github.com/phpcfdi/cfdi-expresiones
[php-version]: https://packagist.org/packages/phpcfdi/cfdi-expresiones
[discord]: https://discord.gg/aFGYXvX
[release]: https://github.com/phpcfdi/cfdi-expresiones/releases
[license]: https://github.com/phpcfdi/cfdi-expresiones/blob/main/LICENSE
[build]: https://github.com/phpcfdi/cfdi-expresiones/actions/workflows/build.yml?query=branch:main
[reliability]:https://sonarcloud.io/component_measures?id=phpcfdi_cfdi-expresiones&metric=Reliability
[maintainability]: https://sonarcloud.io/component_measures?id=phpcfdi_cfdi-expresiones&metric=Maintainability
[coverage]: https://sonarcloud.io/component_measures?id=phpcfdi_cfdi-expresiones&metric=Coverage
[violations]: https://sonarcloud.io/project/issues?id=phpcfdi_cfdi-expresiones&resolved=false
[downloads]: https://packagist.org/packages/phpcfdi/cfdi-expresiones

[badge-source]: https://img.shields.io/badge/source-phpcfdi/cfdi--expresiones-blue.svg?logo=github
[badge-php-version]: https://img.shields.io/packagist/php-v/phpcfdi/cfdi-expresiones?logo=php
[badge-discord]: https://img.shields.io/discord/459860554090283019?logo=discord
[badge-release]: https://img.shields.io/github/release/phpcfdi/cfdi-expresiones.svg??logo=git
[badge-license]: https://img.shields.io/github/license/phpcfdi/cfdi-expresiones.svg?logo=open-source-initiative
[badge-build]: https://img.shields.io/github/actions/workflow/status/phpcfdi/cfdi-expresiones/build.yml?branch=main&logo=github-actions
[badge-reliability]: https://sonarcloud.io/api/project_badges/measure?project=phpcfdi_cfdi-expresiones&metric=reliability_rating
[badge-maintainability]: https://sonarcloud.io/api/project_badges/measure?project=phpcfdi_cfdi-expresiones&metric=sqale_rating
[badge-coverage]: https://img.shields.io/sonar/coverage/phpcfdi_cfdi-expresiones/main?logo=sonarcloud&server=https%3A%2F%2Fsonarcloud.io
[badge-violations]: https://img.shields.io/sonar/violations/phpcfdi_cfdi-expresiones/main?format=long&logo=sonarcloud&server=https%3A%2F%2Fsonarcloud.io
[badge-downloads]: https://img.shields.io/packagist/dt/phpcfdi/cfdi-expresiones.svg?logo=packagist
