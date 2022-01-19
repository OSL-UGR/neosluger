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

## Server settings

An example server settings file used by Neosluger can be found in `conf/neosluger.conf`.
To install the server, copy `conf/neosluger.conf` to `/etc/nginx/sites-enabled/neosluger.conf` and add the following directive in `/etc/nginx/nginx.conf`:

```nginx
http {
	# Add this include directive in the body of the already existing http block:
	include /etc/nginx/sites-enabled/neosluger.conf;
}
```

## Cache directory

QR codes are saved in a cache directory to be presented to the user and be downloaded by them.
Its route is set in [`url-result.php`](https://github.com/OSL-UGR/neosluger/blob/main/src/url-result.php#L16) and defaults to `src/cache/qr/`.
You may be forbidden from writing into this directory if Neosluger is hosted in `/usr/`.
If that is the case, please refer to [this ServerFault answer](https://serverfault.com/a/997496).

To use the directory, you need to give the user running PHP permission to write into it.
Neosluger will create the cache directory if it does not exist, so you should give write permissions to `src/`.
This may look like the following:

```sh
chown -R "$USER":http src/
chmod -R g+w src/
```

# Neosluger (Español)

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
Su ruta está declarada en [`url-result.php`](https://github.com/OSL-UGR/neosluger/blob/main/src/url-result.php#L16) y es `src/cache/qr/`.
Puede que tengas prohibido escribir en este directorio si has alojado Neosluger en `/usr/`.
En ese caso, consulta [esta respuesta de ServerFault]((https://serverfault.com/a/997496).

Para usar este directorio necesitarás proporcionarle permisos de escritura sobre él al usuario que ejecuta PHP.
Neoslugger intentará crear el directorio de caché si no existe, así que deberías darle permisos de escritura en `src/`:
Puedes hacerlo con una orden similar a la siguiente:

```sh
chown -R "$USER":http src/
chmod -R g+w src/
```

## Configuración del servidor

Un fichero de ejemplo de la configuración del servidor usada por Neosluger puede encontrarse en `conf/neosluger.conf`.
Para instalar el servidor, copia `conf/neosluger.conf` en `/etc/nginx/sites-enabled/neosluger.conf` y añade la siguiente directiva en `/etc/nginx/nginx.conf`:

```nginx
http {
	# Añade esta directiva include en el cuerpo del bloque http ya existente:
	include /etc/nginx/sites-enabled/neosluger.conf;
}
```
