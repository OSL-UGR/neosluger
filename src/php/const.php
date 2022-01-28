<?php declare(strict_types=1);


namespace Neosluger
{
	use MongoDB\Client as Mongo;

	const HASH_LENGTH    = 8;
	const MAX_HANDLE_LEN = 50;
	const MIN_HANDLE_LEN = 5;
	const MONGO          = new Mongo("mongodb://localhost:27017");
	const SITE_ADDRESS   = "localhost/";


	function LOG_COLLECTION ()
	{
		return MONGO->neosluger->access_logs;
	}


	function URL_COLLECTION ()
	{
		return MONGO->neosluger->urls;
	}


	function parse_request_uri_nth_item (int $n): string
	{
		/*
		 * Study this regex with https://regex101.com/.
		 * Example where REQUEST_URI is '/item/12345?always=ignored':
		 * - n=1: Returns 'item'.
		 * - n=2: Returns '12345'.
		 */
		return preg_replace("/^\/?([^\/?]+\/){".($n-1)."}([^\/?]+).*$/", "$2", $_SERVER["REQUEST_URI"]);
	}
}


?>
