<?php declare(strict_types=1);


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");


function render (): void
{
	$loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT']."/templates");
	$twig   = new \Twig\Environment($loader);

	echo $twig->render("qr.html", [
		"qr_tab" => "active-tab",
	]);
}


render();


?>
