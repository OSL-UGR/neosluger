<?php declare(strict_types=1);


require_once("const.php");
use MongoDB\Client as Mongo;


final class URL
{
	const HASH_LENGTH  = Neosluger\HASH_LENGTH;
	const SITE_ADDRESS = Neosluger\SITE_ADDRESS;

	// Please change this to an enum when it becomes avaliable (PHP 8 >= 8.1.0)
	const URL_IS_NULL  = "IS_NULL";
	const URL_NOT_NULL = "NOT_NULL";


	private DateTime $creation_datetime;
	private string $destination = "";
	private string $handle = "";
	private string $password = "";


	private function __construct (string $destination, string $handle = "", string $password = "", string $is_null = URL::URL_NOT_NULL)
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
					$this->handle = $handle;
				else
					$this->create_handle_with_hash();

				if (!empty($password))
					$this->password = $password;
			}
		}
	}


	public static function from_db_result (MongoDB\Model\BSONDocument $result): URL
	{
		return new URL(
			$result["destination"],
			$result["handle"],
			$result["password"]
		);
	}


	public static function from_form (string $destination, string $handle = "", string $password = "")
	{
		$new_url = new URL($destination, $handle, $password);
		$new_url->add_to_database();
		return $new_url;
	}


	public static function from_null ()
	{
		return new URL("", "", "", URL::URL_IS_NULL);
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


	public function is_null (): bool
	{
		return ($this->destination == "");
	}


	public function is_password_protected (): bool
	{
		return !empty($this->password);
	}


	public function log_access (): DateTime
	{
		$mongo = new Mongo("mongodb://localhost:27017");
		$log_collection  = $mongo->neosluger->access_logs;
		$access_datetime = new DateTime("NOW", new DateTimeZone(date("T")));

		$log_collection->updateOne(["handle" => $this->handle], [
			'$push' => ["accesses" => $access_datetime->format("Y-m-d H:i:s.u")]
		]);

		return $access_datetime;
	}


	public function password (): string
	{
		return $this->password;
	}


	private function add_to_database (): void
	{
		$mongo = new Mongo("mongodb://localhost:27017");
		$url_collection = $mongo->neosluger->urls;
		$log_collection = $mongo->neosluger->access_logs;

		$url_collection->insertOne([
			"destination" => $this->destination,
			"handle"      => $this->handle,
			"password"    => $this->password,
		]);

		// The date is stored as a string because PHP is a stringly typed language.

		$log_collection->insertOne([
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
