<?php
	$TOP_COUNT = 5;

	include_once "myzv-inc/config.php";

	renderHandTable( $db, "FinalPlayers >= 2 AND Credit > Debit", "(Credit-Debit) DESC", $TOP_COUNT, true ); 
?>
