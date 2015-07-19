<?php
$_REQ = array();

reset($_POST);
while ( list($key,$data) = each($_POST) )
	$_REQ[$key] = addslashes($data);

reset($_GET);
while ( list($key,$data) = each($_GET) )
	$_REQ[$key] = addslashes($data);

header("Access-Control-Allow-Origin: *");
header("Vary: Accept-Encoding");
?>
