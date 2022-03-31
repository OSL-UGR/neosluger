<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/../settings/locale.php");


$ERR_COULDNT_LOG = fn () => \NslSettings\localise([
	"EN" => "Could not log access to",
	"ES" => "¡No se pudo registrar acceso a",
]);


$ERR_DUPLICATE_HANDLE = fn () => \NslSettings\localise([
	"EN" => "There exists already a URL with handle",
	"ES" => "¡Ya existe un enlace con nombre",
]);


$ERR_EMPTY_LOG_PRE = fn () => \NslSettings\localise([
	"EN" => "Log for",
	"ES" => "¡El historial de",
]);
$ERR_EMPTY_LOG_POST = fn () => \NslSettings\localise([
	"EN" => "is empty!",
	"ES" => "está vacío!",
]);


$ERR_ILLEGAL_DESTINATION = fn () => \NslSettings\localise([
	"EN" => "The URL string is not an actual URL!",
	"ES" => "¡La cadena del enlace no representa un enlace!",
]);


$ERR_ILLEGAL_HANDLE_LEN = fn () => \NslSettings\localise([
	"EN" => "Custom handles must be between 5 and 50 characters long!",
	"ES" => "¡Los nombres personalizados de los enlaces deben tener entre 5 y 50 caracteres!",
]);


$ERR_ILLEGAL_IP = fn () => \NslSettings\localise([
	"EN" => "Only users from the University of Granada can create short URLs!",
	"ES" => "¡Sólo pueden crear enlaces los usuarios de la Universidad de Granada!",
]);


$ERR_LOG_NOT_FOUND = fn () => \NslSettings\localise([
	"EN" => "Could not find log for URL",
	"ES" => "No se pudo encontrar el historial del enlace",
]);


$ERR_NO_DESTINATION = fn () => \NslSettings\localise([
	"EN" => "A destination URL is required!",
	"ES" => "¡Se require un enlace de destino!",
]);


$ERR_UNWRAP = fn () => \NslSettings\localise([
	"EN" => "Fatal error when unwrapping a Result object!",
	"ES" => "¡Error fatal desempaquetando un objeto Result!",
]);


$ERR_URL_NOT_INSERTED = fn () => \NslSettings\localise([
	"EN" => "There was an error inserting the URL in the system!",
	"ES" => "¡Se encontró un error al insertar el enlace en el sistema!",
]);


?>
