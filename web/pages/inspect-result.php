<?php declare(strict_types=1); namespace NeoslugerWeb;


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/core/const.php");


function filter_short_link_handle (string $query): string
{
	$handle = trim($query);
	$query_no_https  = trim(preg_replace("/https?:\/\//", "", $query));

	if (str_starts_with($query_no_https, \Neosluger\SITE_ADDRESS))
		$handle = substr($query_no_https, strlen(\Neosluger\SITE_ADDRESS), strlen($query));

	return trim($handle);
}


function goto_stats ()
{
	$query  = $_POST["neosluger-inspect-string"];
	$handle = filter_short_link_handle($query);

	header("Location: /stats/".$handle, true, 301);
}


goto_stats();


?>
