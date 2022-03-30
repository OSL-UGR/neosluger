<?php declare(strict_types=1); namespace NslAPI;


require_once(__DIR__."/../api-response.php");


final class APIResponseTest extends \PHPUnit\Framework\TestCase
{
	private const ERROR = "Testing error!";
	private const URL   = "https://ugr.es/";

	private APIResponse $error_response;
	private APIResponse $value_response;

	protected function setUp (): void
	{
		$this->error_response = APIResponse::from_error(APIResponseTest::ERROR);
		$this->value_response = APIResponse::from_value(APIResponseTest::URL);
	}


	public function test_correct_response_is_successful (): void
	{
		$this->assertTrue($this->value_response->success());
	}


	public function test_error_response_is_not_successful (): void
	{
		$this->assertFalse($this->error_response->success());
	}


	public function test_url_from_correct_response_can_be_retrieved (): void
	{
		$this->assertEquals(APIResponseTest::URL, $this->value_response->url());
	}


	public function test_error_from_error_response_can_be_retrieved (): void
	{
		$this->assertEquals(APIResponseTest::ERROR, $this->error_response->error());
	}


	public function test_error_from_correct_response_is_empty (): void
	{
		$this->assertEmpty($this->value_response->error());
	}


	public function test_url_from_error_response_is_empty (): void
	{
		$this->assertEmpty($this->error_response->url());
	}


	public function test_two_responses_from_the_same_data_are_equal (): void
	{
		$error_response = APIResponse::from_error(APIResponseTest::ERROR);
		$value_response = APIResponse::from_value(APIResponseTest::URL);

		$this->assertEquals($this->error_response,            $error_response);
		$this->assertEquals($this->error_response->error(),   $error_response->error());
		$this->assertEquals($this->error_response->success(), $error_response->success());
		$this->assertEquals($this->error_response->url(),     $error_response->url());

		$this->assertEquals($this->value_response,            $value_response);
		$this->assertEquals($this->value_response->error(),   $value_response->error());
		$this->assertEquals($this->value_response->success(), $value_response->success());
		$this->assertEquals($this->value_response->url(),     $value_response->url());
	}


	public function test_two_responses_from_different_data_are_not_equal (): void
	{
		$error_response = APIResponse::from_error("Weird error!!!");
		$value_response = APIResponse::from_value("https://osl.ugr.es/");

		$this->assertNotEquals($this->error_response,          $error_response);
		$this->assertNotEquals($this->error_response->error(), $error_response->error());
		$this->assertEquals($this->error_response->success(),  $error_response->success());
		$this->assertEquals($this->error_response->url(),      $error_response->url());

		$this->assertNotEquals($this->value_response,         $value_response);
		$this->assertEquals($this->value_response->error(),   $value_response->error());
		$this->assertEquals($this->value_response->success(), $value_response->success());
		$this->assertNotEquals($this->value_response->url(),  $value_response->url());
	}


	public function test_responses_can_be_enconded_into_json (): void
	{
		$error_response = [
			"url"     => $this->error_response->url(),
			"success" => $this->error_response->success(),
			"error"   => $this->error_response->error(),
		];

		$value_response = [
			"url"     => $this->value_response->url(),
			"success" => $this->value_response->success(),
			"error"   => $this->value_response->error(),
		];

		$this->assertEquals(json_encode($error_response), $this->error_response->json_encode());
	}
}


?>
