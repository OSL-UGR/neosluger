<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/url.php");


/** @interface QRRequestBoundary
  * @brief Access boundary to the core logic for working with QR code images.
  *
  * This interface defines how components external to the core logic interact
  * with it. It must be the only way outsiders access the core logic, so that
  * maintaining the code is infinitely easier and extentng the boundary
  * implementation is done seamlessly.
  */


interface QRRequestBoundary
{
	/** @fn generate_qr_from_string (string $string): string
	  * @brief Creates a QR code image from an arbitrary string.
	  *
	  * @param $string The arbitrary string to create the QR code from.
	  * @return string The path to the QR code image in the system.
	  */

	public function generate_qr_from_string (string $string): string;


	/** @fn generate_qr_from_url (URL $url): string
	  * @brief Creates a QR code image from the full handle of an URL.
	  *
	  * @param $url The url to create the QR code from.
	  * @return string The path to the QR code image in the system.
	  */

	public function generate_qr_from_url (URL $url): string;
}


?>
