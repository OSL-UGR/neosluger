<?php declare(strict_types=1);


require_once("const.php");


final class URL
{
	const HASH_LENGTH    = Neosluger\HASH_LENGTH;
	const MAX_HANDLE_LEN = Neosluger\MAX_HANDLE_LEN;
	const MIN_HANDLE_LEN = Neosluger\MIN_HANDLE_LEN;
	const SITE_ADDRESS   = Neosluger\SITE_ADDRESS;

	// Please change this to an enum when it becomes avaliable (PHP 8 >= 8.1.0)
	const URL_IS_NULL  = "IS_NULL";
	const URL_NOT_NULL = "NOT_NULL";


	private bool $duplicate = false;
	private DateTime $creation_datetime;
	private string $destination = "";
	private string $handle = "";


	private function __construct (string $destination, string $handle = "", string $is_null = URL::URL_NOT_NULL)
	{
		$this->creation_datetime = new DateTime("NOW", new DateTimeZone(date("T")));

		if ($is_null == URL::URL_NOT_NULL)
		{
			if (!filter_var($destination, FILTER_VALIDATE_URL))
				$this->destination = "";
			else
			{
				$this->destination = $destination;

				if (!empty($handle))
				{
					$this->handle = $handle;
				}
				else
				{
					$this->create_handle_with_hash();

					while (URL::handle_already_exists_in_database($this->handle))
					{
						$this->creation_datetime = new DateTime("NOW", new DateTimeZone(date("T")));
						$this->create_handle_with_hash();
					}
				}
			}
		}
	}


	public static function from_db_result (MongoDB\Model\BSONDocument $result): URL
	{
		return new URL(
			$result["destination"],
			$result["handle"],
		);
	}


	public static function from_form (string $destination, string $handle = "")
	{
		$new_url      = URL::from_null();
		$handle_len   = strlen($handle);
		$valid_handle = (
			empty($handle) ||
			(URL::MIN_HANDLE_LEN <= $handle_len && $handle_len <= URL::MAX_HANDLE_LEN)
		);

		if ($valid_handle)
		{
			if (!empty($handle) && URL::handle_already_exists_in_database($handle))
			{
				$new_url->duplicate = true;
			}
			else
			{
				$new_url = new URL($destination, $handle);
				$new_url->add_to_database();
			}
		}

		return $new_url;
	}


	public static function from_null ()
	{
		return new URL("", "", "", URL::URL_IS_NULL);
	}


	private static function handle_already_exists_in_database (string $handle): bool
	{
		$result     = Neosluger\URL_COLLECTION()->find(["handle" => $handle]);
		$exists     = (count($result->toArray()) > 0);

		return $exists;
	}


	public function creation_datetime (): DateTime
	{
		return $this->creation_datetime;
	}


	public function destination (): string
	{
		return $this->destination;
	}


	public function full_handle (): string
	{
		return URL::SITE_ADDRESS . $this->handle;
	}


	public function handle (): string
	{
		return $this->handle;
	}


	public function is_duplicate (): bool
	{
		return $this->duplicate;
	}


	public function is_null (): bool
	{
		return empty($this->destination);
	}


	public function log_access (): DateTime
	{
		$access_datetime = new DateTime("NOW", new DateTimeZone(date("T")));

		Neosluger\LOG_COLLECTION()->updateOne(["handle" => $this->handle], [
			'$push' => ["accesses" => $access_datetime->format("Y-m-d H:i:s.u")]
		]);

		return $access_datetime;
	}


	private function add_to_database (): void
	{
		Neosluger\URL_COLLECTION()->insertOne([
			"destination" => $this->destination,
			"handle"      => $this->handle,
		]);

		// The date is stored as a string because PHP is a stringly typed language.

		Neosluger\LOG_COLLECTION()->insertOne([
			"handle"   => $this->handle,
			"accesses" => array($this->creation_datetime->format("Y-m-d H:i:s.u"))
		]);
	}


	private function create_handle_with_hash (): void
	{
		$this->handle = substr(
			sha1($this->creation_datetime->format("Y-m-d H:i:s.u") . $this->destination),
			0, URL::HASH_LENGTH
		);
	}
}


?>
