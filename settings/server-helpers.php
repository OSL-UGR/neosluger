<?php declare(strict_types=1); namespace NeoslugerSettings;


require_once(__DIR__."/../settings/settings.php");


/** @fn parse_request_uri_nth_item (string $uri, int $index): string
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

function parse_request_uri_nth_item (string $uri, int $index): string
{
	return preg_replace("/^\/?([^\/?]+\/){".($index-1)."}([^\/?]+).*$/", "$2", $uri);
}


/** @fn user_ip_is_allowed (string $address): bool
  * @brief Checks whether the user's ip is in the allowlist.
  *
  * @return bool Whether the IP is allowed.
  */

function user_ip_is_allowed (string $address): bool
{
	$allowed = false;
	$i = 0;

	while (!$allowed && $i < count(ALLOWED_IPS))
	{
		$ip_regex = "/".preg_replace("/\.\*/", "\..+", ALLOWED_IPS[$i])."/";
		$allowed  = (preg_match($ip_regex, $address) !== 0);
		++$i;
	}

	return $allowed;
}


?>
