<?php declare(strict_types=1); namespace NslWeb;


require_once(__DIR__."/../presenter/render.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/settings/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/server-helpers.php");

ini_set("display_errors", strval(\NslSettings\DEBUG));


render("index", [
	"index_tab" => "active-tab",
	"shortener_allowed" => \NslSettings\user_ip_is_allowed($_SERVER["REMOTE_ADDR"]),
]);


?>
