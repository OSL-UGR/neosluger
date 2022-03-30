<?php declare(strict_types=1); namespace NslAPI;


require_once(__DIR__."/api-query.php");
require_once(__DIR__."/api-response.php");
require_once(__DIR__."/strings.php");
require_once(__DIR__."/../settings/boundaries.php");
require_once(__DIR__."/../settings/server-helpers.php");


function process_api_query (APIQuery $query): APIResponse
{
	global $ERR_INVALID_IP;

	$response = APIResponse::from_error($ERR_INVALID_IP());

	if (\NslSettings\user_ip_is_allowed($_SERVER["REMOTE_ADDR"]))
	{
		$result = \NslSettings\url_boundary()->register_new_url($query->url(), $query->handle());

		try
		{
			$response = APIResponse::from_value($result->unwrap()->full_handle());
		}
		catch (\Exception $e)
		{
			$response = APIResponse::from_error($e->__toString());
		}
	}

	return $response;
}

?>
