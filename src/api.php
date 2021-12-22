<?php declare(strict_types=1);

ini_set("display_errors", '1');
require_once("vendor/autoload.php");

function main ()
{
	$loader = new \Twig\Loader\FilesystemLoader("templates");
	$twig   = new \Twig\Environment($loader);

	echo $twig->render("api.html", [
		"api_tab" => "active-tab",
	]);
}

main();

?>
