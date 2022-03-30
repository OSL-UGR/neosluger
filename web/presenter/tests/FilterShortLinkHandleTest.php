<?php declare(strict_types=1); namespace NslWeb;


require_once(__DIR__."/../filter-short-link-handle.php");


final class FilterShortLinkHandleTest extends \PHPUnit\Framework\TestCase
{
	private const SITE_ADDRESS = "localhost/";

	public function test_filtered_handle_contains_no_leading_or_trailing_blanks (): void
	{
		$handle = filter_short_link_handle("\t handle \t", FilterShortLinkHandleTest::SITE_ADDRESS);
		$this->assertEquals("handle", $handle);
	}


	public function test_filtered_handle_removes_leading_https (): void
	{
		$handle1 = filter_short_link_handle("\t https://handle \t", FilterShortLinkHandleTest::SITE_ADDRESS);
		$handle2 = filter_short_link_handle("\t http://handle  \t", FilterShortLinkHandleTest::SITE_ADDRESS);

		$this->assertEquals("handle", $handle1);
		$this->assertEquals("handle", $handle2);
	}


	public function test_filtered_handle_removes_site_address (): void
	{
		$handle1 = filter_short_link_handle("\t ".FilterShortLinkHandleTest::SITE_ADDRESS."handle         \t", FilterShortLinkHandleTest::SITE_ADDRESS);
		$handle2 = filter_short_link_handle("\t ".FilterShortLinkHandleTest::SITE_ADDRESS."handle         \t", FilterShortLinkHandleTest::SITE_ADDRESS);
		$handle3 = filter_short_link_handle("\t https://".FilterShortLinkHandleTest::SITE_ADDRESS."handle \t", FilterShortLinkHandleTest::SITE_ADDRESS);
		$handle4 = filter_short_link_handle("\t http://".FilterShortLinkHandleTest::SITE_ADDRESS."handle  \t", FilterShortLinkHandleTest::SITE_ADDRESS);

		$this->assertEquals("handle", $handle1);
		$this->assertEquals("handle", $handle2);
		$this->assertEquals("handle", $handle3);
		$this->assertEquals("handle", $handle4);
	}
}


?>
