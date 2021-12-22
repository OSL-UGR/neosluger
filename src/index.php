<?php declare(strict_types=1);

ini_set("display_errors", '1');
require_once("vendor/autoload.php");

function main (): void
{
	$loader = new \Twig\Loader\FilesystemLoader("templates");
	$twig   = new \Twig\Environment($loader);

	echo $twig->render("index.html", [
		"index_tab" => "active-tab",
	]);
}

main();

?>
