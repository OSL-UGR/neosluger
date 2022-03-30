<?php declare(strict_types=1); namespace NeoslugerWeb;


require_once(__DIR__."/../presenter/filter-short-link-handle.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/settings/settings.php");

ini_set("display_errors", strval(\NeoslugerSettings\DEBUG));


function page_main ()
{
	$handle = filter_short_link_handle($_POST["neosluger-inspect-string"], \NeoslugerSettings\SITE_ADDRESS);
	header("Location: /stats/".$handle, true, 301);
}


page_main();


?>
