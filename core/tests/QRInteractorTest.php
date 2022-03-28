<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/../../database/dummy-db.php");
require_once(__DIR__."/../qr-interactor.php");

use chillerlan\QRCode\QRCode;


final class QRInteractorTest extends \PHPUnit\Framework\TestCase
{
	private const TEST_DIR  = "cache-tests/";
	private const QR_STRING = "test";

	private QRInteractor $interactor;
	private URL $url;


	protected function setUp (): void
	{
		$this->interactor = new QRInteractor(QRInteractorTest::TEST_DIR);
		$this->url = new URL("https://ugr.es/", new \DateTime("NOW"), "test-handle");
	}


	public function test_interactor_handles_file_in_a_directoy (): void
	{
		$this->assertDirectoryExists(QRInteractorTest::TEST_DIR);
	}


	public function test_path_to_interactor_can_be_queried (): void
	{
		$path = $this->interactor->qr_directory();
		$this->assertEquals($path, QRInteractorTest::TEST_DIR);
	}


	public function test_qr_can_be_generated_from_a_string (): void
	{
		$this->assertNotEmpty($this->interactor->generate_qr_from_string("test"));
	}


	public function test_creating_qr_from_string_returns_its_path (): void
	{
		$string1 = "test1";
		$string2 = "test2";

		$path1 = $this->interactor->generate_qr_from_string($string1);
		$path2 = $this->interactor->generate_qr_from_string($string2);

		$this->assertFileExists($path1);
		$this->assertFileExists($path2);
		$this->assertNotEquals($path1, $path2);
	}


	public function test_path_to_string_qr_is_a_valid_file (): void
	{
		$path = $this->interactor->generate_qr_from_string(QRInteractorTest::QR_STRING);
		$this->assertFileExists($path);

		$other_path = "cache-test-2";
		$other_interactor = new QRInteractor($other_path);
		$other_path = $other_interactor->generate_qr_from_string(QRInteractorTest::QR_STRING);
		$this->assertFileExists($other_path);
	}


	public function test_string_qr_file_size_isnt_zero (): void
	{
		$path = $this->interactor->generate_qr_from_string(QRInteractorTest::QR_STRING);
		$size = filesize($path);

		$this->assertGreaterThan(0, $size);
	}


	public function test_created_file_contains_a_string_qr_code_image (): void
	{
		$path = $this->interactor->generate_qr_from_string(QRInteractorTest::QR_STRING);
		$qr1  = file_get_contents($path);

		$qr2_options = $this->interactor->qr_options();
		(new QRCode($qr2_options))->render(QRInteractorTest::QR_STRING, $path);
		$qr2 = file_get_contents($path);

		$this->assertEquals($qr1, $qr2);
	}


	public function test_qrs_from_two_strings_dont_produce_the_same_file (): void
	{
		$path1 = $this->interactor->generate_qr_from_string(QRInteractorTest::QR_STRING);
		$path2 = $this->interactor->generate_qr_from_string(QRInteractorTest::QR_STRING."a");
		$qr1 = file_get_contents($path1);
		$qr2 = file_get_contents($path2);

		$this->assertNotEquals($qr1, $qr2);
	}


	public function test_qr_can_be_generated_from_a_url (): void
	{
		$this->assertNotEmpty($this->interactor->generate_qr_from_url($this->url));
	}


	public function test_creating_qr_from_url_returns_its_path (): void
	{
		$path = $this->interactor->generate_qr_from_url($this->url);
		$this->assertEquals($path, QRInteractorTest::TEST_DIR.$this->url->handle().".png");
	}


	public function test_path_to_url_qr_is_a_valid_file (): void
	{
		$path = $this->interactor->generate_qr_from_url($this->url);
		$this->assertFileExists($path);

		$other_path = "cache-test-2";
		$other_interactor = new QRInteractor($other_path);
		$other_path = $other_interactor->generate_qr_from_url($this->url);
		$this->assertFileExists($other_path);
	}


	public function test_url_qr_file_size_isnt_zero (): void
	{
		$path = $this->interactor->generate_qr_from_url($this->url);
		$size = filesize($path);

		$this->assertGreaterThan(0, $size);
	}


	public function test_created_file_contains_a_url_qr_code_image (): void
	{
		$qr_path = $this->interactor->generate_qr_from_url($this->url);
		$qr1 = file_get_contents($qr_path);

		$qr2_options = $this->interactor->qr_options();
		(new QRCode($qr2_options))->render($this->url->full_handle(), $qr_path);
		$qr2 = file_get_contents($qr_path);

		$this->assertEquals($qr1, $qr2);
	}


	public function test_qrs_from_two_urls_dont_produce_the_same_file (): void
	{
		$other_url = new URL("https://ugr.es/", new \DateTime("NOW"), "test-handle2");

		$path1 = $this->interactor->generate_qr_from_url($this->url);
		$path2 = $this->interactor->generate_qr_from_url($other_url);
		$qr1 = file_get_contents($path1);
		$qr2 = file_get_contents($path2);

		$this->assertNotEquals($qr1, $qr2);
	}
}


?>
