<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/../settings/settings.php");


/** @fn parse_request_uri_nth_item (int $n): string
  * @brief Gets a portion of an URI.
  *
  * @param $n nth item to get from the URI.
  * @return string Queried element from the URI.
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


/** @fn user_ip_is_allowed (): bool
  * @brief Checks whether the user's ip is in the allowlist.
  *
  * @return bool Whether the IP is allowed.
  *
  * The user is always allowed if it has no IP, i.e. they are a dev testing
  * locally.
  */

function user_ip_is_allowed (): bool
{
	$allowed = false;
	$i = 0;

	if (!array_key_exists("REMOTE_ADDR", $_SERVER))
		$allowed = true;

	while (!$allowed && $i < count(\NeoslugerSettings\ALLOWED_IPS))
	{
		$ip_regex = "/".preg_replace("/\.\*/", "\..+", \NeoslugerSettings\ALLOWED_IPS[$i])."/";
		$allowed  = (preg_match($ip_regex, $_SERVER["REMOTE_ADDR"]) != false);
		++$i;
	}

	return $allowed;
}


?>
