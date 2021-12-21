# Neosluger

**University of Granada's URL shortener.**

[Spanish](#Español)

This proyect is a complete rewrite of [sluger](https://github.com/psicobyte/sluger), a tool created by Psicobyte and other members of the Office for Free Software of the University of Granada.

## Dependencies

This site requires [Twig](https://github.com/twigphp/Twig) to render the templated pages and [php-qrcode](https://github.com/chillerlan/php-qrcode) to create QR codes.
It aditionally requires [PHPUnit](https://github.com/sebastianbergmann/phpunit) to run the test suites.

You can easily install them with `composer`:

```sh
cd src/
composer install
```

All dependencies will be installed in `src/vendor/`.

## Cache directory

QR codes are saved in a cache directory to be presented to the user and be downloaded by them.
Its route is set in [`url-result.php`](https://github.com/OSL-UGR/neosluger/blob/main/src/url-result.php#L11) and defaults to `src/cache/qr/`.
You may be forbidden from writing into this directory if Neosluger is hosted in `/usr/`.
If that is the case, please refer to [this ServerFault answer](https://serverfault.com/a/997496).

# Español

**Acortador de URLs de la Universidad de Granada.**

Este proyecto es una reescritura desde cero de [sluger](https://github.com/psicobyte/sluger), una herramienta creada por Psicobyte y otros miembros de la Oficina del Software Libre de la Universidad de Granada.

## Dependencias

Este sitio requiere [Twig](https://github.com/twigphp/Twig) para mostrar las plantillas de las páginas y [php-qrcode](https://github.com/chillerlan/php-qrcode) para crear códigos QR.
También requiere [PHPUnit](https://github.com/sebastianbergmann/phpunit) para ejecutar las suites de tests.

Puedes instalar estos paquetes fácilmente con `composer`:

```sh
cd src/
composer install
```

Todas las dependencias se instalarán en `src/vendor/`.

## Directorio de caché

Los códigos QR se guardan en un directorio de caché para mostrarlos al usuario y que éste los descargue.
Su ruta está declarada en [`url-result.php`](https://github.com/OSL-UGR/neosluger/blob/main/src/url-result.php#L11) y es `src/cache/qr/`.
Puede que tengas prohibido escribir en este directorio si has alojado Neosluger en `/usr/`.
En ese caso, consulta [esta respuesta de ServerFault]((https://serverfault.com/a/997496).
