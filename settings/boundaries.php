<?php declare(strict_types=1); namespace NeoslugerWeb;


require_once(__DIR__."/qr-interactor.php");
require_once(__DIR__."/url-interactor.php");
require_once(__DIR__."/database/mongodb-connector.php");
require_once(__DIR__."/../settings.php");


function qr_boundary (): \Neosluger\QRRequestBoundary
{
	return new \Neosluger\QRInteractor(\Neosluger\cache_directory());
}


function url_boundary (): \Neosluger\URLRequestBoundary
{
	return new \Neosluger\URLInteractor(new \NeoslugerDB\MongoDBConnector(\Neosluger\DB_ADDRESS));
}


?>
