<?php declare(strict_types=1); namespace NslLocale;


require_once(__DIR__."/../settings/settings.php");


function localize (array $strings): string
{
	$message = $strings["EN"];

	if (array_key_exists(\NslSettings\LANGUAGE, $strings))
		$message = $strings[\NslSettings\LANGUAGE];

	return $message;
}

?>
