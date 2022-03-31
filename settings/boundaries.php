<?php declare(strict_types=1); namespace NslSettings;


require_once(__DIR__."/../core/qr-interactor.php");
require_once(__DIR__."/../core/url-interactor.php");
require_once(__DIR__."/../database/mongodb-connector.php");
require_once(__DIR__."/../settings/settings.php");


function qr_boundary (): \Nsl\QRRequestBoundary
{
	static $interactor = null;

	if (is_null($interactor))
		$interactor = new \Nsl\QRInteractor(\NslSettings\CACHE_DIRECTORY);

	return $interactor;
}


function url_boundary (): \Nsl\URLRequestBoundary
{
	static $interactor = null;

	if (is_null($interactor))
		$interactor = new \Nsl\URLInteractor(new \NslDB\MongoDBConnector());

	return $interactor;
}


?>
