<?php declare(strict_types=1); namespace Neosluger;


require_once(__DIR__."/vendor/autoload.php");
use MongoDB\Client as Mongo;


/*
 * Edit these constants to modify Neosluger's behaviours in your server.
 * You MUST at least change SITE_ADDRESS to your server address.
 */

const DB_ADDRESS      = "mongodb://localhost:27017"; // The database where data is stored. MongoDB in this case.
const HANDLE_LENGTH   = 8; // The length of the handle assigned to an URL if none is passed
const HASH_LENGTH     = 8; // The length of the hash QR codes are created with
const MAX_HANDLE_LEN  = 50; // The maximum length a user-defined handle can be for an URL
const MIN_HANDLE_LEN  = 5; // The minimum length a user-defined handle can be for an URL, must be lower than HANDLE_LENGTH
const SITE_ADDRESS    = "http://150.214.22.199/"; // The address Neosluger is hosted on


/*
 * The cache directory is defined as a function because PHP doesn't allow
 * concatenation in constants.
 */

function cache_directory (): string
{
	return (array_key_exists("DOCUMENT_ROOT", $_SERVER) ? $_SERVER["DOCUMENT_ROOT"]."/cache/" : __DIR__."/../cache/");
}


/*
 * IP addresses from which users are allowed to access certain parts of the service.
 */

const ALLOWED_IPS = [
	"192.168.1.1",
	"127.0.0.1",
	"150.214.*.*",
	"172.*.*.*"
];


function LOG_COLLECTION ()
{
	$mongo = new Mongo("mongodb://localhost:27017");
	return $mongo->neosluger->access_logs;
}


function URL_COLLECTION ()
{
	$mongo = new Mongo("mongodb://localhost:27017");
	return $mongo->neosluger->urls;
}


?>
