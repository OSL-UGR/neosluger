<?php declare(strict_types=1);


require_once("../src/php/const.php");


function create_indexes ()
{
	Neosluger\URL_COLLECTION()->createIndex(['handle' => 1]);
	Neosluger\LOG_COLLECTION()->createIndex(['handle' => 1]);
}


function main ()
{
	create_indexes();
}

main();

?>
