<?php declare(strict_types=1);


require_once("vendor/autoload.php");
require_once("php/url.php");


use MongoDB\Client as Mongo;
use MongoDB\Collection as Collection;
use PHPUnit\Framework\TestCase;


final class URLDatabaseTest extends TestCase
{
	private URL $url;
	private Mongo $mongo;
	private Collection $url_collection;
	private Collection $log_collection;


	protected function setUp (): void
	{
		$this->url        = URL::from_form("https://www.ugr.es/");
		$this->mongo      = new Mongo("mongodb://localhost:27017");
		$this->url_collection = $this->mongo->neosluger->urls;
		$this->log_collection = $this->mongo->neosluger->access_logs;
	}


	public function test_new_urls_are_added_to_the_database (): void
	{
		$result = $this->url_collection->find(["handle" => $this->url->handle()]);
		$this->assertEquals(1, count($result->toArray()));
	}


	public function test_the_database_stores_all_the_urls_data_members (): void
	{
		$result     = $this->url_collection->find(["handle" => $this->url->handle()]);
		$url_fields = $result->toArray()[0];

		$this->assertEquals($url_fields["destination"], $this->url->destination());
		$this->assertEquals($url_fields["handle"],      $this->url->handle());
		$this->assertEquals($url_fields["password"],    $this->url->password());
	}


	public function test_a_new_url_object_can_be_created_from_a_database_result (): void
	{
		$result     = $this->url_collection->find(["handle" => $this->url->handle()]);
		$url_fields = $result->toArray()[0];
		$new_url    = URL::from_db_result($url_fields);

		$this->assertEquals($url_fields["destination"], $new_url->destination());
		$this->assertEquals($url_fields["handle"],      $new_url->handle());
		$this->assertEquals($url_fields["password"],    $new_url->password());
	}


	public function test_a_new_url_adds_its_creation_datetime_into_its_log (): void
	{
		$result = $this->log_collection->find(["handle" => $this->url->handle()]);

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
		$result = $this->log_collection->find(["handle" => $this->url->handle()]);

		$log_fields = $result->toArray();
		$first_log  = $log_fields[0];
		$accesses   = iterator_to_array($first_log["accesses"]);

		$this->assertEquals(2, count($accesses));

		$logged_datetime = new DateTime($accesses[1]);
		$this->assertEquals($logged_datetime, $access_datetime);
	}
}


?>
