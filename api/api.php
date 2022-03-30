<?php declare(strict_types=1); namespace NslAPI;


require_once(__DIR__."/process-api-query.php");


function api_main (): void
{
	$query = new APIQuery(
		(isset($_GET["url"])    ? $_GET["url"]    : ""),
		(isset($_GET["handle"]) ? $_GET["handle"] : ""),
	);

	$response = process_api_query($query);

	header("Content-type: application/json");
	echo $response->json_encode();
}


api_main();


?>
