<?php declare(strict_types=1); namespace NeoslugerWeb;


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/core/const.php");


function render (): void
{
	$loader = new \Twig\Loader\FilesystemLoader(__DIR__."/../templates");
	$twig   = new \Twig\Environment($loader);

	echo $twig->render("index.html", [
		"index_tab" => "active-tab",
		"shortener_allowed" => \Neosluger\user_ip_is_allowed(),
	]);
}


render();


?>
