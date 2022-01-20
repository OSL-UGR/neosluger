<?php declare(strict_types=1);


require_once("vendor/autoload.php");
require_once("php/const.php");
require_once("php/new_api.php");


use PHPUnit\Framework\TestCase;


final class NewAPITest extends TestCase
{
	private string $url    = "https://ugr.es/";
	private string $handle = "my-handle_01";
	private NewAPIQuery $query;
	private NewAPIQuery $query_with_handle;
	private NewAPIQuery $empty_query;
	private NewAPIResponse $empty_response;


	protected function setUp (): void
	{
		$this->query             = new NewAPIQuery($this->url);
		$this->query_with_handle = new NewAPIQuery($this->url, $this->handle);
		$this->empty_query       = new NewAPIQuery;
		$this->empty_response    = new NewAPIResponse();
	}


	public function test_query_has_two_fields (): void
	{
		$this->assertEmpty($this->empty_query->url());
		$this->assertEmpty($this->empty_query->handle());
	}


	public function test_query_is_constructed_with_its_fields (): void
	{
		$this->assertEquals($this->query_with_handle->url(),    $this->url);
		$this->assertEquals($this->query_with_handle->handle(), $this->handle);
	}


	public function test_response_fails_if_built_without_url (): void
	{
		$this->assertEmpty($this->empty_response->url());
		$this->assertFalse($this->empty_response->success());
		$this->assertEquals($this->empty_response->errormsg(), NewAPIResponse::MSG_NO_URL);
	}


	public function test_if_response_succeeds_its_errormsg_is_empty (): void
	{
		// The url should be a short one but we don't mind in this test
		$response = new NewAPIResponse($this->url);

		$this->assertTrue($response->success());
		$this->assertEmpty($response->errormsg());
	}


	public function test_response_can_be_json_encoded_and_decoded (): void
	{
		$response_json = $this->empty_response->json_encode();
		$this->assertEquals(json_last_error(), JSON_ERROR_NONE);

		$decoded_response = json_decode($response_json, true);
		$this->assertEquals(json_last_error(), JSON_ERROR_NONE);

		$this->assertEmpty($decoded_response["url"]);
		$this->assertFalse($decoded_response["success"]);
		$this->assertEquals($decoded_response["errormsg"], NewAPIResponse::MSG_NO_URL);
	}


	public function test_api_gets_query_returns_response (): void
	{
		$response = NewAPI::process($this->query);
		$this->assertInstanceOf(NewAPIResponse::class, $response);
	}


	public function test_processing_query_without_url_returns_error (): void
	{
		$response = NewAPI::process($this->empty_query);

		$this->assertEmpty($response->url());
		$this->assertFalse($response->success());
		$this->assertEquals($response->errormsg(), NewAPIResponse::MSG_NO_URL);
	}


	public function test_processing_query_with_url_returns_short_url (): void
	{
		$response = NewAPI::process($this->query);
		$regex    = "/" . preg_replace("/\//", "\/", Neosluger\SITE_ADDRESS) . "[a-zA-Z0-9_\-]+/";

		$this->assertEquals(1, preg_match($regex, $response->url()));
		$this->assertTrue($response->success());
		$this->assertEmpty($response->errormsg());
	}


	public function test_processing_query_with_invalid_url_returns_error (): void
	{
		$response = NewAPI::process(new NewAPIQuery("Never gonna give you up!"));

		$this->assertEmpty($response->url());
		$this->assertFalse($response->success());
		$this->assertEquals($response->errormsg(), NewAPIResponse::MSG_INVALID_URL);
	}


	public function test_processing_query_with_duplicate_handle_returns_error (): void
	{
		$response = NewAPI::process($this->query_with_handle);
		$response = NewAPI::process($this->query_with_handle);

		$this->assertEmpty($response->url());
		$this->assertFalse($response->success());
		$this->assertEquals($response->errormsg(), NewAPIResponse::MSG_DUPLICATE_HANDLE);
	}
}


?>
