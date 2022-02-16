<?php declare(strict_types=1);


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/qr.php");


function render ()
{
	$loader = new \Twig\Loader\FilesystemLoader($_SERVER['DOCUMENT_ROOT']."/templates");
	$twig   = new \Twig\Environment($loader);

	$handle = Neosluger\parse_request_uri_nth_item(2);
	$url    = URL::from_database($handle);

	if ($url->is_null())
	{
		echo $twig->render("stats.html", [
			"error" => true,
		]);
	}
	else
	{
		$qr_path  = QRWrapper::from_url($url);
		$url_logs = Neosluger\LOG_COLLECTION()->find(["handle" => $url->handle()])->toArray()[0];
		$accesses = $url_logs["accesses"];
		$creation = new DateTime($accesses[0]);

		echo $twig->render("stats.html", [
			"accesses"      => count($accesses) - 1,
			"creation_date" => $creation->format("Y-m-d"),
			"creation_time" => $creation->format("H:i:s"),
			"url"           => $url,
			"qr_path"       => $qr_path,
		]);
	}
}


render();


?>
