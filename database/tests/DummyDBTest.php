<?php declare(strict_types=1); namespace NslDB;


require_once(__DIR__."/db-tests.php");
require_once(__DIR__."/../dummy-db.php");


final class DummyDBTest extends DBTest
{
	protected function setUp (): void
	{
		parent::setUp();
		$this->db = new DummyDB;
	}
}


?>
