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
}


?>
