<?php declare(strict_types=1);


const ROOT = "..";
ini_set("display_errors", '1');
require_once(ROOT."/vendor/autoload.php");
require_once(ROOT."/php/url.php");


use chillerlan\QRCode\{QRCode, QROptions};


function generate_qr (URL $url)
{
	$cache_directory = ROOT."/cache/qr";

	if (!file_exists($cache_directory))
		mkdir($cache_directory, 0775, true);

	$qr_path    = $cache_directory."/qr-" . $url->handle() . ".png";
	$qr_options = new QROptions([
		"outputType"       => QRCode::OUTPUT_IMAGE_PNG,
		"eccLevel"         => QRCode::ECC_L,
		"imageTransparent" => false,
		"pngCompression"   => 9,
	]);

	(new QRCode($qr_options))->render($url->full_handle(), $qr_path);
	return $qr_path;
}


function read_form ()
{
	return array(
		"handle" => $_POST["neosluger-handle"],
		"url"    => $_POST["neosluger-url"],
	);
}


function main ()
{
	$loader = new \Twig\Loader\FilesystemLoader(ROOT."/templates");
	$twig   = new \Twig\Environment($loader);

	$form_fields = read_form();
	$url         = URL::from_form($form_fields["url"], $form_fields["handle"]);
	$qr_path     = generate_qr($url);

	echo $twig->render("url-result.html", [
		"index_tab" => "active-tab",
		"url"       => $url,
		"qr_path"   => $qr_path,
	]);
}


main();


?>