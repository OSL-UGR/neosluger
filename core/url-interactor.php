<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/strings.php");
require_once(__DIR__."/url-request-boundary.php");


/** @class QRInteractor
  * @brief `URLRequestBoundary` implementation to manage short URLs.
  */

final class URLInteractor implements URLRequestBoundary
{
	/** Interface to the massive storage system driver that stores the URLs. */
	private URLGateway $gateway;


	/** @fn __construct (URLGateway $gateway)
	  * @brief Constructs the interactor with a gateway implementation.
	  *
	  * @para $gateway Implementation of the URLGateway interface.
	  */

	public function __construct (URLGateway $gateway)
	{
		$this->gateway = $gateway;
	}


	/** @fn create_handle_with_hash (\DateTime $datetime, string $destination): string
	  * @brief Generates a random handle for an URL by hashing a datetime its destination.
	  *
	  * @param $datetime The datetime as the hash's random componen.
	  * @param $destination The destination who's matching handle is going to be created.
	  * @return string The newly created handle string.
	  */

	private function create_handle_with_hash (\DateTime $datetime, string $destination): string
	{
		return substr(sha1($datetime->format("Y-m-d H:i:s.u") . $destination), 0, \NslSettings\HANDLE_LENGTH);
	}


	/** @fn current_datetime (): \DateTime
	  * @brief Uniform interface to query the system for the current datetime.
	  *
	  * @return \DateTime The current datetime.
	  */

	private function current_datetime (): \DateTime
	{
		return new \DateTime("NOW", new \DateTimeZone(date('T')));
	}


	/** @fn handle_is_within_bounds (string $handle): bool
	  * @brief Checks whether the handle honours the system's handle length bounds.
	  *
	  * @param $handle The handle to be checked.
	  * @returh bool Whether the handle's length is valid.
	  */

	private function handle_is_within_bounds (string $handle): bool
	{
		$length = strlen($handle);
		return (\NslSettings\MIN_HANDLE_LEN <= $length && $length <= \NslSettings\MAX_HANDLE_LEN);
	}


	/* @fn find_url_by_handle (string $handle): ?URL
	 * @brief Implementation of `URLRequestBoundary::find_url_by_handle`.
	 */

	public function find_url_by_handle (string $handle): Result
	{
		global $ERR_URL_NOT_FOUND;
		$result = $this->gateway->find_url_by_handle($handle);

		if (!$result->ok())
			$result->push_back($ERR_URL_NOT_FOUND());

		return $result;
	}


	/* @fn find_urls_logged_accesses (URL $url): ?array
	 * @brief Implementation of `URLRequestBoundary::find_urls_logged_accesses`.
	 */

	public function find_urls_logged_accesses (URL $url): Result
	{
		global $ERR_EMPTY_LOG_PRE, $ERR_EMPTY_LOG_POST, $ERR_LOG_NOT_FOUND;
		$result = $this->gateway->find_urls_logged_accesses($url);

		if ($result->ok())
		{
			$log = $result->unwrap();

			if (!empty($log))
				$result = Result::from_value($log);
			else
				$result->push_back($ERR_EMPTY_LOG_PRE()." '".$url->full_handle()."' ".$ERR_EMPTY_LOG_POST());
		}

		if (!$result->ok())
			$result->push_back($ERR_LOG_NOT_FOUND()." '".$url->full_handle()."'!");

		return $result;
	}


	/* @fn log_access_to_url (URL $url): ?\DateTime
	 * @brief Implementation of `URLRequestBoundary::log_access_to_url`.
	 *
	 * The access datetime is calculated by the interactor to remove that
	 * responsibility from the caller.
	 */

	public function log_access_to_url (URL $url): Result
	{
		global $ERR_COULDNT_LOG;
		$datetime = $this->current_datetime();
		$result = Result::from_error($ERR_COULDNT_LOG()." '".$url->full_handle()."'!");

		if ($this->gateway->log_access_to_url($url, $datetime)->ok())
			$result = Result::from_value($datetime);

		return $result;
	}

	/* @fn register_new_url (string $destination, string $handle = ""): ?URL
	 * @brief Implementation of `URLRequestBoundary::register_new_url`.
	 *
	 * We first check whether the user is allowed to perform this operation.
	 * In an affirmative case, we generate a random handle if none is passed.
	 * Checking whether the handle is valid always succeeds for empty handles.
	 * We return the URL Result by querying the gateway for it to ensure that it¡
	 * has been correctly inserted.
	 */

	public function register_new_url (string $destination, string $handle = ""): Result
	{
		global $ERR_INVALID_HANDLE_LEN, $ERR_URL_NOT_INSERTED;
		$result = Result::from_error($ERR_INVALID_HANDLE_LEN());

		$datetime = $this->current_datetime();

		if (empty($handle))
		{
			$handle = $this->create_handle_with_hash($this->current_datetime(), $destination);

			while ($this->gateway->find_url_by_handle($handle)->ok())
				$handle = $this->create_handle_with_hash($this->current_datetime(), $destination);
		}

		$handle_is_valid = $this->handle_is_within_bounds($handle) && !$this->gateway->find_url_by_handle($handle)->ok();

		if ($handle_is_valid)
		{
			$result = $register = $this->gateway->register_new_url(new URL($destination, $datetime, $handle));

			if ($register->ok() && $register->unwrap() === true)
			{
				$result = $found_url = $this->gateway->find_url_by_handle($handle);

				if (!$found_url->ok())
					$result->push_back($ERR_URL_NOT_INSERTED());
			}
		}

		return $result;
	}
}


?>
