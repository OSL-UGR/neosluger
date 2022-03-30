<?php declare(strict_types=1); namespace NslAPI;


final class APIQuery
{
	private string $handle;
	private string $url;


	public function __construct (string $url, string $handle)
	{
		$this->handle = $handle;
		$this->url    = $url;
	}


	public function handle (): string
	{
		return $this->handle;
	}


	public function url (): string
	{
		return $this->url;
	}
};


?>
