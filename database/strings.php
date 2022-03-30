<?php declare(strict_types=1); namespace NslDB;


require_once(__DIR__."/../settings/locale.php");


$ERR_COULDNT_LOG = fn () =>\NslLocale\localize([
	"EN" => "Could not log URL access for",
	"ES" => "¡No se pudo registrar el acceso al enlace",
]);


$ERR_COULDNT_REGISTER = fn () =>\NslLocale\localize([
	"EN" => "Could not register URL",
	"ES" => "¡No se pudo registrar el enlace",
]);


$ERR_DATABASE_OFFLINE = fn () =>\NslLocale\localize([
	"EN" => "Critical error connecting to the database! Is it even on?",
	"ES" => "¡Error crítico al conectarse a la base de datos! ¿Está encendida siquiera?",
]);


$ERR_LOG_NOT_FOUND = fn () =>\NslLocale\localize([
	"EN" => "Could not find log for URL",
	"ES" => "¡No se pudo encontrar el historial del enlace",
]);


$ERR_URL_NOT_FOUND = fn () => \NslLocale\localize([
	"EN" => "Could not find URL with hanle",
	"ES" => "¡No se pudo encontrar el enlace con nombre",
]);


$ERR_REGISTERING_FAILED_LOG_PRE = fn () =>\NslLocale\localize([
	"EN" => "Registration of logs for URL",
	"ES" => "¡El registro de un historial para el enlace",
]);
$ERR_REGISTERING_FAILED_URL_PRE = fn () =>\NslLocale\localize([
	"EN" => "Registration of URL",
	"ES" => "¡El registro del enlace",
]);
$ERR_REGISTERING_FAILED_POST = fn () =>\NslLocale\localize([
	"EN" => "failed!",
	"ES" => "falló!",
]);


$ERR_ZERO_RESULTS_LOG_PRE = fn () => \NslLocale\localize([
	"EN" => "Log lookup for URL",
	"ES" => "¡La búsqueda del historial del enlace",
]);
$ERR_ZERO_RESULTS_URL_PRE = fn () => \NslLocale\localize([
	"EN" => "URL lookup with handle",
	"ES" => "¡La búsqueda de un enlace con nombre",
]);
$ERR_ZERO_RESULTS_POST = fn () => \NslLocale\localize([
	"EN" => "returned zero results!",
	"ES" => "no devolvió resultados!",
]);


?>
