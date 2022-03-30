<?php declare(strict_types=1); namespace NslSettings;


/*
 * Edit these constants to modify Nsl's behaviours in your server.
 * You MUST at least change SITE_ADDRESS to your server address.
 */


const DEBUG = 1; // Set it to 1 while testing and to 0 in production.

const CACHE_DIRECTORY =  __DIR__."/../cache/"; // Where cache files are saved, relative to this file.
const HANDLE_LENGTH   = 8; // The length of the handle assigned to an URL if none is passed
const HASH_LENGTH     = 8; // The length of the hash QR codes are created with
const LANGUAGE        = "EN"; // Language for the strings the user can read. Defaults to "EN".
const MAX_HANDLE_LEN  = 50; // The maximum length a user-defined handle can be for an URL
const MIN_HANDLE_LEN  = 5; // The minimum length a user-defined handle can be for an URL, must be lower than HANDLE_LENGTH
const SITE_ADDRESS    = "localhost/"; // The address Nsl is hosted on


// IP addresses from which users are allowed to access certain parts of the service.
const ALLOWED_IPS = [
	"192.168.1.1",
	"127.0.0.1",
	"150.214.*.*",
	"172.*.*.*"
];


?>
