<?php declare(strict_types=1);


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/url.php");


use MongoDB\Client as Mongo;


function find_page (string $uri): string
{
	$path   = $_SERVER['DOCUMENT_ROOT']."/pages/".$uri.".php";
	$result = "";

	if (file_exists($path))
		$result = $path;

	return $result;
}


function get_url_from_database (string $handle): URL
{
	$mongo      = new Mongo("mongodb://localhost:27017");
	$collection = $mongo->neosluger->urls;
	$result     = $collection->find(["handle" => $handle])->toArray();
	$url        = URL::from_null();

	if (count($result) > 0)
		$url = URL::from_db_result($result[0]);

	return $url;
}


function parse_request_uri_first_item (): string
{
	// Study this regex with https://regex101.com/. Absolutely recommended!
	return preg_replace("/^\/([^\/?]+).*/", "$1", $_SERVER["REQUEST_URI"]);
}


function try_old_api_or_404 (): void
{
	if (str_starts_with($_SERVER["REQUEST_URI"], "/sluger.php"))
		echo json_encode([
			"url"   => "",
			"error" => "0",
			"text"  => "This API is deprecated. Read the docs to update it!"
		]);
	else
		include($_SERVER['DOCUMENT_ROOT']."/pages/404.php");
}


function main (): void
{
	$uri  = parse_request_uri_first_item();
	$path = find_page($uri);

	if (!empty($path))
	{
		include($path);
	}
	else
	{
		$url = get_url_from_database($uri);

		if ($url->is_null())
			try_old_api_or_404();
		else
			header("Location: " . $url->destination(), true,  301);
	}

}


main();


?>
