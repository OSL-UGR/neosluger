<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/../settings/locale.php");


$ERR_COULDNT_LOG = fn () => \NslSettings\localise([
	"EN" => "Could not log access to",
	"ES" => "¡No se pudo registrar acceso a",
]);


$ERR_EMPTY_LOG_PRE = fn () => \NslSettings\localise([
	"EN" => "Log for",
	"ES" => "¡El historial de",
]);
$ERR_EMPTY_LOG_POST = fn () => \NslSettings\localise([
	"EN" => "is empty!",
	"ES" => "está vacío!",
]);


$ERR_INVALID_HANDLE_LEN = fn () => \NslSettings\localise([
	"EN" => "Custom handles must be between 5 and 50 characters long!",
	"ES" => "¡Los nombre de los enlaces deben tener entre 5 y 50 caracteres!",
]);


$ERR_LOG_NOT_FOUND = fn () => \NslSettings\localise([
	"EN" => "Could not find log for URL",
	"ES" => "No se pudo encontrar el historial del enlace",
]);


$ERR_UNWRAP = fn () => \NslSettings\localise([
	"EN" => "Fatal error when unwrapping a Result object!",
	"ES" => "¡Error fatal desempaquetando un objeto Result!",
]);


$ERR_URL_NOT_FOUND = fn () => \NslSettings\localise([
	"EN" => "The URL wasn't found in the system!",
	"ES" => "¡No se encontró el enlace en el sistema!",
]);


$ERR_URL_NOT_INSERTED = fn () => \NslSettings\localise([
	"EN" => "There was an error inserting the URL in the system!",
	"ES" => "¡Se encontró un error al insertar el enlace en el sistema!",
]);


?>
