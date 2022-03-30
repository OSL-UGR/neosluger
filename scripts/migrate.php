<?php declare(strict_types=1); namespace NslScripts;


require_once(__DIR__."/../settings/settings.php");
require_once(__DIR__."/../vendor/autoload.php");


const OLDSTYLE_CHARS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";


function mongodb_collection (string $collection): \MongoDB\Collection
{
	static $database = null;

	if (is_null($database))
		$database = (new \MongoDB\Client("mongodb://localhost:27017"))->neosluger;

	return $database->selectCollection($collection);
}


function convert_autoincremented_index_to_oldstyle_handle (int $index): string
{
	$charlen = strlen(OLDSTYLE_CHARS);

	// Convert index from base 10 to base $charlen with four numbers
	$num1 = $index % $charlen;
	$num2 = ((int) ($index / $charlen)) % $charlen;
	$num3 = ((int) ($index / pow($charlen, 2))) % $charlen;
	$num4 = ((int) ($index / pow($charlen, 3))) % $charlen;

	return (OLDSTYLE_CHARS[$num4] . OLDSTYLE_CHARS[$num3] . OLDSTYLE_CHARS[$num2] . OLDSTYLE_CHARS[$num1]);
}


function get_log_table_from_link_row (\mysqli $mysqli, array $link_row): array
{
	return $mysqli->query("SELECT * FROM log WHERE idpag = '" . $link_row["id"] . "'")->fetch_all(MYSQLI_ASSOC);
}


function insert_log_in_mongodb (array $link_row, array $log_table): void
{
	$creation_datetime = new \DateTime($link_row["creada"]);

	$log_object = [
		"handle"   => $link_row["id"],
		"accesses" => array($creation_datetime->format("Y-m-d H:i:s.u")),
	];

	foreach ($log_table as $log_row)
	{
		$access_datetime = new \DateTime($log_row["fecha"]);
		array_push($log_object["accesses"], $access_datetime->format("Y-m-d H:i:s.u"));
	}

	mongodb_collection(\NslSettings\LOGS_COLLECTION)->insertOne($log_object);
}


function insert_url_in_mongodb (array $link_row): void
{
	mongodb_collection(\NslSettings\URLS_COLLECTION)->insertOne([
		"handle"      => $link_row["id"],
		"destination" => $link_row["url"],
	]);
}


function migrate_links (\mysqli $mysqli, \mysqli_result $links_table, bool $convert_indices = true): void
{
	foreach ($links_table as $link_row)
	{
		if ($convert_indices)
			$link_row["id"] = convert_autoincremented_index_to_oldstyle_handle((int) $link_row["id"]);

		$log_table = get_log_table_from_link_row($mysqli, $link_row);

		echo "==> Inserting URL: " . $link_row["id"] . "\n";
		insert_url_in_mongodb($link_row);

		echo "  -> Creation datetime: " . $link_row["creada"] . "\n";
		echo "  -> Total number of accesses: " . count($log_table) . "\n";
		insert_log_in_mongodb($link_row, $log_table);
	}
}

function reset_mongodb_database (): bool
{
	$user_consents_destruction = false;

	echo "\nWARNING! YOU ARE ABOUT TO RUN DESTRUCTIVE OPERATIONS!\n";
	echo "THIS WILL ERASE ALL RECORDS IN YOUR MONGODB DATABASE!\n\n";

	$input = readline("Type YES if you are sure you want to ERASE EVERYTHING: ");
	$user_consents_destruction = ($input == "YES");

	if ($user_consents_destruction)
	{
		$random_string = bin2hex(random_bytes(16));

		echo "\nAre you absolutely sure? You will LOSE EVERYTHING FOREVER (a long time!).\n";
		echo "If you're sure, type the random string at the end of the message to continue.\n";
		echo "We waive all responsibility on any lost records on your database.\n";
		echo "Keep in mind that this is a decision you're taking as a grown up adult\n";
		echo "Type this string to continue: " . $random_string . "\n";

		$input = readline("> ");
		$user_consents_destruction = ($input == $random_string);
	}

	if ($user_consents_destruction)
	{
		echo "Dropping collections...\n";
		mongodb_collection(\NslSettings\URLS_COLLECTION)->drop();
		mongodb_collection(\NslSettings\LOGS_COLLECTION)->drop();
	}
	else
	{
		echo "No destructive operations were performed.\n";
	}

	return $user_consents_destruction;
}


function connect_to_mysql_db (): \mysqli
{
	$old_tty = shell_exec("stty -g");
	$user    = get_current_user();
	$db      = "sluger";

	echo "Insert you credentials...\n";

	$input = readline("Username (default=".$user."): ");
	if (!empty($input))
		$user = $input;

	shell_exec("stty -echo");
	$pass = readline("Password: ");
	shell_exec("stty ".$old_tty);

	$input = readline("\nDatabase (default=".$db."): ");
	if (!empty($input))
		$db = $input;

	$mysqli = null;

	try
	{
		$mysqli = new \mysqli("localhost", $user, $pass, $db);
	}
	catch (\Error $e)
	{
		echo "\n\n";
		echo ">>>>> Can't connect to MySQL!!!\n";
		echo ">>>>> Did you uncomment \"extension=mysqli\" in \"/etc/php/php.ini\"?\n";
		echo ">>>>> Is the MySQL database on?\n\n\n";
		throw $e;
	}

	if ($mysqli->connect_errno)
		throw new \ErrorException("Could not connect to ".$db."!");

	echo "Connection successful!\n";
	return $mysqli;
}


function migrate_from_mysql (): void
{
	$mysqli = connect_to_mysql_db();

	if (reset_mongodb_database())
	{
		migrate_links($mysqli, $mysqli->query("SELECT * FROM direcciones"));
		migrate_links($mysqli, $mysqli->query("SELECT * FROM elegidas"), false);
		echo "All records were successfully migrated!\n";
	}
}


function generate_indices ()
{
	echo "Generating indices...\n";

	mongodb_collection(\NslSettings\URLS_COLLECTION)->createIndex(['handle' => 1]);
	mongodb_collection(\NslSettings\LOGS_COLLECTION)->createIndex(['handle' => 1]);

	echo "Indices were successfully generated!\n";
}


function main ()
{
	echo
		"Select an option:\n" .
		"[1] Generate indices for MongoDB\n" .
		"[2] Migrate database from Sluger's Mysql database\n" .
		"[0] Exit program\n";
	$option = readline("> ");

	if ($option == "1")
		generate_indices();
	else if ($option == "2")
		migrate_from_mysql();

	echo "Bye!\n";
}


main();


?>
