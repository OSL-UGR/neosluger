<?php declare(strict_types=1);


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/url.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/const.php");


/** @fn find_page (string $uri): string
  * @brief Finds the file `$uri.php` in `pages/*.php`.
  * @return $result: Path to the file if found, empty string otherwise
  */

function find_page (string $uri): string
{
	$path   = $_SERVER['DOCUMENT_ROOT']."/pages/".$uri.".php";
	$result = "";

	if (file_exists($path))
		$result = $path;

	return $result;
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
		include($_SERVER['DOCUMENT_ROOT']."/pages/404.php");
}


/** @fn main (): void
  * @brief Resolves the URI and serves the user with the page they're looking for.
  */

function main (): void
{
	$uri  = Neosluger\parse_request_uri_nth_item(1);
	$path = find_page($uri);

	if (!empty($path))
	{
		include($path);
	}
	else
	{
		$url = URL::from_database($uri);

		if ($url->is_null())
		{
			try_old_api_or_404();
		}
		else
		{
			// Prevent the user from caching this page so that all accesses are logged
			header('Expires: Fri, 25 Oct 1996 14:40:00 GMT'); // Happy birthday to me~
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0', FALSE);
			header('Pragma: no-cache');

			$url->log_access();
			header("Location: " . $url->destination(), true,  301);
		}
	}
}


main();


?>
