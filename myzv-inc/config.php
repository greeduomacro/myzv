<?
	list($usec, $sec) = explode(" ", microtime());
	$__starttime = $sec+$usec;

	include_once("mysql.php");

	$db = new Database( getenv('MYSQL_HOST'), getenv('MYSQL_USER'), getenv('MYSQL_PW'), getenv('MYSQL_DB'), false );

	include_once "request.php";

	include_once "defines.php";
?>
