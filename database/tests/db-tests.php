<?php declare(strict_types=1); namespace NslDB;


require_once(__DIR__."/../../core/url.php");


class DBTest extends \PHPUnit\Framework\TestCase
{
	protected mixed $db;

	private \Nsl\URL $url1;
	private \Nsl\URL $url2;


	protected function now (): \DateTime
	{
		return new \Datetime("NOW", new \DateTimeZone(date('T')));
	}


	protected function setUp (): void
	{
		$this->url1 = new \Nsl\URL("https://ugr.es/",    $this->now(), "test-url1");
		$this->url2 = new \Nsl\URL("https://oslugr.es/", $this->now(), "test-url2");
	}


	public function test_is_empty_before_starting_tests (): void
	{
		$this->assertEmpty($this->db->urls());
		$this->assertEmpty($this->db->logs());
	}


	public function test_a_new_url_can_be_inserted (): void
	{
		$result = $this->db->register_new_url($this->url1);
		$this->assertTrue($result->ok());
	}


	public function test_isnt_empty_after_inserting_url (): void
	{
		$this->db->register_new_url($this->url1);

		$this->assertNotEmpty($this->db->urls());
		$this->assertNotEmpty($this->db->logs());
	}


	public function test_urls_storage_contains_only_url_objects (): void
	{
		$this->db->register_new_url($this->url1);
		$this->assertContainsOnlyInstancesOf(\Nsl\URL::class, $this->db->urls());
	}


	public function test_logs_storage_contains_only_arrays_of_datetimes (): void
	{
		$this->db->register_new_url($this->url1);
		foreach ($this->db->logs() as $log)
			$this->assertContainsOnlyInstancesOf(\DateTime::class, $log);
	}


	public function test_non_inserted_url_cannot_be_retrieved (): void
	{
		$url = $this->db->find_url_by_handle("does-not-exist");
		$this->assertFalse($url->ok());
	}


	public function test_logs_cannot_be_retrieved_from_non_inserted_url (): void
	{
		$log = $this->db->find_urls_logged_accesses($this->url1);
		$this->assertFalse($log->ok());
	}


	public function test_url_can_be_retrieved_after_inserting_it (): void
	{
		$this->db->register_new_url($this->url1);
		$url = $this->db->find_url_by_handle($this->url1->handle())->unwrap();

		$this->assertEquals($this->url1, $url);
	}


	public function test_urls_log_can_be_retrieved_after_inserting_the_url (): void
	{
		$this->db->register_new_url($this->url1);
		$log = $this->db->find_urls_logged_accesses($this->url1)->unwrap();

		$this->assertEquals(1, count($log));
		$this->assertEquals($this->url1->creation_datetime(), $log[0]);
	}


	public function test_two_urls_can_be_inserted_and_retrieved (): void
	{
		$this->db->register_new_url($this->url1);
		$this->db->register_new_url($this->url2);

		$url1 = $this->db->find_url_by_handle($this->url1->handle())->unwrap();
		$url2 = $this->db->find_url_by_handle($this->url2->handle())->unwrap();

		$this->assertNotEquals($url1, $url2);
	}


	public function test_access_to_url_can_be_logged (): void
	{
		$this->db->register_new_url($this->url1);

		$access_dt = $this->now();
		$this->db->log_access_to_url($this->url1, $access_dt);

		$log = $this->db->find_urls_logged_accesses($this->url1)->unwrap();

		$this->assertEquals(2, count($log));
		$this->assertEquals($access_dt, $log[1]);
	}


	public function test_access_to_url_doesnt_affect_other_urls (): void
	{
		$this->db->register_new_url($this->url1);
		$this->db->register_new_url($this->url2);

		$this->db->log_access_to_url($this->url1, $this->now());

		$log1 = $this->db->find_urls_logged_accesses($this->url1)->unwrap();
		$log2 = $this->db->find_urls_logged_accesses($this->url2)->unwrap();

		$this->assertEquals(2, count($log1));
		$this->assertEquals(1, count($log2));
	}
}


?>
