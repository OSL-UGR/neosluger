<?php declare(strict_types=1);


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/const.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/api.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/url.php");


function process_api_petition (): void
{
	$query = new APIQuery(
		array_key_exists("url",    $_GET) ? $_GET["url"]    : "",
		array_key_exists("handle", $_GET) ? $_GET["handle"] : "",
	);
	$response = API::process($query);

	header("Content-type: application/json");
	echo $response->json_encode();
}


process_api_petition();


?>
