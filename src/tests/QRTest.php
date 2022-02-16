<?php declare(strict_types=1);


require_once("vendor/autoload.php");
require_once("php/qr.php");


final class QRTest extends PHPUnit\Framework\TestCase
{
	public function test_is_saved_in_cache_directory (): void
	{
		$this->assertDirectoryExists("./" . QRWrapper::cache_directory());
	}


	public function test_can_be_generated_from_url (): void
	{
		$url  = URL::from_form("https://www.ugr.es/");
		$path = QRWrapper::from_url($url);

		$this->assertNotEmpty($path);
		$this->assertFileExists($path);
	}


	public function test_can_be_generated_from_string (): void
	{
		$path = QRWrapper::from_string("No need to make it an URL!");

		$this->assertNotEmpty($path);
		$this->assertFileExists($path);
	}


	public function test_can_be_generated_from_the_same_query_multiple_times (): void
	{
		$url  = URL::from_form("https://www.ugr.es/");
		$path = QRWrapper::from_url($url);
		$path = QRWrapper::from_url($url);

		$this->assertNotEmpty($path);
		$this->assertFileExists($path);

		$path = QRWrapper::from_string("No need to make it an URL!");
		$path = QRWrapper::from_string("No need to make it an URL!");

		$this->assertNotEmpty($path);
		$this->assertFileExists($path);
	}
}


?>
