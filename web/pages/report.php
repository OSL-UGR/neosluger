<?php declare(strict_types=1); namespace NeoslugerWeb;


require_once(__DIR__."/../presenter/render.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/settings/settings.php");

ini_set("display_errors", strval(\NeoslugerSettings\DEBUG));


render("report");


?>
