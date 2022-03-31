<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/url.php");


/** @interface URLGateway
  * @brief Entity gateway to manage URLs in the massive storage system.
  *
  * This interface is defined in the core logic and must be implemented by the
  * database or any other massive storage drivers. The results should only
  * return errors if there were errors deriving from the database.
  */

interface URLGateway
{
	/** @fn find_url_by_handle (string $handle): Result
	  * @brief Queries the massive storage for an URL registered with a handle.
	  *
	  * @param $handle Handle of the URL to search for.
	  * @return Result The URL optionally found in the system.
	  */

	public function find_url_by_handle (string $handle): Result; // <URL>


	/** @fn find_urls_logged_accesses (URL $url): Result
	  * @brief Queries the massive storage for an URL's access logs.
	  *
	  * @param $url The URL to be queried.
	  * @return Result The URL's optionally got access logs.
	  *
	  * This function should never return `null` because inserting a URL implies
	  * that its access logs are initialised with its creation date. If no logs
	  * are found consider that you have a very serious error with your massive
	  * storage management system.
	  */

	public function find_urls_logged_accesses (URL $url): Result; // <array>


	/** @fn log_access_to_url (URL $url, \DateTime $datetime): Result
	  * @brief Updates the massive storage's URL access logs.
	  *
	  * @param $url The URL's logs to be updated.
	  * @param $datetime The datetime to update the logs with.
	  * @return Result Optionally `true` if it was logged, an error otherwise.
	  */

	public function log_access_to_url (URL $url, \DateTime $datetime): Result; // <bool>


	/** @fn register_new_url (URL $url): Result
	  * @brief Inserts a new URL in the massive storage system.
	  *
	  * @param $url The URL to be inserted.
	  * @return Result Optionally `true` if the URL was registered, `false` if it already existed.
	  */

	public function register_new_url (URL $url, string $authors_ip): Result; // <bool>
}


?>
