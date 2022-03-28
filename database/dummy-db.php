<?php declare(strict_types=1); namespace NeoslugerDB;


require_once(__DIR__."/../core/result.php");
require_once(__DIR__."/../core/url.php");
require_once(__DIR__."/../core/url-gateway.php");


/** @class DummyDB
  * @brief `\Neosluger\URLGateway` implementation for testing purposes.
  *
  * DO NOT USE THIS DATABASE IN PRODUCTION! This is used to test the core logic
  * without the need to have a running database driver. This class mimics a
  * database with two arrays of URLS and logs. If you use this in production
  * no URLs will be saved in the system!
  */

class DummyDB implements \Neosluger\URLGateway
{
	/** URLs inserted in the database during the current execution. */
	private array $urls = [];

	/** Logs for the inserted URLs. */
	private array $logs = [];


	/** @fn find_url_by_handle (string $handle): ?\Neosluger\URL
	  * @brief Implementation of `\Neosluger\URLRequestBoundary::find_url_by_handle`.
	  */

	public function find_url_by_handle (string $handle): \Neosluger\Result
	{
		$result = \Neosluger\Result::from_error("Could not find URL '".$handle."'!");

		if (array_key_exists($handle, $this->urls))
			$result = \Neosluger\Result::from_value($this->urls[$handle]);

		return $result;
	}


	/** @fn find_urls_logged_accesses (\Neosluger\URL $url): ?array
	  * @brief Implementation of `\Neosluger\URLRequestBoundary::find_urls_logged_accesses`.
	  */

	public function find_urls_logged_accesses (\Neosluger\URL $url): \Neosluger\Result
	{
		$result = \Neosluger\Result::from_error("Could not find logs for '".$url->handle()."'!");

		if (array_key_exists($url->handle(), $this->logs))
			$result = \Neosluger\Result::from_value($this->logs[$url->handle()]);

		return $result;
	}


	/** @fn log_access_to_url (\Neosluger\URL $url, \DateTime $datetime): bool
	  * @brief Implementation of `\Neosluger\URLRequestBoundary::log_access_to_url`.
	  */

	public function log_access_to_url (\Neosluger\URL $url, \DateTime $datetime): \Neosluger\Result
	{
		array_push($this->logs[$url->handle()], $datetime);
		return \Neosluger\Result::from_value(true);
	}


	/** @fn register_new_url (\Neosluger\URL $url): bool
	  * @brief Implementation of `\Neosluger\URLRequestBoundary::register_new_url`.
	  */

	public function register_new_url (\Neosluger\URL $url): \Neosluger\Result
	{
		$this->urls[$url->handle()] = $url;
		$this->logs[$url->handle()] = [$url->creation_datetime()];
		return \Neosluger\Result::from_value(true);
	}


	/** @fn urls (): array
	  * @brief Consultor for the underlying URLs array.
	  *
	  * @return array The URLs array.
	  */

	public function urls (): array
	{
		return $this->urls;
	}


	/** @fn logs (): array
	  * @brief Consultor for the underlying logs array.
	  *
	  * @return array The logs array.
	  */

	public function logs (): array
	{
		return $this->logs;
	}
}


?>
