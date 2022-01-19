<?php declare(strict_types=1);


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");


function render ()
{
	$loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT']."/templates");
	$twig   = new \Twig\Environment($loader);

	echo $twig->render("licence.html", [
		"licence_tab" => "active-tab",
	]);
}


render();


?>
