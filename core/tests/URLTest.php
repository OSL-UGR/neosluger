<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/../../vendor/autoload.php");
require_once(__DIR__."/../url.php");


final class URLTest extends \PHPUnit\Framework\TestCase
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


	public function test_a_null_url_can_be_constructed (): void
	{
		$new_url = URL::from_null();

		$this->assertTrue($new_url->is_null());
		$this->assertEquals("", $new_url->destination());
		$this->assertEquals("", $new_url->handle());
	}


	public function test_url_build_from_not_url_string_makes_it_null (): void
	{
		$new_url = URL::from_form("ROFL COPTER!!!");

		$this->assertTrue($new_url->is_null());
		$this->assertEquals("", $new_url->destination());
		$this->assertEquals("", $new_url->handle());
	}


	public function test_url_handle_is_set_on_construction (): void
	{
		$handle = $this->url->handle();
		$this->assertEquals(strlen($handle), HASH_LENGTH);
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
			SITE_ADDRESS . $handle
		);
	}


	public function test_a_custom_handle_can_be_passed_on_construction (): void
	{
		// Create a random handle every time to avoid duplicate errors
		$datetime = new \DateTime("NOW", new \DateTimeZone(date("T")));
		$handle   = sha1($datetime->format("Y-m-d H:i:s.u"));
		$new_url  = URL::from_form($this->destination_text, $handle);

		$this->assertEquals($new_url->handle(), $handle);
	}


	public function test_url_handle_must_be_at_least_five_characters_long (): void
	{
		$handle  = "hey";
		$new_url = URL::from_form($this->destination_text, $handle);

		$this->assertTrue($new_url->is_null());
	}


	public function test_url_handle_must_be_at_most_fifty_characters_long (): void
	{
		$handle  = "012345678901234567890123456789012345678901234567890"; // 51 chars
		$new_url = URL::from_form($this->destination_text, $handle);

		$this->assertTrue($new_url->is_null());
	}
}


?>
