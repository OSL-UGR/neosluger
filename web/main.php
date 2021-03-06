<?php declare(strict_types=1); namespace NslWeb;


require_once($_SERVER['DOCUMENT_ROOT']."/core/url.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/boundaries.php");
require_once($_SERVER['DOCUMENT_ROOT']."/settings/server-helpers.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/settings/settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");

ini_set("display_errors", strval(\NslSettings\DEBUG));


/** @fn find_page (string $uri): string
  * @brief Finds the file `$uri.php` in `pages/*.php`.
  *
  * @param $uri The URI requested by the user from the web.
  * @return string Path to the file if found, empty string otherwise
  *
  * To avoid web server shenanigans with `/` begin the index we transform the
  * string agead of time and we don't have to mess with the server settings.
  */

function find_page (string $uri): string
{
	if ($uri === "/")
		$uri = "/index";

	$path   = __DIR__."/pages/".$uri.".php";
	return (file_exists($path) ? $path : "");
}


/** @fn redirect_to (\Nsl\URL $url): void
  * @brief Redirectes the user from a short URL to their destination.
  *
  * @param $url The URL to redirect the user from.
  */

function redirect_to (\Nsl\URL $url): void
{
	// Prevent the user from caching this page so that all accesses are logged
	header('Expires: Fri, 25 Oct 1996 14:40:00 GMT'); // Happy birthday to me~
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', FALSE);
	header('Pragma: no-cache');

	\NslSettings\url_boundary()->log_access_to_url($url);
	header("Location: " . $url->destination(), true,  301);
}


function respond_to_api_request (): void
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/api/api.php");
}


/** @fn try_old_api_or_404 (): void
  * @brief Redirects the user to the 404 page if the request URI is not for the old API.
  *
  * This is the final step in resolving the URI. Instead of throwing a 404 error
  * when no other pages can be found, we first check if the user is trying to
  * call the old API and serve a custom error instead. This step can be skipped
  * and just serve the error page if calls to the old API stop being maintained.
  */

function try_old_api_or_404 (): void
{
	if (str_starts_with($_SERVER["REQUEST_URI"], "/sluger.php"))
		echo json_encode([
			"url"   => "",
			"error" => "0",
			"text"  => "This API is deprecated. Read the docs to update it!"
		]);
	else
		include($_SERVER['DOCUMENT_ROOT']."/web/pages/404.php");
}


/** @fn main (): void
  * @brief Resolves the URI and serves the user with the page they're looking for.
  */

function main (): void
{
	$uri = \NslSettings\parse_request_uri_nth_item($_SERVER["REQUEST_URI"], 1);

	if ($uri === "api")
		respond_to_api_request();
	else
	{
		$web_path = find_page($uri);

		if (!empty($web_path))
			include($web_path);
		else
		{
			$url = \NslSettings\url_boundary()->find_url_by_handle($uri);

			if ($url->ok())
				redirect_to($url->unwrap());
			else
				try_old_api_or_404();
		}
	}
}


main();


?>
