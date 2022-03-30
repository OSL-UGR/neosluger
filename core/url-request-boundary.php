<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/result.php");
require_once(__DIR__."/url.php");


/** @interface URLRequestBoundary
  * @brief Access boundary to the core logic for working with short URLs.
  *
  * This interface defines how components external to the core logic interact
  * with it. It must be the only way outsiders access the core logic, so that
  * maintaining the code is infinitely easier and extentng the boundary
  * implementation is done seamlessly.
  */

interface URLRequestBoundary
{
	/** @fn find_url_by_handle (string $handle): ?URL
	  * @brief Queries the existence of an URL with a given handle in the system.
	  *
	  * @param $handle The handle to query the system for.
	  * @return Result The URL optionally found in the system.
	  */

	public function find_url_by_handle (string $handle): Result; // <URL>


	/** @fn find_urls_logged_accesses (URL $url): ?array
	  * @brief Queries the system for an URL's access logs.
	  *
	  * @param $url The URL to be queried.
	  * @return ?array The URL's access logs, `null` if none were found.
	  *
	  * This function should never return `null` because registering a URL implies
	  * that its access logs are initialised with its creation date. If no logs
	  * are found consider that you have a very serious error with your system.
	  */

	public function find_urls_logged_accesses (URL $url): Result; // <array>


	/** @fn log_access_to_url (URL $url): ?\DateTime
	  * @brief Updates an URL's access logs.
	  *
	  * @param $url The URL's logs to be updated.
	  * @return \DateTime The logged datetime, `null` in case of failure.
	  */

	public function log_access_to_url (URL $url): Result; // <\DateTime>


	/** @fn register_new_url (URL $url): bool
	  * @brief Inserts a new URL in the system.
	  *
	  * @param $destination The new URL's destination.
	  * @param $handle (Optional) The new URL's handle.
	  * @return Result The optionally newly inserted URL.
	  */

	public function register_new_url (string $destination, string $handle = ""): Result;
}


?>
