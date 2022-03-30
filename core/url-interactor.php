<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/strings.php");
require_once(__DIR__."/url-request-boundary.php");
require_once(__DIR__."/../settings/server-helpers.php");


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


	/* @fn find_url_by_handle (string $handle): ?URL
	 * @brief Implementation of `URLRequestBoundary::find_url_by_handle`.
	 */

	public function find_url_by_handle (string $handle): Result
	{
		return $this->gateway->find_url_by_handle($handle);
	}


	/* @fn find_urls_logged_accesses (URL $url): ?array
	 * @brief Implementation of `URLRequestBoundary::find_urls_logged_accesses`.
	 */

	public function find_urls_logged_accesses (URL $url): Result
	{
		return $this->gateway->find_urls_logged_accesses($url);
	}


	/* @fn log_access_to_url (URL $url): ?\DateTime
	 * @brief Implementation of `URLRequestBoundary::log_access_to_url`.
	 *
	 * The access datetime is calculated by the interactor to remove that
	 * responsibility from the caller.
	 */

	public function log_access_to_url (URL $url): Result
	{
		$datetime = $this->current_datetime();
		$result   = $this->gateway->log_access_to_url($url, $datetime);

		if ($result->ok())
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

	public function register_new_url (string $destination, string $handle, string $authors_ip): Result
	{
		$result = $this->url_registration_is_valid($destination, $handle, $authors_ip);

		if ($result->ok())
			$result = $this->create_url_and_send_for_registration($destination, $handle, $authors_ip);

		return $result;
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


	private function create_url_and_send_for_registration (string $destination, string $handle, string $authors_ip): Result
	{
		if (empty($handle))
			do
				$handle = $this->create_handle_with_hash($this->current_datetime(), $destination);
			while ($this->gateway->find_url_by_handle($handle)->ok());

		$url    = new URL($destination, $this->current_datetime(), $handle);
		$result = $this->gateway->register_new_url($url, $authors_ip);

		if ($result->ok())
			$result = Result::from_value($url);

		return $result;
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

	private function custom_handle_is_within_bounds (string $handle): bool
	{
		$length = strlen($handle);
		return ($length === 0 || (\NslSettings\MIN_HANDLE_LEN <= $length && $length <= \NslSettings\MAX_HANDLE_LEN));
	}


	private function url_registration_is_valid (string $destination, string $handle, string $authors_ip): Result
	{
		global $ERR_DUPLICATE_HANDLE, $ERR_ILLEGAL_DESTINATION, $ERR_ILLEGAL_HANDLE_LEN, $ERR_ILLEGAL_IP, $ERR_NO_DESTINATION;
		$result = Result::from_error($ERR_ILLEGAL_IP());

		if (\NslSettings\user_ip_is_allowed($authors_ip))
		{
			if (empty($destination))
				$result = Result::from_error($ERR_NO_DESTINATION());
			else if (!filter_var($destination, FILTER_VALIDATE_URL))
				$result = Result::from_error($ERR_ILLEGAL_DESTINATION());
			else if (!$this->custom_handle_is_within_bounds($handle))
				$result = Result::from_error($ERR_ILLEGAL_HANDLE_LEN());
			else if ($this->gateway->find_url_by_handle($handle)->ok())
				$result = Result::from_error($ERR_DUPLICATE_HANDLE());
			else
				$result = Result::from_value(true);
		}

		return $result;
	}
}


?>
