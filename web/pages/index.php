<?php declare(strict_types=1); namespace NeoslugerWeb;


require_once(__DIR__."/../presenter/render.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/settings/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/server-helpers.php");

ini_set("display_errors", strval(\NeoslugerSettings\DEBUG));


render("index", [
	"index_tab" => "active-tab",
	"shortener_allowed" => \NeoslugerSettings\user_ip_is_allowed($_SERVER["REMOTE_ADDR"]),
]);


?>
