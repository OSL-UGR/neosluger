<?php declare(strict_types=1); namespace NeoslugerDB;


require_once(__DIR__."/../mongodb-connector.php");
require_once(__DIR__."/../../core/url.php");
require_once(__DIR__."/../../settings/settings.php");


final class MongoDBTest extends \PHPUnit\Framework\TestCase
{
	private const LOGS_COLLECTION = "access_logs_tests";
	private const URLS_COLLECTION = "urls_tests";

	private MongoDBConnector $db;
	private \DateTime $datetime;
	private \Neosluger\URL $url;


	protected function setUp (): void
	{
		/*
		 * First of all, reset the testing tables on the database. We don't use
		 * the connector here because god forbid the next maintainer drops all
		 * collections in production.
		 */
		$client = new \MongoDB\Client(DEFAULT_ADDRESS);
		$client->selectCollection("neosluger", MongoDBTest::LOGS_COLLECTION)->drop();
		$client->selectCollection("neosluger", MongoDBTest::URLS_COLLECTION)->drop();

		$this->db = new MongoDBConnector();
		$this->db->set_logs_collection(MongoDBTest::LOGS_COLLECTION);
		$this->db->set_urls_collection(MongoDBTest::URLS_COLLECTION);

		$this->datetime = new \Datetime("NOW", new \DateTimeZone(date('T')));
		$this->url = new \Neosluger\URL("https://ugr.es/", $this->datetime, "test-url");
	}


	private function assertContainsNonStrict (mixed $needle, array $haystack): void
	{
		/*
		 * This is a less strict version of assertContains, which checks for
		 * strict triple equality and requires that both objects pointer values
		 * be equal. A workaround is checking that *at least* one item of the
		 * array is equal in its fields to the target object.
		 */

		$contains = false;
		$i = 0;

		while (!$contains && $i < count($haystack))
			$contains = ($needle == $haystack[$i++]);

		$this->assertTrue($contains);
	}


	public function test_is_empty_after_construction (): void
	{
		$this->assertEmpty($this->db->urls());
		$this->assertEmpty($this->db->logs());
	}


	public function test_isnt_empty_after_inserting_url (): void
	{
		$result = $this->db->register_new_url($this->url);
		$this->assertTrue($result->ok());

		$this->assertNotEmpty($this->db->urls());
		$this->assertNotEmpty($this->db->logs());

		$this->assertContainsOnlyInstancesOf(\Neosluger\URL::class, $this->db->urls());
		foreach ($this->db->logs() as $log)
			$this->assertContainsOnlyInstancesOf(\DateTime::class, $log);
	}


	public function test_url_can_be_retrieved_after_inserting_it (): void
	{
		$result = $this->db->register_new_url($this->url);
		$this->assertTrue($result->unwrap());

		$retrieved_url = $this->db->find_url_by_handle($this->url->handle())->unwrap();
		$retrieved_log = $this->db->find_urls_logged_accesses($this->url)->unwrap();

		$this->assertEquals($retrieved_url->handle(), $this->url->handle());
		$this->assertEquals($retrieved_url->destination(), $this->url->destination());

		foreach ($retrieved_log as $date)
			$date->setTimezone(new \DateTimeZone(date('T')));

		$this->assertContainsNonStrict($this->url->creation_datetime(), $retrieved_log);
	}


	public function test_two_urls_can_be_inserted_and_retrieved (): void
	{
		$other_datetime = new \Datetime("NOW", new \DateTimeZone(date('T')));
		$other_url = new \Neosluger\URL("https://ugr.es/", $other_datetime, "ello");

		$this->db->register_new_url($this->url);
		$this->db->register_new_url($other_url);

		$retrieved_url       = $this->db->find_url_by_handle($this->url->handle())->unwrap();
		$other_retrieved_url = $this->db->find_url_by_handle($other_url->handle())->unwrap();

		$this->assertEquals($retrieved_url->handle(),      $this->url->handle());
		$this->assertEquals($retrieved_url->destination(), $this->url->destination());

		$this->assertEquals($other_retrieved_url->handle(),      $other_url->handle());
		$this->assertEquals($other_retrieved_url->destination(), $other_url->destination());
	}


	public function test_urls_access_can_be_logged_and_retrieved (): void
	{
		$other_datetime = new \Datetime("NOW", new \DateTimeZone(date('T')));

		$this->db->register_new_url($this->url);
		$this->db->log_access_to_url($this->url, $other_datetime);

		$retrieved_datetimes = $this->db->find_urls_logged_accesses($this->url)->unwrap();

		$this->assertContainsNonStrict($this->datetime, $retrieved_datetimes);
		$this->assertContainsNonStrict($other_datetime, $retrieved_datetimes);
	}


	public function test_non_inserted_url_cannot_be_retrieved (): void
	{
		$this->expectException(\LogicException::class);
		$this->db->find_url_by_handle("does-not-exist")->unwrap();
	}


	public function test_logs_cannot_be_retrieved_from_non_inserted_url (): void
	{
		$this->expectException(\LogicException::class);
		$this->db->find_urls_logged_accesses($this->url)->unwrap();
	}
}


?>
