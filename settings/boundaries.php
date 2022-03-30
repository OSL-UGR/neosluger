<?php declare(strict_types=1); namespace NeoslugerSettings;


require_once(__DIR__."/../core/qr-interactor.php");
require_once(__DIR__."/../core/url-interactor.php");
require_once(__DIR__."/../database/mongodb-connector.php");
require_once(__DIR__."/../settings/settings.php");


function qr_boundary (): \Neosluger\QRRequestBoundary
{
	static $interactor = null;

	if (is_null($interactor))
		$interactor = new \Neosluger\QRInteractor(\NeoslugerSettings\CACHE_DIRECTORY);

	return $interactor;
}


function url_boundary (): \Neosluger\URLRequestBoundary
{
	static $interactor = null;

	if (is_null($interactor))
		$interactor = new \Neosluger\URLInteractor(new \NeoslugerDB\MongoDBConnector());

	return $interactor;
}


?>
