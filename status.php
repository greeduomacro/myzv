<?php
	include_once "myzv-inc/config.php";

	$db->query( "SELECT COUNT(*) as count FROM myrunuo_characters" );
	$char_count = $db->fetchfield( 'count' );

	$db->query( "SELECT COUNT(*) as count FROM myrunuo_guilds" );
	$guild_count = $db->fetchfield( 'count' );
?>
Currently tracking <? echo $char_count; ?> characters and <? echo $guild_count; ?> guilds.
