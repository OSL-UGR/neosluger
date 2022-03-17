<?php declare(strict_types=1); namespace Neosluger;

require_once(__DIR__."/../vendor/autoload.php");


use MongoDB\Client as Mongo;

/*
 * Edit these constants to modify Neosluger's behaviours in your server.
 * You MUST at least change SITE_ADDRESS to your server address.
 */

const HASH_LENGTH    = 8;
const MAX_HANDLE_LEN = 50;
const MIN_HANDLE_LEN = 5;
const MONGO          = new Mongo("mongodb://localhost:27017");
const SITE_ADDRESS   = "localhost/";


/*
 * IP Addresses from which the user is allowed to access certain parts of the
 * service. Use `user_ip_is_allowed()`, defined below, to check whether they
 * have access.
 */

const ALLOWED_IPS = [
	"127.0.0.1",
	"150.214.*.*",
	"172.*.*.*"
];


function LOG_COLLECTION ()
{
	return MONGO->neosluger->access_logs;
}


function URL_COLLECTION ()
{
	return MONGO->neosluger->urls;
}


/** @fn parse_request_uri_nth_item (int $n): string
  * @brief Gets a portion of an URI.
  *
  * Study this regex with https://regex101.com/.
  * Example where REQUEST_URI is '/item/12345?always=ignored':
  * - n=1: Returns 'item'.
  * - n=2: Returns '12345'.
  */

function parse_request_uri_nth_item (int $n): string
{
	return preg_replace("/^\/?([^\/?]+\/){".($n-1)."}([^\/?]+).*$/", "$2", $_SERVER["REQUEST_URI"]);
}


function user_ip_is_allowed (): bool
{
	$allowed = false;
	$i = 0;

	while (!$allowed && $i < count(ALLOWED_IPS))
	{
		$ip_regex = "/".preg_replace("/\.\*/", "\..+", ALLOWED_IPS[$i])."/";
		$allowed  = (preg_match($ip_regex, $_SERVER['REMOTE_ADDR']) != false);
		++$i;
	}

	return $allowed;
}


?>
