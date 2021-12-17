<?php

ini_set("display_errors", 1);
require_once("vendor/autoload.php");
require_once("php/url.php");

function read_form ()
{
	return array(
		"handle" => $_POST["neosluger-handle"],
		"url"    => $_POST["neosluger-url"],
	);
}

function main ()
{
	$loader = new \Twig\Loader\FilesystemLoader("templates");
	$twig   = new \Twig\Environment($loader);

	$form_fields = read_form();
	$url = new URL($form_fields["url"], $form_fields["handle"]);

	echo $twig->render("url-result.html", [
		"index_tab" => "active-tab",
		"url" => $url,
	]);
}

main();

?>
