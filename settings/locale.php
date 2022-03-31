<?php declare(strict_types=1); namespace NslSettings;


require_once(__DIR__."/settings.php");


function localise (array $msg): string
{
	return (array_key_exists(LANGUAGE, $msg) ? $msg[LANGUAGE] : $msg["EN"]);
}

?>
