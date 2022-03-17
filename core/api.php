<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/const.php");


/** @class APIQuery
  * @brief Collection of items passed by the user to the API when calling it
  *
  * Even though this class could be substituted by an associative array, it is
  * interesting to keep it because it makes it impossible to tamper with the
  * arguments after construction, allowing for better const correctness.
  */

class APIQuery
{
	private string $handle = "";
	private string $url    = "";


	public function __construct (string $url = "", string $handle = "")
	{
		$this->url    = $url;
		$this->handle = $handle;
	}


	public function handle (): string
	{
		return $this->handle;
	}


	public function url (): string
	{
		return $this->url;
	}
}


/** @class APIResponse
  * @brief Collection of items passed by the API as a response to the user
  *
  * Even though this class could be substituted by an associative array, it is
  * interesting to keep it because it makes it impossible to tamper with the
  * arguments after construction, allowing for better const correctness.
  */

class APIResponse
{
	const MSG_DUPLICATE_HANDLE   = "A URL with your handle already exists!";
	const MSG_INVALID_HANDLE_LEN = "Custom handles must be between 5 and 50 characters long!";
	const MSG_INVALID_IP         = "Only users from the University of Granada can create short URLs!";
	const MSG_INVALID_URL        = "The URL string is not an actual URL!";
	const MSG_NO_URL             = "A URL is required!";


	private string $errormsg = "";
	private string $url = "";
	private bool $success = false;


	public function __construct (string $url = "", string $errormsg = "")
	{
		if (empty($url))
		{
			if (empty($errormsg))
				$this->errormsg = APIResponse::MSG_NO_URL;
			else
				$this->errormsg = $errormsg;
		}
		else
		{
			$this->url = $url;
			$this->success = true;
		}
	}


	public function errormsg (): string
	{
		return $this->errormsg;
	}


	public function success (): bool
	{
		return $this->success;
	}


	public function url (): string
	{
		return $this->url;
	}


	public function json_encode (): string
	{
		return json_encode([
			"url"      => $this->url,
			"success"  => $this->success,
			"errormsg" => $this->errormsg,
		]);
	}
}


/** @class API
  * @brief Static functions that process the users' queries.
  *
  * Instead of using a namespace, we prefer a class with static functions which
  * can limit their access for future maintainers.
  */

class API
{
	private static function get_error_reason (APIQuery $query, URL $url): APIResponse
	{
		$handle       = $query->handle();
		$handle_len   = strlen($handle);
		$response     = null;
		$valid_handle = (
			empty($handle) ||
			(MIN_HANDLE_LEN <= $handle_len && $handle_len <= MAX_HANDLE_LEN)
		);

		if ($valid_handle)
		{
			if ($url->is_duplicate())
				$response = new APIResponse("", APIResponse::MSG_DUPLICATE_HANDLE);
			else
				$response = new APIResponse("", APIResponse::MSG_INVALID_URL);
		}
		else
		{
			$response = new APIResponse("", APIResponse::MSG_INVALID_HANDLE_LEN);
		}

		return $response;
	}


	public static function process($query): APIResponse
	{
		$response = new APIResponse();

		if (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']) && !user_ip_is_allowed())
		{
			$response = new APIResponse("", APIResponse::MSG_INVALID_IP);
		}
		else if (!empty($query->url()))
		{
			$url = URL::from_form($query->url(), $query->handle());

			if ($url->is_null())
				$response = API::get_error_reason($query, $url);
			else
				$response = new APIResponse(SITE_ADDRESS . $url->handle());
		}

		return $response;
	}
}


?>
