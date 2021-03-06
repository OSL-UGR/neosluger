<?php declare(strict_types=1); namespace Nsl;


require_once(__DIR__."/strings.php");


final class Result
{
	private mixed $value = null;
	private ?string $error = null;
	private ?Result $next  = null;


	private function __construct (mixed $value, ?string $error)
	{
		$this->value = $value;
		$this->error = $error;
	}


	public static function from_error (string $error): Result
	{
		return new Result(null, $error);
	}


	public static function from_value (mixed $value): Result
	{
		return new Result($value, null);
	}


	public function errors (): ?array
	{
		$result = $this;
		$errors = [$result->error];

		while (!is_null($result->next))
		{
			$result = $result->next;
			array_push($errors, $result->error);
		}

		return $errors;
	}


	public function first_error (): ?string
	{
		return $this->error;
	}


	public function ok (): bool
	{
		return (!is_null($this->value));
	}


	public function push_back (string $error): void
	{
		$result = $this;

		if (empty($this->error))
		{
			$this->error = $error;
			$this->value = null;
		}
		else
		{
			while (!is_null($result->next))
				$result = $result->next;

			$result->next = Result::from_error($error);
		}
	}


	public function unwrap (): mixed
	{
		global $ERR_UNWRAP;

		if (!$this->ok())
		{
			$result = $this;
			$error = $ERR_UNWRAP()."\n - ".$result->error;

			while (!is_null($result->next))
			{
				$result = $result->next;
				$error .= "\n - ".$result->error;
			}

			throw new \LogicException($error);
		}

		return $this->value;
	}
}


?>
