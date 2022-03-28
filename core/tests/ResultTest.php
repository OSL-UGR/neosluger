<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/../result.php");


final class ResultTest extends \PHPUnit\Framework\TestCase
{
	private int $number = 1337;
	private string $msg = "A nice string";
	private string $error_description = "Oh no I failed!";

	private Result $ok_result;
	private Result $error_result;


	protected function setUp (): void
	{
		$this->ok_result    = Result::from_value($this->number);
		$this->error_result = Result::from_error($this->error_description);
	}


	public function test_correct_result_is_ok (): void
	{
		$result = Result::from_value($this->msg);

		$this->assertTrue($result->ok());
		$this->assertEquals($result->unwrap(), $this->msg);

		$result = Result::from_value($this->number);

		$this->assertTrue($result->ok());
		$this->assertEquals($result->unwrap(), $this->number);
	}


	public function test_error_result_is_not_ok (): void
	{
		$result = Result::from_error($this->error_description);

		$this->assertFalse($result->ok());
		$this->assertEquals($result->first_error(), $this->error_description);
	}


	public function test_if_ok_error_is_null (): void
	{
		$this->assertNull($this->ok_result->first_error());
	}


	public function test_error_result_throws_exception_on_unrwapping (): void
	{
		$this->expectException(\LogicException::class);
		$this->error_result->unwrap();
	}


	public function test_an_error_can_be_pushed_into_a_previous_one (): void
	{
		$next_error  = "The last call returned an error!";
		$error_stack = [$this->error_description, $next_error];

		$this->error_result->push_back($next_error);
		$this->assertEquals($this->error_result->errors(), $error_stack);
	}


	public function test_many_errors_can_be_stacked (): void
	{
		$error_stack = [$this->error_description];

		for ($i = 0; $i < 10; ++$i)
		{
			$errormsg = "Error ".$i;
			array_push($error_stack, $errormsg);
			$this->error_result->push_back($errormsg);
		}

		$this->assertEquals($this->error_result->errors(), $error_stack);
	}


	public function test_pushing_back_an_error_removes_any_value (): void
	{
		$errormsg = "Error msg";
		$this->ok_result->push_back($errormsg);

		$this->assertFalse($this->ok_result->ok());
		$this->assertEquals($this->ok_result->first_error(), $errormsg);
	}
}


?>
