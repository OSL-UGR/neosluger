<?php declare(strict_types=1); namespace NeoslugerWeb;


require_once(__DIR__."/../presenter/render.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/boundaries.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/settings/settings.php");

ini_set("display_errors", strval(\NeoslugerSettings\DEBUG));


function read_form (): array
{
	return array(
		"qr-string" => $_POST["neosluger-qr-string"],
	);
}


function page_main (): void
{
	$form_fields = read_form();
	$qr_path = \NeoslugerSettings\qr_boundary()->generate_qr_from_string($form_fields["qr-string"]);

	render("qr-result", [
		"destination" => $form_fields["qr-string"],
		"qr_path"     => substr($qr_path, strlen($_SERVER["DOCUMENT_ROOT"])),
		"qr_tab"      => "active-tab",
	]);
}


page_main();


?>
