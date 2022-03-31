<?php declare(strict_types=1); namespace NslAPI;


require_once(__DIR__."/api-query.php");
require_once(__DIR__."/api-response.php");
require_once(__DIR__."/../settings/boundaries.php");


function api_main (): void
{
	$query = new APIQuery(
		(isset($_GET["url"])    ? $_GET["url"]    : ""),
		(isset($_GET["handle"]) ? $_GET["handle"] : ""),
	);

	$result = \NslSettings\url_boundary()->register_new_url($query->url(), $query->handle(), $_SERVER["REMOTE_ADDR"]);

	try
	{
		$response = APIResponse::from_value($result->unwrap()->full_handle());
	}
	catch (\Exception $e)
	{
		$response = APIResponse::from_error($e->__toString());
	}

	header("Content-type: application/json");
	echo $response->json_encode();
}


api_main();


?>
