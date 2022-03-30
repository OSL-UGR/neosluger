<?php declare(strict_types=1); namespace NeoslugerWeb;


require_once(__DIR__."/../presenter/render.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/boundaries.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/settings/settings.php");

ini_set("display_errors", strval(\NeoslugerSettings\DEBUG));


function render_stats (\Neosluger\URL $url, array $logs): void
{
	$qr_path = \NeoslugerSettings\qr_boundary()->generate_qr_from_url($url);

	render("stats", [
		"accesses"      => count($logs) - 1,
		"creation_date" => $logs[0]->format("Y-m-d"),
		"creation_time" => $logs[0]->format("H:i:s"),
		"url"           => $url,
		"qr_path"       => substr($qr_path, strlen($_SERVER["DOCUMENT_ROOT"])),
	]);
}


function page_main (): void
{
	$handle = \Neosluger\parse_request_uri_nth_item(2);
	$find_result = \NeoslugerSettings\url_boundary()->find_url_by_handle($handle);

	if ($find_result->ok())
	{
		$url = $find_result->unwrap();
		render_stats($url, \NeoslugerSettings\url_boundary()->find_urls_logged_accesses($url)->unwrap());
	}
	else
		render("stats", ["error" => true]);
}


page_main();


?>
