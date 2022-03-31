<?php declare(strict_types=1); namespace NslDB;


require_once(__DIR__."/../settings/locale.php");


$ERR_COULDNT_LOG = fn () =>\NslSettings\localise([
	"EN" => "Could not log URL access for",
	"ES" => "¡No se pudo registrar el acceso al enlace",
]);


$ERR_DATABASE_OFFLINE = fn () =>\NslSettings\localise([
	"EN" => "Critical error connecting to the database! Is it even on?",
	"ES" => "¡Error crítico al conectarse a la base de datos! ¿Está encendida siquiera?",
]);


$ERR_LOG_NOT_FOUND = fn () =>\NslSettings\localise([
	"EN" => "Could not find log for URL",
	"ES" => "¡No se pudo encontrar el historial del enlace",
]);


$ERR_URL_NOT_FOUND = fn () => \NslSettings\localise([
	"EN" => "Could not find URL with hanle",
	"ES" => "¡No se pudo encontrar el enlace con nombre",
]);


$ERR_REGISTERING_FAILED_LOG = fn () =>\NslSettings\localise([
	"EN" => "Could not register log for URL",
	"ES" => "¡No se pudo registrar un historial para el enlace",
]);


$ERR_REGISTERING_FAILED_URL = fn () =>\NslSettings\localise([
	"EN" => "Could not register URL",
	"ES" => "¡No se pudo registrar el enlace",
]);


$ERR_ZERO_RESULTS_LOG = fn () => \NslSettings\localise([
	"EN" => "Zero log results were retrieved for URL",
	"ES" => "¡No se encontraron resultados para un historial del enlace",
]);


$ERR_ZERO_RESULTS_URL = fn () => \NslSettings\localise([
	"EN" => "Zero URL results were retrieved for handle",
	"ES" => "¡No se encontraron resultados para un enlace con nombre",
]);


?>
