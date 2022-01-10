<?php declare(strict_types=1);


use MongoDB\Client as Mongo;


final class URL
{
	const HASH_LENGTH  = 6;
	const MILLISECONDS = 1000;
	const SITE_ADDRESS = "https://sl.ugr.es/";


	private DateTime $creation_datetime;
	private string $destination  = "";
	private string $handle = "";
	private string $password = "";


	private function __construct (string $destination, string $handle = "", string $password = "")
	{
		if (!filter_var($destination, FILTER_VALIDATE_URL))
			throw new InvalidArgumentException("'$destination' is not an URL!");

		$this->creation_datetime = new DateTime("NOW", new DateTimeZone(date("T")));
		$this->destination = $destination;

		if (!empty($handle))
			$this->handle = $handle;
		else
			$this->create_handle_with_hash();

		if (!empty($password))
			$this->password = $password;
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
