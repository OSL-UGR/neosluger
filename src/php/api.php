<?php declare(strict_types=1);


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


class APIResponse
{
	const MSG_DUPLICATE_HANDLE   = "A URL with your handle already exists!";
	const MSG_INVALID_HANDLE_LEN = "Custom handles must be between 5 and 50 characters long!";
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


class API
{
	private static function get_error_reason (APIQuery $query, URL $url): APIResponse
	{
		$handle       = $query->handle();
		$handle_len   = strlen($handle);
		$response     = null;
		$valid_handle = (
			empty($handle) ||
			(URL::MIN_HANDLE_LEN <= $handle_len && $handle_len <= URL::MAX_HANDLE_LEN)
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

		if (!empty($query->url()))
		{
			$url = URL::from_form($query->url(), $query->handle());

			if ($url->is_null())
				$response = API::get_error_reason($query, $url);
			else
				$response = new APIResponse(Neosluger\SITE_ADDRESS . $url->handle());
		}

		return $response;
	}
}


?>
