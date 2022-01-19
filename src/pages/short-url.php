<?php declare(strict_types=1);


const ROOT = "..";
ini_set("display_errors", '1');
require_once(ROOT."/vendor/autoload.php");
require_once(ROOT."/php/url.php");


use MongoDB\Client as Mongo;


function get_url_from_database (): URL
{
	$handle     = parse_short_url_handle();
	$mongo      = new Mongo("mongodb://localhost:27017");
	$collection = $mongo->neosluger->urls;
	$result     = $collection->find(["handle" => $handle])->toArray();
	$url        = URL::from_null();

	if (count($result) > 0)
		$url = URL::from_db_result($result[0]);

	return $url;
}


function parse_short_url_handle (): string
{
	// Study this regex with https://regex101.com/. Absolutely recommended!
	return preg_replace("/^\/([^\/]+).*/", "$1", $_SERVER["REQUEST_URI"]);
}


function main (): void
{
	$loader = new \Twig\Loader\FilesystemLoader(ROOT."/templates");
	$twig   = new \Twig\Environment($loader);

	$url = get_url_from_database();

	if ($url->is_null())
	{
		echo $twig->render("404.html");
	}
	else
	{
		header("Location: " . $url->destination(), true,  301);
	}
}


main();


?>
