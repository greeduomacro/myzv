<?php
	$TOP_COUNT = 5;
	include_once "myzv-inc/config.php";

	renderPlayerTable( $db, "winnings > 0", "winnings DESC", $TOP_COUNT );
?>
