<?php declare(strict_types=1);


ini_set("display_errors", '1');
require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/const.php");
require_once($_SERVER['DOCUMENT_ROOT']."/php/url.php");


class API
{
	private static $response = [
		"url"      => "",
		"success"  => true,
		"errormsg" => "",
	];


	private static function args_contain_url (array $args): bool
	{
		$contained = (!empty($args["url"]));

		if (!$contained)
		{
			API::$response["success"]  = false;
			API::$response["errormsg"] = "A URL is required!";
		}

		return $contained;
	}


	private static function get_petition_arguments (): array
	{
		return [
			"handle" => array_key_exists("handle", $_GET) ? $_GET["handle"] : "",
			"url"    => array_key_exists("url",    $_GET) ? $_GET["url"]    : "",
		];
	}


	private static function url_is_valid (URL $url): bool
	{
		$valid = (!$url->is_null());

		if (!$valid)
		{
			API::$response["success"]  = false;

			if ($url->is_duplicate())
				API::$response["errormsg"] = "A URL with your handle already exists!";
			else
				API::$response["errormsg"] = "The URL string passed was not an actual URL!";
		}

		return $valid;
	}


	public static function process_petition (): void
	{
		$args = API::get_petition_arguments();

		if (API::args_contain_url($args))
		{
			$url = URL::from_form($args["url"], $args["handle"]);

			if (API::url_is_valid($url))
				API::$response["url"] = Neosluger\SITE_ADDRESS.$url->handle();
		}

		header("Content-type: application/json");
		echo json_encode(API::$response);
	}
}


API::process_petition();


?>
