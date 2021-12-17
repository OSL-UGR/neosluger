<?php declare(strict_types=1);

final class URL
{
	const HASH_LENGTH = 6;

	private DateTime $creation_datetime;
	private string $handle = "";
	private string $destination  = "";
	private string $password = "";

	public function __construct (string $destination, string $handle = "", string $password = "")
	{
		if (!filter_var($destination, FILTER_VALIDATE_URL))
			throw new InvalidArgumentException("'$destination' is not an URL!");

		$this->creation_datetime = new DateTime("NOW");
		$this->destination = $destination;

		if (!empty($handle))
			$this->handle = $handle;
		else
			$this->handle = substr(
				hash("sha1", $this->creation_datetime->format("Y-m-d H:i:s.u") . $this->destination),
				0, URL::HASH_LENGTH
			);

		if (!empty($password))
			$this->password = $password;
	}

	public function destination (): string
	{
		return $this->destination;
	}

	public function handle (): string
	{
		return $this->handle;
	}

	public function is_password_protected (): bool
	{
		return !empty($this->password);
	}

	public function password (): string
	{
		return $this->password;
	}
}

?>
