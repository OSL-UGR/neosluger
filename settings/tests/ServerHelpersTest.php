<?php declare(strict_types=1); namespace NslSettings;


require_once(__DIR__."/../server-helpers.php");


final class ServerHelpersTest extends \PHPUnit\Framework\TestCase
{
	private const URI = "/test/12345/";

	public function test_zeroth_item_returns_whole_uri (): void
	{
		$result = parse_request_uri_nth_item(ServerHelpersTest::URI, 0);
		$this->assertEquals(ServerHelpersTest::URI, $result);
	}


	public function test_first_item_can_be_parsed (): void
	{
		$result = parse_request_uri_nth_item(ServerHelpersTest::URI, 1);
		$this->assertEquals("test", $result);
	}


	public function test_second_item_can_be_parsed (): void
	{
		$result = parse_request_uri_nth_item(ServerHelpersTest::URI, 2);
		$this->assertEquals("12345", $result);
	}


	public function test_allowed_ip_is_allowed (): void
	{
		foreach (ALLOWED_IPS as $ip)
			$this->assertTrue(user_ip_is_allowed($ip));
	}


	public function test_not_allowed_ip_are_not_allowed (): void
	{
		$this->assertFalse(user_ip_is_allowed("Not an IP"));
		$this->assertFalse(user_ip_is_allowed("1.1.1.1"));
	}
}


?>
