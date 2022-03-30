<?php declare(strict_types=1); namespace NslDB;


require_once(__DIR__."/../dummy-db.php");
require_once(__DIR__."/../../core/url.php");


final class DummyDBTest extends \PHPUnit\Framework\TestCase
{
	private DummyDB $db;
	private \DateTime $datetime;
	private \Nsl\URL $url;


	protected function setUp (): void
	{
		$this->db = new DummyDB;
		$this->datetime = new \Datetime("NOW", new \DateTimeZone(date('T')));
		$this->url = new \Nsl\URL("https://ugr.es/", $this->datetime, "test-url");
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

		$this->assertContainsOnlyInstancesOf(\Nsl\URL::class, $this->db->urls());
		foreach ($this->db->logs() as $log)
			$this->assertContainsOnlyInstancesOf(\DateTime::class, $log);
	}


	public function test_url_can_be_retrieved_after_inserting_it (): void
	{
		$result = $this->db->register_new_url($this->url);
		$this->assertTrue($result->ok());

		$this->assertContainsOnlyInstancesOf(\Nsl\URL::class, $this->db->urls());
		foreach ($this->db->logs() as $log)
			$this->assertContainsOnlyInstancesOf(\DateTime::class, $log);

		$retrieved_url = $this->db->find_url_by_handle($this->url->handle())->unwrap();
		$retrieved_log = $this->db->find_urls_logged_accesses($this->url)->unwrap();

		$this->assertEquals($retrieved_url,    $this->url);
		$this->assertEquals($retrieved_log[0], $this->url->creation_datetime());
	}


	public function test_two_urls_can_be_inserted_and_retrieved (): void
	{
		$other_datetime = new \Datetime("NOW", new \DateTimeZone(date('T')));
		$other_url = new \Nsl\URL("https://ugr.es/", $other_datetime, "ello");

		$this->db->register_new_url($this->url);
		$this->db->register_new_url($other_url);

		$this->assertContainsOnlyInstancesOf(\Nsl\URL::class, $this->db->urls());
		foreach ($this->db->logs() as $log)
			$this->assertContainsOnlyInstancesOf(\DateTime::class, $log);

		$retrieved_url       = $this->db->find_url_by_handle($this->url->handle())->unwrap();
		$other_retrieved_url = $this->db->find_url_by_handle($other_url->handle())->unwrap();

		$this->assertEquals($retrieved_url,       $this->url);
		$this->assertEquals($other_retrieved_url, $other_url);
	}


	public function test_urls_access_can_be_logged_and_retrieved (): void
	{
		$other_datetime = new \Datetime("NOW", new \DateTimeZone(date('T')));
		$datetimes = [$this->datetime, $other_datetime];
		$this->db->register_new_url($this->url);

		$result = $this->db->log_access_to_url($this->url, $other_datetime);
		$this->assertTrue($result->ok());

		$this->assertContainsOnlyInstancesOf(\Nsl\URL::class, $this->db->urls());
		foreach ($this->db->logs() as $log)
			$this->assertContainsOnlyInstancesOf(\DateTime::class, $log);

		$retrieved_datetimes = $this->db->find_urls_logged_accesses($this->url)->unwrap();
		$this->assertEquals($retrieved_datetimes, $datetimes);
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
