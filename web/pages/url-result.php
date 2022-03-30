<?php declare(strict_types=1); namespace NeoslugerWeb; ini_set("display_errors", '1');


require_once(__DIR__."/../presenter/render.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/boundaries.php");


function read_form (): array
{
	return array(
		"handle" => empty($_POST["neosluger-handle"]) ? "" : $_POST["neosluger-handle"],
		"url"    => $_POST["neosluger-url"],
	);
}


function page_main (): void
{
	$qr_path = "";
	$url = null;
	$form_fields = read_form();
	$register_result = \NeoslugerSettings\url_boundary()->register_new_url($form_fields["url"], $form_fields["handle"]);

	if ($register_result->ok())
	{
		$url = $register_result->unwrap();
		$qr_path = \NeoslugerSettings\qr_boundary()->generate_qr_from_string($url->full_handle());
	}

	render("url-result", [
		"destination" => $form_fields["url"],
		"handle"      => $form_fields["handle"],
		"index_tab"   => "active-tab",
		"url"         => $url,
		"qr_path"     => substr($qr_path, strlen($_SERVER["DOCUMENT_ROOT"])),
	]);
}


page_main();


?>
