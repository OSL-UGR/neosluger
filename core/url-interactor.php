<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/helper-functions.php");
require_once(__DIR__."/url-request-boundary.php");


const ERR_URL_NOT_FOUND      = "The URL wasn't found in the system!";
const ERR_URL_NOT_INSERTED   = "There was an error inserting the URL in the system!";
const ERR_DUPLICATE_HANDLE   = "A URL with your handle already exists!";
const ERR_INVALID_HANDLE_LEN = "Custom handles must be between 5 and 50 characters long!";
const ERR_INVALID_IP         = "Only users from the University of Granada can create short URLs!";
const ERR_INVALID_URL        = "The URL string is not an actual URL!";
const ERR_NO_URL             = "A URL is required!";


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
		return substr(sha1($datetime->format("Y-m-d H:i:s.u") . $destination), 0, HANDLE_LENGTH);
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
		return (MIN_HANDLE_LEN <= $length && $length <= MAX_HANDLE_LEN);
	}


	/* @fn find_url_by_handle (string $handle): ?URL
	 * @brief Implementation of `URLRequestBoundary::find_url_by_handle`.
	 */

	public function find_url_by_handle (string $handle): Result
	{
		$result = Result::from_value($this->gateway->find_url_by_handle($handle));

		var_dump($result);

		if (!$result->ok())
			$result->push_back(ERR_URL_NOT_FOUND);

		return $result;
	}


	/* @fn find_urls_logged_accesses (URL $url): ?array
	 * @brief Implementation of `URLRequestBoundary::find_urls_logged_accesses`.
	 */

	public function find_urls_logged_accesses (URL $url): Result
	{
		$result = $this->gateway->find_urls_logged_accesses($url);

		if ($result->ok())
		{
			$log = $result->unwrap();

			if (!empty($log))
				$result = Result::from_value($log);
			else
				$result->push_back("Logs for '".$url->full_handle()."' are empty!");
		}

		if (!$result->ok())
			$result->push_back("Could not find logs for '".$url->full_handle()."'!");

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
		$datetime = $this->current_datetime();
		$result = Result::from_error("Could not log access to '".$url->full_handle()."'!");

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
		$result = Result::from_error(ERR_INVALID_IP);

		if (user_ip_is_allowed())
		{
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
						$result->push_back(ERR_URL_NOT_INSERTED);
				}
			}
		}

		return $result;
	}
}


?>
