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

The server settings used by Neosluger can be found in `conf/neosluger.conf`.
The heavy lifting is done by `@extensionless-urls`:

```nginx
location @extensionless-urls {
	rewrite (\/(api|help|index|licence|url-result))$ $1.php last;
	rewrite \/([a-zA-Z0-9]+)$ /short-url.php last;
}
```

This `location` directive pattern matches the URL in two cases:

- **`(\/(api|help|index|licence|url-result))$`:** This matches the pages available on the web server. All pages are translated to their `*.php` equivalent, so they look cleaner in the URL bar.
- **`\/([a-zA-Z0-9\-_]+)$`:** This matches all other possible URLs, which are the shortened ones. They are redirected to `short-url.php` so that the script can redirect the user to the destination site or serve them a 404 page in case the handle cannot be found in the system.

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

La configuración del servidor usada por Neosluger puede encontrarse en `conf/neosluger.conf`.
La función `@extensionless-urls` es la que se carga la mayor parte del trabajo:

```nginx
location @extensionless-urls {
	rewrite (\/(api|help|index|licence|url-result))$ $1.php last;
	rewrite \/([a-zA-Z0-9]+)$ /short-url.php last;
}
```

Esta directiva `location` busca patrones coincidentes en dos casos:

- **`(\/(api|help|index|licence|url-result))$`:** Encuentra las páginas disponibles en el servidor web. Todas las páginas se traducen a su equivalente `*.php`, de forma que se ven más limpias en la barra de la URL.
- **`\/([a-zA-Z0-9\-_]+)$`:** Encuentra el resto de URLs posibles, que son las acortadas. Se redirigen a `short-url.php` para que el script pueda redirigir al usuario al sitio de destino o servirles una página 404 en caso de que la URL no se encuentre en el sistema.

Para instalar el servidor, copia `conf/neosluger.conf` en `/etc/nginx/sites-enabled/neosluger.conf` y añade la siguiente directiva en `/etc/nginx/nginx.conf`:

```nginx
http {
	# Añade esta directiva include en el cuerpo del bloque http ya existente:
	include /etc/nginx/sites-enabled/neosluger.conf;
}
```
