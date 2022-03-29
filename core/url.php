<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/../settings/settings.php");


/** @class URL
  * @brief Representation of a shortened URL.
  *
  * This class is a simple data structure that could be created with an
  * associative array. However, it is defined as an object with a constructor
  * to prevent future maintainers from tampering with its members.
  */

final class URL
{
	/** Date and time when the URL was registered into the system. */
	private \DateTime $creation_datetime;

	/** Website the user will be redirected to when accessing the URL. */
	private string $destination;

	/** String of characters that follow Neosluger's address to form the short URL. **/
	private string $handle;


	/** @fn __construct (string $destination, \DateTime $creation_datetime, string $handle)
	  * @brief Constructs the URL object with immutable members.
	  *
	  * @param $destination The URL's destination address.
	  * @param $creation_datetime The URL's creation datetime.
	  * @param $handle The URL's handle.
	  */

	public function __construct (string $destination, \DateTime $creation_datetime, string $handle)
	{
		$this->creation_datetime = $creation_datetime;
		$this->destination = $destination;
		$this->handle = $handle;
	}


	/** @fn creation_datetime (): \Datetime
	  * @brief Consultor for the URL's creation datetime.
	  *
	  * @return \DateTime The private creation datetime member.
	  */

	public function creation_datetime (): \DateTime
	{
		return $this->creation_datetime;
	}


	/** @fn destination (): string
	  * @brief Consultor for the URL's destination address.
	  *
	  * @return string The private destination member.
	  */

	public function destination (): string
	{
		return $this->destination;
	}


	/** @fn full_handle (): string
	  * @brief Composes the handle with Neosluger's site address.
	  */

	public function full_handle (): string
	{
		return \NeoslugerSettings\SITE_ADDRESS . $this->handle;
	}


	/** @fn handle (): string
	  * @brief Consultor for the URL's handle.
	  *
	  * @return string The private handle member.
	  */

	public function handle (): string
	{
		return $this->handle;
	}
}


?>
