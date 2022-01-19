<?php declare(strict_types=1);


const ROOT = "..";
ini_set("display_errors", '1');
require_once(ROOT."/vendor/autoload.php");


function main ()
{
	$loader = new \Twig\Loader\FilesystemLoader(ROOT."/templates");
	$twig   = new \Twig\Environment($loader);

	echo $twig->render("licence-raw.html");
}


main();


?>