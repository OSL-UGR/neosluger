<?php declare(strict_types=1); namespace NeoslugerWeb;


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/core/qr.php");


function read_form ()
{
	return array(
		"handle" => empty($_POST["neosluger-handle"]) ? "" : $_POST["neosluger-handle"],
		"url"    => $_POST["neosluger-url"],
	);
}


function render ()
{
	$loader = new \Twig\Loader\FilesystemLoader(__DIR__."/../templates");
	$twig   = new \Twig\Environment($loader);

	$form_fields = read_form();
	$url         = \Neosluger\URL::from_form($form_fields["url"], $form_fields["handle"]);
	$qr_path     = \Neosluger\QRWrapper::from_url($url);

	echo $twig->render("url-result.html", [
		"destination" => $form_fields["url"],
		"handle"      => $form_fields["handle"],
		"index_tab"   => "active-tab",
		"url"         => $url,
		"qr_path"     => substr($qr_path, strlen($_SERVER["DOCUMENT_ROOT"])),
	]);
}


render();


?>
