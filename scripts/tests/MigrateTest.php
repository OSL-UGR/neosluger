<?php declare(strict_types=1); namespace NslScripts;


require_once(__DIR__."/../migrate.php");


final class MigrateTest extends \PHPUnit\Framework\TestCase
{
	private int $len;

	protected function setUp (): void
	{
		$this->len = strlen(OLDSTYLE_CHARS);
	}


	public function test_converting_index_to_oldstyle_handle_returns_four_character_string (): void
	{
		for ($i = 0; $i < $this->len*1e4; ++$i)
			$this->assertEquals(4, strlen(convert_autoincremented_index_to_oldstyle_handle($i)));
	}


	public function test_handles_from_numbers_lower_than_the_number_of_oldstyle_chars_contain_three_preceding_zero_index_chars (): void
	{
		for ($i = 0; $i < $this->len; ++$i)
		{
			$result = convert_autoincremented_index_to_oldstyle_handle($i);
			$this->assertEquals(OLDSTYLE_CHARS[0], $result[0]);
			$this->assertEquals(OLDSTYLE_CHARS[0], $result[1]);
			$this->assertEquals(OLDSTYLE_CHARS[0], $result[2]);
		}
	}


	public function test_the_least_significant_character_matches_the_index_of_the_oldstyle_chars (): void
	{
		for ($i = 0; $i < $this->len*1e4; ++$i)
		{
			$result = convert_autoincremented_index_to_oldstyle_handle($i);
			$this->assertEquals(OLDSTYLE_CHARS[$i % $this->len], $result[3]);
		}
	}


	public function test_the_second_least_significant_character_matches_the_index_of_the_oldstyle_chars_divided_by_the_number_of_chars (): void
	{
		for ($i = 0; $i < $this->len*1e4; $i += $this->len)
		{
			$result = convert_autoincremented_index_to_oldstyle_handle($i);
			$this->assertEquals(OLDSTYLE_CHARS[($i / $this->len) % $this->len], $result[2]);
		}
	}

	public function test_the_second_most_significant_character_matches_the_index_of_the_oldstyle_chars_divided_by_the_square_of_the_number_of_chars (): void
	{
		for ($i = 0; $i < $this->len*1e4; $i += $this->len**2)
		{
			$result = convert_autoincremented_index_to_oldstyle_handle($i);
			$this->assertEquals(OLDSTYLE_CHARS[($i / ($this->len**2)) % $this->len], $result[1]);
		}
	}

	public function test_the_most_significant_character_matches_the_index_of_the_oldstyle_chars_divided_by_the_cube_of_the_number_of_chars (): void
	{
		for ($i = 0; $i < $this->len*1e4; $i += $this->len**3)
		{
			$result = convert_autoincremented_index_to_oldstyle_handle($i);
			$this->assertEquals(OLDSTYLE_CHARS[($i / ($this->len**3)) % $this->len], $result[0]);
		}
	}
}


?>
