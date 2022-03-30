<?php declare(strict_types=1); namespace NslWeb;


require_once(__DIR__."/../presenter/render.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/settings/settings.php");

ini_set("display_errors", strval(\NslSettings\DEBUG));


render("report");


?>
