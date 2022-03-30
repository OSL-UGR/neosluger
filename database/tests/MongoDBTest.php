<?php declare(strict_types=1); namespace NslDB;


require_once(__DIR__."/db-tests.php");
require_once(__DIR__."/../mongodb-connector.php");


final class MongoDBTest extends DBTest
{
	private const LOGS_COLLECTION = "access_logs_tests";
	private const URLS_COLLECTION = "urls_tests";

	protected function setUp (): void
	{
		parent::setUp();

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
	}
}


?>
