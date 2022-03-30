<?php declare(strict_types=1); namespace NeoslugerLocale;


require_once(__DIR__."/../settings/settings.php");


function localize (array $strings): string
{
	$message = $strings["EN"];

	if (array_key_exists(\NeoslugerSettings\LANGUAGE, $strings))
		$message = $strings[\NeoslugerSettings\LANGUAGE];

	return $message;
}

?>
