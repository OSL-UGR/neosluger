<?php declare(strict_types=1);


require_once("vendor/autoload.php");
require_once("php/url.php");


use PHPUnit\Framework\TestCase;


final class URLDatabaseTest extends TestCase
{
	private URL $url;


	protected function setUp (): void
	{
		$this->url = URL::from_form("https://www.ugr.es/");
	}


	public function test_new_urls_are_added_to_the_database (): void
	{
		$result = Neosluger\URL_COLLECTION()->find(["handle" => $this->url->handle()]);
		$this->assertEquals(1, count($result->toArray()));
	}


	public function test_the_database_stores_all_the_urls_data_members (): void
	{
		$result     = Neosluger\URL_COLLECTION()->find(["handle" => $this->url->handle()]);
		$url_fields = $result->toArray()[0];

		$this->assertEquals($url_fields["destination"], $this->url->destination());
		$this->assertEquals($url_fields["handle"],      $this->url->handle());
	}


	public function test_a_new_url_object_can_be_created_from_a_database_result (): void
	{
		$result     = Neosluger\URL_COLLECTION()->find(["handle" => $this->url->handle()]);
		$url_fields = $result->toArray()[0];
		$new_url    = URL::from_db_result($url_fields);

		$this->assertEquals($url_fields["destination"], $new_url->destination());
		$this->assertEquals($url_fields["handle"],      $new_url->handle());
	}


	public function test_a_new_url_adds_its_creation_datetime_into_its_log (): void
	{
		$result = Neosluger\LOG_COLLECTION()->find(["handle" => $this->url->handle()]);

		$log_fields = $result->toArray();
		$this->assertEquals(1, count($log_fields));

		$first_log = $log_fields[0];
		$accesses  = iterator_to_array($first_log["accesses"]);
		$datetime  = new DateTime($accesses[0]);

		$this->assertEquals($datetime, $this->url->creation_datetime());
	}


	public function test_accessing_a_url_logs_its_access_time (): void
	{
		$access_datetime = $this->url->log_access();
		$result = Neosluger\LOG_COLLECTION()->find(["handle" => $this->url->handle()]);

		$log_fields = $result->toArray();
		$first_log  = $log_fields[0];
		$accesses   = iterator_to_array($first_log["accesses"]);

		$this->assertEquals(2, count($accesses));

		$logged_datetime = new DateTime($accesses[1]);
		$this->assertEquals($logged_datetime, $access_datetime);
	}


	public function test_duplicate_urls_create_an_invalid_url (): void
	{
		$handle = "PHPUnit-handle_url10";
		$result = Neosluger\URL_COLLECTION()->find(["handle" => $handle]);

		if (count($result->toArray()) == 0)
			URL::from_form("https://www.ugr.es/", $handle);

		$new_url = URL::from_form("https://www.ugr.es/", $handle);
		$this->assertTrue($new_url->is_null());
		$this->assertTrue($new_url->is_duplicate());
	}
}


?>
