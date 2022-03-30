<?php declare(strict_types=1); namespace NslAPI;


require_once(__DIR__."/../process-api-query.php");
require_once(__DIR__."/../../settings/settings.php");


final class ProcessAPIQueryTest extends \PHPUnit\Framework\TestCase
{
	const URL = "https://ugr.es";


	protected function setUp (): void
	{
		// This is obviously a testing trick, don't try in production
		$_SERVER["REMOTE_ADDR"] = \NslSettings\ALLOWED_IPS[0];
	}


	public function test_queries_from_invalid_ip_return_an_error (): void
	{
		global $ERR_INVALID_IP;
		$_SERVER["REMOTE_ADDR"] = "1.1.1.1";

		$query    = new APIQuery("", "");
		$response = process_api_query($query);

		$this->assertFalse($response->success());
		$this->assertEquals($ERR_INVALID_IP(), $response->error());
	}


	public function test_queries_without_url_return_an_error (): void
	{
		global $ERR_NO_URL;
		$query    = new APIQuery("", "");
		$response = process_api_query($query);

		$this->assertFalse($response->success());
		$this->assertEquals($ERR_NO_URL(), $response->error());
	}


	public function test_queries_with_an_invalid_url_return_an_error (): void
	{
		global $ERR_INVALID_URL;
		$query    = new APIQuery("invalid-url", "");
		$response = process_api_query($query);

		$this->assertFalse($response->success());
		$this->assertEquals($ERR_INVALID_URL(), $response->error());
	}


	public function test_queries_with_an_excesively_short_handle_return_an_error (): void
	{
		global $ERR_INVALID_HANDLE_LEN;
		$query    = new APIQuery(ProcessAPIQueryTest::URL, "ey");
		$response = process_api_query($query);

		$this->assertFalse($response->success());
		$this->assertEquals($ERR_INVALID_HANDLE_LEN(), $response->error());
	}


	public function test_queries_with_a_valid_url_dont_return_an_error (): void
	{
		$query    = new APIQuery(ProcessAPIQueryTest::URL, "");
		$response = process_api_query($query);

		$this->assertTrue($response->success());
	}
}


?>
