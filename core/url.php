<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/../settings.php");


/** @class URL
  * @brief Representation of a shortened URL.
  */

final class URL
{
	// Please change this to an enum when it becomes avaliable (PHP 8 >= 8.1.0)
	const URL_IS_NULL  = "IS_NULL";
	const URL_NOT_NULL = "NOT_NULL";


	private bool $duplicate = false;
	private \DateTime $creation_datetime;
	private string $destination = "";
	private string $handle = "";


	/** @fn private function __construct (string $destination, string $handle = "", string $is_null = URL::URL_NOT_NULL)
	  * @brief private general constructor
	  *
	  * The reason for it being private is that PHP doesn't overload functions,
	  * so a reasonable way of having multiple constructors is going the Rust way
	  * and having many static from_* functions that construct an URL depending
	  * on the context. This lets the class have the flexibility of one with
	  * overloaded constructors as well as being more explicit about the context
	  * each instanced is constructed in, making it more maintainable.
	  */

	private function __construct (string $destination, string $handle = "", string $is_null = URL::URL_NOT_NULL)
	{
		$this->creation_datetime = new \DateTime("NOW", new \DateTimeZone(date("T")));

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
						$this->creation_datetime = new \DateTime("NOW", new \DateTimeZone(date("T")));
						$this->create_handle_with_hash();
					}
				}
			}
		}
	}


	/** @fn public static function from_database (string $handle): URL
	  * @brief Constructs an URL from the corresponding fields stored in the database
	  * @return: A valid URL or a null one if it doesn't exist
	  *
	  * URLs in the database are indexed by their handle, so we use it to find it
	  * in the database and construct the object from the result. This alleviates
	  * the responsibility of searching the database from the callers and exposes
	  * less intricate types to te system.
	  */

	public static function from_database (string $handle): URL
	{
		$result = URL_COLLECTION()->find(["handle" => $handle])->toArray();
		$url    = URL::from_null();

		if (count($result) > 0)
			$url = new URL($result[0]["destination"], $result[0]["handle"]);

		return $url;
	}


	/** @fn public static function from_form (string $destination, string $handle = "")
	  * @brief Constructs an URL from the fields of the web form or the API
	  * @return: A valid URL or a null one the form contains errors
	  *
	  * This function makes various checks to avoid entering invalid URLs in the
	  * databse. First, the handle must be either empty or between MIN_HANDLE_LEN
	  * and MAX_HANDLE_LEN. If it's valid, there must not exist an URL with thes
	  * same handle in the database. If it's unique, we create an URL and add it
	  * to the database.
	  */

	public static function from_form (string $destination, string $handle = "")
	{
		$new_url      = URL::from_null();
		$handle_len   = strlen($handle);
		$valid_handle = (
			empty($handle) ||
			(MIN_HANDLE_LEN <= $handle_len && $handle_len <= MAX_HANDLE_LEN)
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


	/** @fn public static function from_null ()
	  * @brief Constructs a null URL (one with a an empty destination)
	  *
	  * This function is used to construct URLS by default. If the relevant
	  * constructor can't create an URL object, a null one is returned instead.
	  */

	public static function from_null ()
	{
		return new URL("", "", "", URL::URL_IS_NULL);
	}


	private static function handle_already_exists_in_database (string $handle): bool
	{
		$result     = URL_COLLECTION()->find(["handle" => $handle]);
		$exists     = (count($result->toArray()) > 0);

		return $exists;
	}


	public function creation_datetime (): \DateTime
	{
		return $this->creation_datetime;
	}


	public function destination (): string
	{
		return $this->destination;
	}


	public function full_handle (): string
	{
		return SITE_ADDRESS . $this->handle;
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


	public function log_access (): \DateTime
	{
		$access_datetime = new \DateTime("NOW", new \DateTimeZone(date("T")));

		LOG_COLLECTION()->updateOne(["handle" => $this->handle], [
			'$push' => ["accesses" => $access_datetime->format("Y-m-d H:i:s.u")]
		]);

		return $access_datetime;
	}


	private function add_to_database (): void
	{
		URL_COLLECTION()->insertOne([
			"destination" => $this->destination,
			"handle"      => $this->handle,
		]);

		// The date is stored as a string because PHP is a stringly typed language.

		LOG_COLLECTION()->insertOne([
			"handle"   => $this->handle,
			"accesses" => array($this->creation_datetime->format("Y-m-d H:i:s.u"))
		]);
	}


	private function create_handle_with_hash (): void
	{
		$this->handle = substr(
			sha1($this->creation_datetime->format("Y-m-d H:i:s.u") . $this->destination),
			0, HASH_LENGTH
		);
	}
}


?>
