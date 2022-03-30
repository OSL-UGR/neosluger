<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/../settings/locale.php");


$ERR_COULDNT_LOG = fn () => \NslLocale\localize([
	"EN" => "Could not log access to",
	"ES" => "¡No se pudo registrar acceso a",
]);


$ERR_EMPTY_LOG_PRE = fn () => \NslLocale\localize([
	"EN" => "Log for",
	"ES" => "¡El historial de",
]);
$ERR_EMPTY_LOG_POST = fn () => \NslLocale\localize([
	"EN" => "is empty!",
	"ES" => "está vacío!",
]);


$ERR_INVALID_HANDLE_LEN = fn () => \NslLocale\localize([
	"EN" => "Custom handles must be between 5 and 50 characters long!",
	"ES" => "¡Los nombre de los enlaces deben tener entre 5 y 50 caracteres!",
]);


$ERR_LOG_NOT_FOUND = fn () => \NslLocale\localize([
	"EN" => "Could not find log for URL",
	"ES" => "No se pudo encontrar el historial del enlace",
]);


$ERR_UNWRAP = fn () => \NslLocale\localize([
	"EN" => "Fatal error when unwrapping a Result object!",
	"ES" => "¡Error fatal desempaquetando un objeto Result!",
]);


$ERR_URL_NOT_FOUND = fn () => \NslLocale\localize([
	"EN" => "The URL wasn't found in the system!",
	"ES" => "¡No se encontró el enlace en el sistema!",
]);


$ERR_URL_NOT_INSERTED = fn () => \NslLocale\localize([
	"EN" => "There was an error inserting the URL in the system!",
	"ES" => "¡Se encontró un error al insertar el enlace en el sistema!",
]);


?>
