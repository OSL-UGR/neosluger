<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/../../database/dummy-db.php");
require_once(__DIR__."/../../settings.php");
require_once(__DIR__."/../url-interactor.php");


final class URLInteractorTest extends \PHPUnit\Framework\TestCase
{
	private const HANDLE = "neosluger-test-handle";
	private const DESTINATION = "https://ugr.es";

	private URLGateway    $database;
	private URLInteractor $interactor;


	protected function setUp (): void
	{
		$this->database = new \NeoslugerDB\DummyDB();
		$this->interactor = new URLInteractor($this->database);
	}


	public function test_url_can_be_inserted_without_its_handle (): void
	{
		$this->assertNotNull($this->interactor->register_new_url(URLInteractorTest::DESTINATION));
	}


	/* public function test_url_can_be_inserted_with_its_handle (): void */
	/* { */
	/* 	$this->assertNotNull($this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)); */
	/* } */


	/* public function test_requested_handle_must_be_between_length_bounds (): void */
	/* { */
	/* 	$short_handle = ""; */
	/* 	$long_handle  = ""; */

	/* 	for ($i = 0; $i < MIN_HANDLE_LEN-1; ++$i) */
	/* 		$short_handle .= "s"; */

	/* 	for ($i = 0; $i < MAX_HANDLE_LEN+1; ++$i) */
	/* 		$long_handle .= "s"; */

	/* 	$short_handle_result = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, $short_handle); */
	/* 	$long_handle_result = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, $long_handle); */

	/* 	$this->assertFalse($short_handle_result->ok()); */
	/* 	$this->assertFalse($long_handle_result->ok()); */
	/* } */


	/* public function test_url_can_be_inserted_more_than_once_without_handle_but_with_same_destination (): void */
	/* { */
	/* 	$this->assertNotNull($this->interactor->register_new_url(URLInteractorTest::DESTINATION)); */
	/* 	$this->assertNotNull($this->interactor->register_new_url("https://osl.ugr.es")); */
	/* } */


	/* public function test_url_cannnot_be_inserted_twice_with_same_handle (): void */
	/* { */
	/* 	$this->assertNotNull($this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)); */

	/* 	$register_again_result_1 = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE); */
	/* 	$register_again_result_2 = $this->interactor->register_new_url("https://osl.ugr.es", URLInteractorTest::HANDLE); */

	/* 	$this->assertFalse($register_again_result_1->ok()); */
	/* 	$this->assertFalse($register_again_result_2->ok()); */
	/* } */


	/* public function test_new_url_is_returned_after_insertion (): void */
	/* { */
	/* 	$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION)->unwrap(); */
	/* 	$this->assertEquals($url->destination(), URLInteractorTest::DESTINATION); */

	/* 	$other_destination = "https://osl.ugr.es"; */
	/* 	$other_url = $this->interactor->register_new_url($other_destination)->unwrap(); */
	/* 	$this->assertEquals($other_url->destination(), $other_destination); */
	/* } */


	/* public function test_new_url_with_handle_is_returned_after_insertion (): void */
	/* { */
	/* 	$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap(); */

	/* 	$this->assertEquals($url->handle(), URLInteractorTest::HANDLE); */
	/* 	$this->assertEquals($url->destination(), URLInteractorTest::DESTINATION); */

	/* 	$other_handle = "neosluger-test-handle2"; */
	/* 	$other_destination = "https://osl.ugr.es"; */
	/* 	$other_url = $this->interactor->register_new_url($other_destination, $other_handle)->unwrap(); */

	/* 	$this->assertEquals($other_url->handle(), $other_handle); */
	/* 	$this->assertEquals($other_url->destination(), $other_destination); */
	/* } */


	/* public function test_url_can_be_retrieved_after_insertion (): void */
	/* { */
	/* 	$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION)->unwrap(); */
	/* 	$retrieved_url = $this->interactor->find_url_by_handle($url->handle())->unwrap(); */

	/* 	$this->assertEquals($retrieved_url->handle(), $url->handle()); */
	/* 	$this->assertEquals($retrieved_url->destination(), $url->destination()); */

	/* 	$other_destination = "https://ugr.es"; */
	/* 	$other_url = $this->interactor->register_new_url($other_destination)->unwrap(); */
	/* 	$other_retrieved_url = $this->interactor->find_url_by_handle($other_url->handle())->unwrap(); */

	/* 	$this->assertEquals($other_retrieved_url->handle(), $other_url->handle()); */
	/* 	$this->assertEquals($other_retrieved_url->destination(), $other_url->destination()); */
	/* 	$this->assertNotEquals($url->handle(), $other_url->handle()); */
	/* } */


	/* public function test_url_with_handle_can_be_retrieved_after_insertion (): void */
	/* { */
	/* 	$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap(); */
	/* 	$retrieved_url = $this->interactor->find_url_by_handle(URLInteractorTest::HANDLE)->unwrap(); */

	/* 	$this->assertEquals($retrieved_url->handle(), $url->handle()); */
	/* 	$this->assertEquals($retrieved_url->destination(), $url->destination()); */

	/* 	$other_handle = "neosluger-test-handle2"; */
	/* 	$other_destination = "https://ugr.es"; */
	/* 	$other_url = $this->interactor->register_new_url($other_destination, $other_handle)->unwrap(); */
	/* 	$other_retrieved_url = $this->interactor->find_url_by_handle($other_handle)->unwrap(); */

	/* 	$this->assertEquals($other_retrieved_url->handle(), $other_url->handle()); */
	/* 	$this->assertEquals($other_retrieved_url->destination(), $other_url->destination()); */
	/* } */


	/* public function test_non_inserted_url_cannot_be_retrieved (): void */
	/* { */
	/* 	$result = $this->interactor->find_url_by_handle("does-not-exist"); */
	/* 	$this->assertFalse($result->ok()); */
	/* } */


	/* public function test_registered_url_can_be_queried_from_the_database (): void */
	/* { */
	/* 	$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap(); */
	/* 	$retrieved_url = $this->database->find_url_by_handle(URLInteractorTest::HANDLE); */

	/* 	$this->assertEquals($url, $retrieved_url); */
	/* } */


	/* public function test_retrieved_url_can_be_queried_from_the_database (): void */
	/* { */
	/* 	$this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE); */

	/* 	$url_result = $this->interactor->find_url_by_handle(URLInteractorTest::HANDLE); */
	/* 	$db_url = $this->database->find_url_by_handle(URLInteractorTest::HANDLE); */

	/* 	$this->assertEquals($url_result->unwrap(), $db_url); */
	/* } */


	/* public function test_logging_access_to_url_saves_it_in_the_database (): void */
	/* { */
	/* 	$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap(); */
	/* 	$this->assertEquals(1, count($this->database->find_urls_logged_accesses($url))); */

	/* 	$this->interactor->log_access_to_url($url); */
	/* 	$this->assertEquals(2, count($this->database->find_urls_logged_accesses($url))); */
	/* } */


	/* public function test_logged_access_to_url_can_be_queried_from_the_database (): void */
	/* { */
	/* 	$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap(); */

	/* 	$datetime1 = $this->interactor->log_access_to_url($url)->unwrap(); */
	/* 	$log = $this->database->find_urls_logged_accesses($url); */
	/* 	$datetime2 = end($log); */

	/* 	$this->assertEquals($datetime1, $datetime2); */
	/* } */


	/* public function test_urls_full_log_can_be_queried (): void */
	/* { */
	/* 	$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap(); */

	/* 	$this->assertNotEmpty($this->interactor->find_urls_logged_accesses($url)->unwrap()); */
	/* } */


	/* public function test_logs_cannot_be_retrieved_from_non_inserted_url (): void */
	/* { */
	/* 	$url = new URL("https://ugr.es/", new \DateTime("NOW"), "test-handle"); */

	/* 	$this->expectException(\LogicException::class); */
	/* 	$this->interactor->find_urls_logged_accesses($url)->unwrap(); */
	/* } */


	/* public function test_log_from_newly_inserted_url_contains_its_creation_date (): void */
	/* { */
	/* 	$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap(); */
	/* 	$logs = $this->interactor->find_urls_logged_accesses($url)->unwrap(); */

	/* 	$this->assertEquals(1, count($logs)); */
	/* 	$this->assertEquals($logs[0], $url->creation_datetime()); */
	/* } */


	/* public function test_accessing_a_url_after_inserting_it_grows_its_log (): void */
	/* { */
	/* 	$url = $this->interactor->register_new_url(URLInteractorTest::DESTINATION, URLInteractorTest::HANDLE)->unwrap(); */
	/* 	$datetime = $this->interactor->log_access_to_url($url)->unwrap(); */
	/* 	$logs = $this->interactor->find_urls_logged_accesses($url)->unwrap(); */

	/* 	$this->assertEquals(2, count($logs)); */
	/* 	$this->assertEquals($logs[0], $url->creation_datetime()); */
	/* 	$this->assertEquals($logs[1], $datetime); */
	/* } */
}


?>
