<?php declare(strict_types=1); namespace NeoslugerWeb; ini_set("display_errors", '1');


function filter_short_link_handle (string $query, string $site_address): string
{
	$url = preg_replace("/https?:\/\//", "", trim($query));

	if (str_starts_with($url, $site_address))
		$url = substr($url, strlen($site_address)); // Remove the leading slash

	return $url;
}

?>
