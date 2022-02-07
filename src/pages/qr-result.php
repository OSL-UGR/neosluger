<?php declare(strict_types=1);


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
use chillerlan\QRCode\{QRCode, QROptions};


function generate_qr (string $qr_string)
{
	$cache_directory = "../cache/qr";

	if (!file_exists($cache_directory))
		mkdir($cache_directory, 0775, true);

	$qr_path    = $cache_directory."/qr-" . substr(sha1($qr_string), 0, 4) . ".png";
	$qr_options = new QROptions([
		"outputType"       => QRCode::OUTPUT_IMAGE_PNG,
		"eccLevel"         => QRCode::ECC_L,
		"imageTransparent" => false,
		"pngCompression"   => 9,
	]);

	try
	{
		(new QRCode($qr_options))->render($qr_string, $qr_path);
	}
	catch (Exception $e)
	{
		error_log($e->__toString());
		$qr_path = "";
	}

	return $qr_path;
}


function read_form ()
{
	return array(
		"qr-string" => $_POST["neosluger-qr-string"],
	);
}


function render ()
{
	$loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT']."/templates");
	$twig   = new \Twig\Environment($loader);

	$form_fields = read_form();
	$qr_path     = generate_qr($form_fields["qr-string"]);

	echo $twig->render("qr-result.html", [
		"destination" => $form_fields["qr-string"],
		"qr_path"     => $qr_path,
		"qr_tab"      => "active-tab",
	]);
}


render();


?>
