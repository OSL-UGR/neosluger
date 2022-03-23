<?php declare(strict_types=1); namespace NeoslugerWeb;


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/core/api.php");
require_once($_SERVER['DOCUMENT_ROOT']."/core/url.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings.php");


function process_api_petition (): void
{
	$query = new \Neosluger\APIQuery(
		array_key_exists("url",    $_GET) ? $_GET["url"]    : "",
		array_key_exists("handle", $_GET) ? $_GET["handle"] : "",
	);
	$response = \Neosluger\API::process($query);

	header("Content-type: application/json");
	echo $response->json_encode();
}


process_api_petition();


?>
