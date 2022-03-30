<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/../url-interactor.php");
require_once(__DIR__."/../../database/dummy-db.php");
require_once(__DIR__."/../../settings/settings.php");


final class URLInteractorTest extends \PHPUnit\Framework\TestCase
{
	private const HANDLE = "neosluger-test-handle";
	private const DESTINATION = "https://ugr.es";

	private URLGateway    $database;
	private URLInteractor $interactor;


	protected function setUp (): void
	{
		$this->database   = new \NslDB\DummyDB();
		$this->interactor = new URLInteractor($this->database);
	}


	public function test_registering_a_url_witout_destination_return_an_error (): void
	{
		global $ERR_NO_DESTINATION;
		$result = $this->interactor->register_new_url("");

		$this->assertFalse($result->ok());
		$this->assertEquals($ERR_NO_DESTINATION(), $result->first_error());
	}


	public function test_registering_a_invalid_url_returns_an_error (): void
	{
		global $ERR_ILLEGAL_DESTINATION;
		$result = $this->interactor->register_new_url("invalid-url");

		$this->assertFalse($result->ok());
		$this->assertEquals($ERR_ILLEGAL_DESTINATION(), $result->first_error());
	}


	public function test_registering_a_url_with_an_excesively_short_handle_return_an_error (): void
	{
		global $ERR_ILLEGAL_HANDLE_LEN;
		$result = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, str_repeat("s", \NslSettings\MIN_HANDLE_LEN-1));

		$this->assertFalse($result->ok());
		$this->assertEquals($ERR_ILLEGAL_HANDLE_LEN(), $result->first_error());
	}


	public function test_registering_a_url_with_an_excesively_long_handle_return_an_error (): void
	{
		global $ERR_ILLEGAL_HANDLE_LEN;
		$result = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, str_repeat("l", \NslSettings\MAX_HANDLE_LEN+1));

		$this->assertFalse($result->ok());
		$this->assertEquals($ERR_ILLEGAL_HANDLE_LEN(), $result->first_error());
	}


	public function test_url_can_be_registered_more_than_once_without_handle_but_with_same_destination (): void
	{
		$this->assertTrue($this->interactor->register_new_url(URLInteractorTest::DESTINATION)->ok());
		$this->assertTrue($this->interactor->register_new_url(URLInteractorTest::DESTINATION)->ok());
	}


	public function test_a_url_cannnot_be_registered_twice_with_same_handle (): void
	{
		global $ERR_DUPLICATE_HANDLE;
		$this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE);

		$result1 = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE);
		$result2 = $this->interactor->register_new_url("https://osl.ugr.es", URLInteractorTest::HANDLE);

		$this->assertFalse($result1->ok());
		$this->assertFalse($result2->ok());

		$this->assertEquals($ERR_DUPLICATE_HANDLE(), $result1->first_error());
		$this->assertEquals($ERR_DUPLICATE_HANDLE(), $result2->first_error());
	}


	public function test_the_new_url_is_returned_after_registration (): void
	{
		$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap();

		$this->assertEquals(URLInteractorTest::DESTINATION, $url->destination());
		$this->assertEquals(URLInteractorTest::HANDLE,      $url->handle());
	}


	public function registering_two_urls_produces_two_different_urls (): void
	{
		$url1 = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap();
		$url2 = $this->interactor->register_new_url("https://osl.ugr.es", "other-test")->unwrap();

		$this->assertNotEquals($url1, $url2);
	}


	public function test_registering_a_url_witout_handle_creates_an_unique_one_for_it (): void
	{
		$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION)->unwrap();
		$this->assertEquals(\NslSettings\HANDLE_LENGTH, strlen($url->handle()));
	}


	public function test_querying_a_non_existent_url_returns_an_error (): void
	{
		$this->assertFalse($this->interactor->find_url_by_handle(URLInteractorTest::HANDLE)->ok());
	}


	public function test_querying_a_url_after_registering_it_returns_that_url (): void
	{
		$url       = $this->interactor->register_new_url(URLInteractorTest::DESTINATION)->unwrap();
		$found_url = $this->interactor->find_url_by_handle($url->handle())->unwrap();

		$this->assertEquals($url, $found_url);
	}


	public function test_querying_different_urls_return_different_results (): void
	{
		$url1 = $this->interactor->register_new_url(URLInteractorTest::DESTINATION)->unwrap();
		$url2 = $this->interactor->register_new_url(URLInteractorTest::DESTINATION)->unwrap();

		$found_url1 = $this->interactor->find_url_by_handle($url1->handle())->unwrap();
		$found_url2 = $this->interactor->find_url_by_handle($url2->handle())->unwrap();

		$this->assertNotEquals($found_url1, $found_url2);
	}


	public function test_urls_full_log_can_be_queried (): void
	{
		$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap();
		$this->assertTrue($this->interactor->find_urls_logged_accesses($url)->ok());
	}


	public function test_querying_log_from_non_registered_url_returns_an_error (): void
	{
		$url = new URL("https://ugr.es/", new \DateTime("NOW"), "test-handle");
		$this->assertFalse($this->interactor->find_urls_logged_accesses($url)->ok());
	}


	public function test_log_from_newly_registered_url_contains_its_creation_date (): void
	{
		$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap();
		$log = $this->interactor->find_urls_logged_accesses($url)->unwrap();

		$this->assertEquals(1, count($log));
		$this->assertEquals($log[0], $url->creation_datetime());
	}


	public function test_accessing_a_url_after_registered_it_grows_its_log (): void
	{
		$url      = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap();
		$datetime = $this->interactor->log_access_to_url($url)->unwrap();
		$logs     = $this->interactor->find_urls_logged_accesses($url)->unwrap();

		$this->assertEquals(2, count($logs));
		$this->assertEquals($logs[0], $url->creation_datetime());
		$this->assertEquals($logs[1], $datetime);
	}


	public function test_registered_url_can_be_queried_from_the_database (): void
	{
		$url    = $this->interactor->register_new_url(URLInteractorTest::DESTINATION)->unwrap();
		$db_url = $this->database->find_url_by_handle($url->handle())->unwrap();

		$this->assertEquals($url, $db_url);
	}


	public function test_retrieved_url_can_be_queried_from_the_database (): void
	{
		$this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE);

		$url_result = $this->interactor->find_url_by_handle(URLInteractorTest::HANDLE)->unwrap();
		$db_url     = $this->database->find_url_by_handle(URLInteractorTest::HANDLE)->unwrap();

		$this->assertEquals($url_result, $db_url);
	}


	public function test_logging_access_to_url_saves_it_in_the_database (): void
	{
		$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap();
		$this->assertEquals(1, count($this->database->find_urls_logged_accesses($url)->unwrap()));

		$this->interactor->log_access_to_url($url);
		$this->assertEquals(2, count($this->database->find_urls_logged_accesses($url)->unwrap()));
	}


	public function test_logged_access_to_url_can_be_queried_from_the_database (): void
	{
		$url         = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap();
		$datetime    = $this->interactor->log_access_to_url($url)->unwrap();
		$log         = $this->database->find_urls_logged_accesses($url)->unwrap();
		$db_datetime = end($log);

		$this->assertEquals($datetime, $db_datetime);
	}
}


?>
