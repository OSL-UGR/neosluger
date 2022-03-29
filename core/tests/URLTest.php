<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/../url.php");
require_once(__DIR__."/../../settings/settings.php");


final class URLTest extends \PHPUnit\Framework\TestCase
{
	public function test_url_fields_can_be_consulted_after_construction (): void
	{
		$datetime    = new \Datetime("NOW", new \DateTimeZone(date('T')));
		$destination = "https://ugr.es/";
		$handle      = "tst1";
		$url         = new URL($destination, $datetime, $handle);

		$this->assertEquals($url->creation_datetime(), $datetime);
		$this->assertEquals($url->destination(),       $destination);
		$this->assertEquals($url->full_handle(),       SITE_ADDRESS . $handle);
		$this->assertEquals($url->handle(),            $handle);

		$other_datetime    = new \Datetime("NOW", new \DateTimeZone(date('T')));
		$other_destination = "https://osl.ugr.es/";
		$other_handle      = "tst2";
		$other_url         = new URL($other_destination, $other_datetime, $other_handle);

		$this->assertEquals($other_url->creation_datetime(), $other_datetime);
		$this->assertEquals($other_url->destination(),       $other_destination);
		$this->assertEquals($other_url->full_handle(),       SITE_ADDRESS . $other_handle);
		$this->assertEquals($other_url->handle(),            $other_handle);
	}
}


?>
