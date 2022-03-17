<?php declare(strict_types=1); namespace NeoslugerWeb;


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/core/qr.php");


function read_form ()
{
	return array(
		"qr-string" => $_POST["neosluger-qr-string"],
	);
}


function render ()
{
	$loader = new \Twig\Loader\FilesystemLoader(__DIR__."/../templates");
	$twig   = new \Twig\Environment($loader);

	$form_fields = read_form();
	$qr_path     = \Neosluger\QRWrapper::from_string($form_fields["qr-string"]);

	echo $twig->render("qr-result.html", [
		"destination" => $form_fields["qr-string"],
		"qr_path"     => substr($qr_path, strlen($_SERVER["DOCUMENT_ROOT"])),
		"qr_tab"      => "active-tab",
	]);
}


render();


?>