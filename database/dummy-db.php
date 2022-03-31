<?php declare(strict_types=1); namespace NslDB;


require_once(__DIR__."/strings.php");
require_once(__DIR__."/../core/result.php");
require_once(__DIR__."/../core/url.php");
require_once(__DIR__."/../core/url-gateway.php");


/** @class DummyDB
  * @brief `\Nsl\URLGateway` implementation for testing purposes.
  *
  * DO NOT USE THIS DATABASE IN PRODUCTION! This is used to test the core logic
  * without the need to have a running database driver. This class mimics a
  * database with two arrays of URLS and logs. If you use this in production
  * no URLs will be saved in the system!
  */

class DummyDB implements \Nsl\URLGateway
{
	/** URLs inserted in the database during the current execution. */
	private array $urls = [];

	/** Logs for the inserted URLs. */
	private array $logs = [];


	/** @fn find_url_by_handle (string $handle): \Nsl\Result
	  * @brief Implementation of `\Nsl\URLRequestBoundary::find_url_by_handle`.
	  */

	public function find_url_by_handle (string $handle): \Nsl\Result // <\Nsl\URL>
	{
		global $ERR_URL_NOT_FOUND;
		$result = \Nsl\Result::from_error($ERR_URL_NOT_FOUND()." '".$handle."'!");

		if (array_key_exists($handle, $this->urls))
			$result = \Nsl\Result::from_value($this->urls[$handle]);

		return $result;
	}


	/** @fn find_urls_logged_accesses (\Nsl\URL $url): \Nsl\Result
	  * @brief Implementation of `\Nsl\URLRequestBoundary::find_urls_logged_accesses`.
	  */

	public function find_urls_logged_accesses (\Nsl\URL $url): \Nsl\Result // <array>
	{
		global $ERR_LOG_NOT_FOUND;
		$result = \Nsl\Result::from_error($ERR_LOG_NOT_FOUND()." '".$url->handle()."'!");

		if (array_key_exists($url->handle(), $this->logs))
			$result = \Nsl\Result::from_value($this->logs[$url->handle()]);

		return $result;
	}


	/** @fn log_access_to_url (\Nsl\URL $url, \DateTime $datetime): \Nsl\Result
	  * @brief Implementation of `\Nsl\URLRequestBoundary::log_access_to_url`.
	  */

	public function log_access_to_url (\Nsl\URL $url, \DateTime $datetime): \Nsl\Result // <bool>
	{
		array_push($this->logs[$url->handle()], $datetime);
		return \Nsl\Result::from_value(true);
	}


	/** @fn register_new_url (\Nsl\URL $url): \Nsl\Result
	  * @brief Implementation of `\Nsl\URLRequestBoundary::register_new_url`.
	  */

	public function register_new_url (\Nsl\URL $url, string $authors_ip): \Nsl\Result // <bool>
	{
		$this->urls[$url->handle()] = $url;
		$this->logs[$url->handle()] = [$url->creation_datetime()];
		return \Nsl\Result::from_value(true);
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
