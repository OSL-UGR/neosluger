<?php declare(strict_types=1);


class NewAPIQuery
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


class NewAPIResponse
{
	const MSG_DUPLICATE_HANDLE = "A URL with your handle already exists!";
	const MSG_INVALID_URL      = "The URL string is not an actual URL!";
	const MSG_NO_URL           = "A URL is required!";


	private string $errormsg = "";
	private string $url = "";
	private bool $success = false;


	public function __construct (string $url = "", string $errormsg = "")
	{
		if (empty($url))
		{
			if (empty($errormsg))
				$this->errormsg = NewAPIResponse::MSG_NO_URL;
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


class NewAPI
{
	public static function process($query): NewAPIResponse
	{
		$response = new NewAPIResponse();

		if (!empty($query->url()))
		{
			$url = URL::from_form($query->url(), $query->handle());

			if ($url->is_null())
			{
				if ($url->is_duplicate())
					$response = new NewAPIResponse("", NewAPIResponse::MSG_DUPLICATE_HANDLE);
				else
					$response = new NewAPIResponse("", NewAPIResponse::MSG_INVALID_URL);
			}
			else
			{
				$response = new NewAPIResponse(Neosluger\SITE_ADDRESS . $url->handle());
			}
		}

		return $response;
	}
}


?>
