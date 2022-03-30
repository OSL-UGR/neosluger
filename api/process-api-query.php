<?php declare(strict_types=1); namespace NslAPI;


require_once(__DIR__."/api-query.php");
require_once(__DIR__."/api-response.php");
require_once(__DIR__."/strings.php");
require_once(__DIR__."/../settings/boundaries.php");
require_once(__DIR__."/../settings/server-helpers.php");


function process_api_query (APIQuery $query): APIResponse
{
	global $ERR_CORE_ERROR, $ERR_INVALID_HANDLE_LEN, $ERR_INVALID_IP, $ERR_INVALID_URL, $ERR_NO_URL;

	$response = APIResponse::from_error("");
	$errormsg = null;

	if (\NslSettings\user_ip_is_allowed($_SERVER["REMOTE_ADDR"]))
	{
		if (empty($query->url()))
			$errormsg = $ERR_NO_URL;
		else if (!filter_var($query->url(), FILTER_VALIDATE_URL))
			$errormsg = $ERR_INVALID_URL;
		else
		{
			$handle_len = strlen($query->handle());
			$valid_handle = ($handle_len === 0 || (\NslSettings\MIN_HANDLE_LEN <= $handle_len && $handle_len <= \NslSettings\MAX_HANDLE_LEN));

			if (!$valid_handle)
				$errormsg = $ERR_INVALID_HANDLE_LEN;
			else
			{
				$result = \NslSettings\url_boundary()->register_new_url($query->url(), $query->handle());

				if ($result->ok())
					$response = APIResponse::from_value($result->unwrap()->full_handle());
				else
					$errormsg = $ERR_CORE_ERROR;
			}
		}
	}
	else
		$errormsg = $ERR_INVALID_IP;

	if (!is_null($errormsg))
		$response = APIResponse::from_error($errormsg());

	return $response;
}

?>
