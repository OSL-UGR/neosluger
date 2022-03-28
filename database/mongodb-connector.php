<?php declare(strict_types=1); namespace NeoslugerDB;


require_once(__DIR__."/../core/result.php");
require_once(__DIR__."/../core/url.php");
require_once(__DIR__."/../core/url-gateway.php");
require_once(__DIR__."/../vendor/autoload.php");


/** @class DummyDB
  * @brief `\Neosluger\URLGateway` implementation for MongoDB.
  */

class MongoDBConnector implements \Neosluger\URLGateway
{
	/** Name of the access logs collection. **/
	private string $logs = "access_logs";

	/** Name of the urls collection. **/
	private string $urls = "urls";

	/** Database to works with. **/
	private \MongoDB\Database $db;


	/** @fn __construct (string $address)
	  * @brief Sets the database connection.
	  *
	  * @param $address The database address to connect to.
	  */

	public function __construct (string $address)
	{
		$this->db = (new \MongoDB\Client($address))->neosluger;
	}


	/** try_db_access (callable $command): mixed
	  * @brief Executes a command on the database recovering from possible errors.
	  *
	  * @param $command Function to be executed on the database.
	  * @return Result The command's optional result.
	  */

	private function try_db_access (callable $command): \Neosluger\Result
	{
		$result = \Neosluger\Result::from_error("Critical error connecting to the database! Is it even on?");

		try
		{
			$result = \Neosluger\Result::from_value($command());
		}
		catch (\Exception $e)
		{
			error_log($e->__toString());
		}

		return $result;
	}


	/** @fn find_log_command (string $handle): ?\MongoDB\Model\BSONArray
	  * @brief Command to query the database for a logs table.
	  *
	  * @param $handle The handle whose logs are being queried.
	  * @return \MongoDB\Model\BSONArray|bool The logs table, `false` if it doesn't exist or is empty.
	  */

	private function find_log_command (string $handle): \MongoDB\Model\BSONArray | bool
	{
		$result = $this->db->selectCollection($this->logs)->find(["handle" => $handle])->toArray();
		return (empty($result) ? false : $result[0]["accesses"]);
	}


	/** @fn find_url_command (string $handle): ?\MongoDB\Model\BSONDocument
	  * @brief Command to query the database for a URL.
	  *
	  * @param $handle The handle whose URL is being queried.
	  * @return \MongoDB\Model\BSONDocument|bool The URL, `false` if it doesn't exist or is empty.
	  */

	private function find_url_command (string $handle): \MongoDB\Model\BSONDocument | bool
	{
		$result = $this->db->selectCollection($this->urls)->find(["handle" => $handle])->toArray();
		return (empty($result) ? false : $result[0]);
	}


	/** @fn insert_log_command (\Neosluger\URL $url): \MongoDB\InsertOneResult
	  * @brief Command to insert a logs document for an URL into the database.
	  *
	  * @param $url The URL whose matching logs document is going to be inserted.
	  * @return \MongoDB\InsertOneResult The insertion result from the database.
	  */

	private function insert_log_command (\Neosluger\URL $url): \MongoDB\InsertOneResult
	{
		return $this->db->selectCollection($this->logs)->insertOne([
			"handle"   => $url->handle(),
			"accesses" => array($url->creation_datetime()->format("Y-m-d H:i:s.u"))
		]);
	}


	/** @fn insert_url_command (\Neosluger\URL $url): \MongoDB\InsertOneResult
	  * @brief Command to insert a URL into the databse.
	  *
	  * @param $url The URL whose document is going to be inserted.
	  * @return \MongoDB\InsertOneResult The insertion result from the database.
	  */

	private function insert_url_command (\Neosluger\URL $url): \MongoDB\InsertOneResult
	{
		return $this->db->selectCollection($this->urls)->insertOne([
			"handle"      => $url->handle(),
			"destination" => $url->destination(),
		]);
	}


	/** @fn update_log_command (\Neosluger\URL $url, \DateTime $datetime): \MongoDB\UpdateResult
	  * @brief Command to update a URL's logs with a new access datetime.
	  *
	  * @param $url The URL whose logs are going to be updated.
	  * @param $datetime The datetime to update the URL's logs with.
	  * @return \MongoDB\UpdateResult The update result from the database.
	  */

	private function update_log_command (\Neosluger\URL $url, \DateTime $datetime): \MongoDB\UpdateResult
	{
		return $this->db->selectCollection($this->logs)->updateOne(["handle" => $url->handle()], [
			'$push' => ["accesses" => $datetime->format("Y-m-d H:i:s.u")]
		]);
	}


	/** @fn generate_qr_from_string (string $contents): string
	  * @brief Implementation of `QRRequestBoundary::generate_qr_from_url`.
	  */

	public function find_url_by_handle (string $handle): \Neosluger\Result
	{
		$result = $url = $this->try_db_access(fn () => $this->find_url_command($handle));

		if ($url->ok())
		{
			$result = $log = $this->try_db_access(fn () => $this->find_log_command($handle));

			if ($log-> ok())
			{
				$url = $url->unwrap();
				$log = $log->unwrap();

				if ($url !== false && $log !== false)
					$result = \Neosluger\Result::from_value(new \Neosluger\URL($url["destination"], new \Datetime($log[0]), $url["handle"]));
				else
					$result->push_back("URL lookup with handle '".$handle."' returned zero results!");
			}
		}

		if (!$result->ok())
			$result->push_back("Could not find URL with handle '".$handle."'!");

		return $result;
	}


	/** @fn find_urls_logged_accesses (\Neosluger\URL $url): ?array
	  * @brief Implementation of `\Neosluger\URLRequestBoundary::find_urls_logged_accesses`.
	  */

	public function find_urls_logged_accesses (\Neosluger\URL $url): \Neosluger\Result
	{
		$result = $accesses = $this->try_db_access(fn () => $this->find_log_command($url->handle()));

		if ($accesses->ok())
		{
			$accesses = $accesses->unwrap();

			if (!empty($accesses))
			{
				$log = [];

				foreach ($accesses as $access)
					array_push($log, new \Datetime($access));

				$result = \Neosluger\Result::from_value($log);
			}
			else
				$result->push_back("Logs lookup for URL '".$url->handle()."' returned zero results!");
		}

		if (!$result->ok())
			$result->push_back("Could not find URL logs for '".$url->handle()."'!");

		return $result;
	}


	/** @fn log_access_to_url (\Neosluger\URL $url, \DateTime $datetime): bool
	  * @brief Implementation of `\Neosluger\URLRequestBoundary::log_access_to_url`.
	  */

	public function log_access_to_url (\Neosluger\URL $url, \DateTime $datetime): \Neosluger\Result
	{
		$update_result = $this->try_db_access(fn () => $this->update_log_command($url, $datetime));
		$result = $update_result;

		if ($update_result->ok() && $update_result->unwrap()->getModifiedCount() === 1)
			$result = \Neosluger\Result::from_value(true);
		else
			$result->push_back("Could not log URL access for '".$url->handle()."'!");

		return $result;
	}


	/** @fn register_new_url (\Neosluger\URL $url): bool
	  * @brief Implementation of `\Neosluger\URLRequestBoundary::register_new_url`.
	  */

	public function register_new_url (\Neosluger\URL $url): \Neosluger\Result
	{
		$result = $found_url = $this->try_db_access(fn () => $this->find_url_command($url->handle()));

		if ($found_url->ok())
		{
			if ($found_url->unwrap() === false)
			{
				$result = $url_insertion = $this->try_db_access(fn () => $this->insert_url_command($url));

				if ($url_insertion->ok() && $url_insertion->unwrap()->getInsertedCount() === 1)
				{
					$result = $log_insertion = $this->try_db_access(fn () => $this->insert_log_command($url));

					if ($log_insertion->ok() && $log_insertion->unwrap()->getInsertedCount() === 1)
						$result = \Neosluger\Result::from_value(true);
					else
						$result->push_back("Insertion of logs for URL '".$url->handle()."' failed!");
				}
				else
					$result->push_back("Insertion of URL '".$url->handle()."' failed!");
			}
			else
				$result = \Neosluger\Result::from_value(false);
		}

		if (!$result->ok())
			$result->push_back("Could not register URL '".$url->handle()."'!");

		return $result;
	}


	/** @fn set_urls_collection (string $urls): void
	  * @brief DEBUG ONLY: Changes the urls collection.
	  *
	  * @param $urls Name of the new urls collection.
	  */

	public function set_urls_collection (string $urls): void
	{
		$this->urls = $urls;
	}


	/** @fn set_logs_collection (string $logs): void
	  * @brief DEBUG ONLY: Changes the logs collection.
	  *
	  * @param $logs Name of the new logs collection.
	  */

	public function set_logs_collection (string $logs): void
	{
		$this->logs = $logs;
	}


	/** @fn urls (): array
	  * @brief DEBUG ONLY: Consultor for the underlying URLs array.
	  *
	  * @return array The URLs array.
	  */

	public function urls (): array
	{
		$urls_result = $this->try_db_access(fn () => $this->db->selectCollection($this->urls)->find())->unwrap()->toArray();
		$urls = [];

		foreach ($urls_result as $url)
		{
			$log = $this->try_db_access(fn () => $this->find_log_command($url["handle"]))->unwrap();
			array_push($urls, new \Neosluger\URL($url["destination"], new \DateTime($log[0]), $url["handle"]));
		}

		return $urls;
	}


	/** @fn logs (): array
	  * @brief DEBUG ONLY: Consultor for the underlying URLs array.
	  *
	  * @return array The logs array.
	  */

	public function logs (): array
	{
		$logs_result = $this->try_db_access(fn () => $this->db->selectCollection($this->logs)->find())->unwrap()->toArray();
		$all_logs = [];

		foreach ($logs_result as $logs)
		{
			$log = [];

			foreach ($logs["accesses"] as $access)
				array_push($log, new \DateTime($access));

			array_push($all_logs, $log);
		}

		return $all_logs;
	}
}


?>
