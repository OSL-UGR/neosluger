<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/qr-request-boundary.php");


/** @class QRInteractor
  * @brief `QRRequestBoundary` implementation to create QR code images.
  *
  * Consider changing the implementation to return a byte stream instead of
  * the path to the image. This way the codes can be returned to the presenter
  * in an storage-agnostic way.
  */

final class QRInteractor implements QRRequestBoundary
{
	/** Options to create the QR code images with. */
	private \chillerlan\QRCode\QROptions $qr_options;

	/** Cache directory where the images are stored. */
	private string $qr_directory;


	/** @fn __construct (string $path)
	  * @brief Constructs the QRInteractor.
	  *
	  * @param $path Cache directory where the images are stored.
	  */

	public function __construct (string $path)
	{
		$this->qr_directory = ((substr($path, -1) == "/") ? $path : $path."/");

		if (file_exists($this->qr_directory) && !is_dir($this->qr_directory))
			unlink($this->qr_directory);

		if (!file_exists($this->qr_directory))
			mkdir($this->qr_directory, 0775, true);

		$this->qr_options = new \chillerlan\QRCode\QROptions([
			"outputType"       => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
			"eccLevel"         => \chillerlan\QRCode\QRCode::ECC_L,
			"imageTransparent" => false,
			"pngCompression"   => 9,
		]);
	}


	/** @fn full_path_to_qr (string $filename): string
	  * @brief Composes the path to a QR code image from its filename.
	  *
	  * @param $filename QR image filename without its extension.
	  * @return string The path to the QR code with a PNG extension.
	  */

	private function full_path_to_qr (string $filename): string
	{
		return $this->qr_directory.$filename.".png";
	}


	/** @fn generate_qr_from_string (string $contents): string
	  * @brief Implementation of `QRRequestBoundary::generate_qr_from_string`.
	  */

	public function generate_qr_from_string (string $contents): string
	{
		$hash     = substr(sha1($contents), 0, \NslSettings\HASH_LENGTH);
		$filename = $this->full_path_to_qr($hash);

		(new \chillerlan\QRCode\QRCode($this->qr_options))->render($contents, $filename);
		return $filename;
	}


	/** @fn generate_qr_from_string (string $contents): string
	  * @brief Implementation of `QRRequestBoundary::generate_qr_from_url`.
	  */

	public function generate_qr_from_url (URL $url): string
	{
		$filename = $this->full_path_to_qr($url->handle());
		(new \chillerlan\QRCode\QRCode($this->qr_options))->render($url->full_handle(), $filename);

		return $filename;
	}


	/** @fn qr_directory (): string
	  * @brief DEBUG ONLY: Cache directory consultor.
	  *
	  * @return string Cache directory passed when constructing the interactor.
	  */

	public function qr_directory (): string
	{
		return $this->qr_directory;
	}


	/** @fn qr_options (): string
	  * @brief DEBUG ONLY: Options consultor.
	  *
	  * @return \chillerlan\QRCode\QROptions Options created when constructing the interactor.
	  */

	public function qr_options (): \chillerlan\QRCode\QROptions
	{
		return $this->qr_options;
	}
}


?>
