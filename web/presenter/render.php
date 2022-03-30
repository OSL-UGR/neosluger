<?php declare(strict_types=1); namespace NeoslugerWeb;


require_once($_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php");


function render (string $page, array $options = []): void
{
	$loader = new \Twig\Loader\FilesystemLoader(__DIR__."/../templates");
	$twig   = new \Twig\Environment($loader);

	echo $twig->render(
		str_ends_with($page, ".html") ? $page : $page.".html",
		$options
	);
}


?>
