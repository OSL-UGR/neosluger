<?php declare(strict_types=1); namespace NslLocale;


require_once(__DIR__."/../settings/locale.php");


$ERR_INVALID_IP = fn () => \NslSettings\localise([
	"EN" => "Only users from the University of Granada can create short URLs!",
	"ES" => "¡Sólo pueden crear enlaces los usuarios de la Universidad de Granada!",
]);


?>
