<?php declare(strict_types=1); namespace NslLocale;


require_once(__DIR__."/../settings/locale.php");

$ERR_CORE_ERROR = fn () => \NslSettings\localise([
	"EN" => "An error in the service prevented us from registering your URL!",
	"ES" => "¡Un error en el servicio nos impidió registrar tu enlace!",
]);


$ERR_INVALID_HANDLE_LEN = fn () => \NslSettings\localise([
	"EN" => "Custom handles must be between 5 and 50 characters long!",
	"ES" => "¡Los nombres personalizados de los enlaces deben tener entre 5 y 50 caracteres!",
]);


$ERR_INVALID_IP = fn () => \NslSettings\localise([
	"EN" => "Only users from the University of Granada can create short URLs!",
	"ES" => "¡Sólo pueden crear enlaces los usuarios de la Universidad de Granada!",
]);


$ERR_INVALID_URL = fn () => \NslSettings\localise([
	"EN" => "The URL string is not an actual URL!",
	"ES" => "¡La cadena del enlace no representa un enlace!",
]);


$ERR_NO_URL = fn () => \NslSettings\localise([
	"EN" => "A URL is required!",
	"ES" => "¡Se require un enlace!",
]);


?>
