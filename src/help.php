<?php

ini_set('display_errors', 1);
require_once('vendor/autoload.php');

function main ()
{
	$loader = new \Twig\Loader\FilesystemLoader('templates');
	$twig   = new \Twig\Environment($loader);

	echo $twig->render('help.html', [
		'help_tab' => "active-tab",
	]);
}

main();

?>