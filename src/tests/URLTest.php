<?php declare(strict_types=1);

require_once("vendor/autoload.php");
require_once("php/url.php");

use PHPUnit\Framework\TestCase;

final class URLTest extends TestCase
{
	private string $destination_text = "https://www.ugr.es/";
	private URL $url;

	protected function setUp (): void
	{
		$this->url = URL::from_form($this->destination_text);
	}

	public function test_url_is_built_from_a_string (): void
	{
		$new_url = URL::from_form($this->destination_text);
		$this->assertEquals($new_url->destination(), $this->destination_text);
	}

	public function test_url_build_from_not_url_string_aborts (): void
	{
		$this->expectException(InvalidArgumentException::class);
		URL::from_form("ROFL COPTER!!!");
	}

	public function test_url_handle_is_set_on_construction (): void
	{
		$handle = $this->url->handle();
		$this->assertEquals(strlen($handle), URL::HASH_LENGTH);
	}

	public function test_two_equal_destinations_dont_produce_the_same_handle (): void
	{
		$other_url = URL::from_form($this->destination_text);
		$handle1 = $this->url->handle();
		$handle2 = $other_url->handle();

		$this->assertNotEquals($handle1, $handle2);
	}

	public function test_full_handle_contains_sites_address (): void
	{
		$handle = $this->url->handle();
		$this->assertEquals(
			$this->url->full_handle(),
			URL::SITE_ADDRESS . $handle
		);
	}

	public function test_a_custom_handle_can_be_passed_on_construction (): void
	{
		$handle  = "My_Handle-01";
		$new_url = URL::from_form($this->destination_text, $handle);

		$this->assertEquals($new_url->handle(), $handle);
	}

	public function test_urls_dont_have_a_password_by_default (): void
	{
		$this->assertNotTrue($this->url->is_password_protected());
	}

	public function test_a_password_can_be_passed_on_construction (): void
	{
		$password = "DZSH3N)o"; // https://duckduckgo.com/?q=password+strong+8
		$new_url  = URL::from_form($this->destination_text, "", $password);

		$this->assertTrue($new_url->is_password_protected());
		$this->assertEquals($new_url->password(), $password);
	}
}

?>
