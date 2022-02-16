<?php declare(strict_types=1);


if (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']))
{
	require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/php/url.php");
}
else
{
	require_once("vendor/autoload.php");
	require_once("url.php");
}


use chillerlan\QRCode\{QRCode, QROptions};


/** @class QRWrapper
  * @brief Static functions that call the QR code creator.
  *
  * Instead of using a namespace, we prefer a class with static functions which
  * can limit their access for future maintainers.
  */

class QRWrapper
{
	private static function generate_qr (string $path, string $content): string
	{
		$file_path = $path;

		if (!file_exists($file_path))
		{
			$qr_options = new QROptions([
				"outputType"       => QRCode::OUTPUT_IMAGE_PNG,
				"eccLevel"         => QRCode::ECC_L,
				"imageTransparent" => false,
				"pngCompression"   => 9,
			]);

			/*
			 * The system should never throw an exception here, but we try and catch
			 * it nonetheless in case dependency changes years from now break the
			 * generator. This way, we don't stop the whole service from working,
			 * just degrade the QR generator section.
			 */

			try
			{
				(new QRCode($qr_options))->render($content, $file_path);
			}
			catch (Exception $e)
			{
				error_log($e->__toString());
				$file_path = "";
			}
		}

		return $file_path;
	}


	public static function cache_directory (): string
	{
		$cache_directory = "cache/qr";

		if (isset($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['DOCUMENT_ROOT']))
			$cache_directory = "../cache/qr";

		if (!file_exists($cache_directory))
		{
			$oldumask = umask(0);
			mkdir($cache_directory, 0775, true);
			umask($oldumask);
		}

		return $cache_directory;
	}


	public static function from_string (string $qr_string): string
	{
		return QRWrapper::generate_qr(
			QRWrapper::cache_directory()."/qr-".substr(sha1($qr_string), 0, 4).".png",
			$qr_string
		);
	}


	public static function from_url (URL $url): string
	{
		return QRWrapper::generate_qr(
			QRWrapper::cache_directory()."/qr-".$url->handle().".png",
			$url->full_handle()
		);
	}
}


?>
