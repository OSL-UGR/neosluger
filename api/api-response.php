<?php declare(strict_types=1); namespace NslAPI;


final class APIResponse
{
	private bool $success;
	private string $error;
	private string $url;

	private function __construct (string $url, bool $success, string $error)
	{
		$this->success = $success;
		$this->error   = $error;
		$this->url     = $url;
	}


	public static function from_error (string $error): APIResponse
	{
		return new APIResponse("", false, $error);
	}


	public static function from_value (string $url): APIResponse
	{
		return new APIResponse($url, true, "");
	}


	public function error (): string
	{
		return $this->error;
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
			"url"     => $this->url,
			"success" => $this->success,
			"error"   => $this->error,
		]);
	}
}


?>
