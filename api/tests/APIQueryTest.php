<?php declare(strict_types=1); namespace NslAPI;


require_once(__DIR__."/../api-query.php");


final class APIQueryTest extends \PHPUnit\Framework\TestCase
{
	private const HANDLE = "https://ugr.es/";
	private const URL    = "Testing error!";

	private APIQuery $query;


	protected function setUp (): void
	{
		$this->query = new APIQuery(APIQueryTest::URL, APIQueryTest::HANDLE);
	}


	public function test_url_can_be_retrieved (): void
	{
		$this->assertEquals(APIQueryTest::URL, $this->query->url());
	}


	public function test_handle_be_retrieved (): void
	{
		$this->assertEquals(APIQueryTest::HANDLE, $this->query->handle());
	}


	public function test_two_responses_from_the_same_data_are_equal (): void
	{
		$query = new APIQuery(APIQueryTest::URL, APIQueryTest::HANDLE);

		$this->assertEquals($this->query,           $query);
		$this->assertEquals($this->query->handle(), $query->handle());
		$this->assertEquals($this->query->url(),    $query->url());
	}


	public function test_two_responses_from_different_data_are_not_equal (): void
	{
		$query = new APIQuery("https://osl.ugr.es/", "super-cool_handle");

		$this->assertNotEquals($this->query,           $query);
		$this->assertNotEquals($this->query->handle(), $query->handle());
		$this->assertNotEquals($this->query->url(),    $query->url());
	}
}


?>
