<?php
	$TOP_COUNT = 5;
	include_once "myzv-inc/config.php";

	$db->query( "SELECT COUNT(*) AS GameCount FROM PokerGames" );

	$gameCount = $db->fetchfield( 'GameCount' );

	$db->query( "SELECT SUM(Credit) - SUM(Debit) AS Earnings FROM PokerPlayers WHERE Credit > Debit" );

	$finalPot = $db->fetchfield( 'Earnings' );

	function getdays( $day1, $day2 ) {
		return round((strtotime($day2)-strtotime($day1))/(24*60*60),0);
	}

	$days = getdays( "2013/09/15", date( "Y/m/d" ) );
	$growth = $gameCount / $days;

	$growth /= 50;
	$growth = (int)$growth;
	$growth *= 50;
?>
We currently have <em><? echo number_format( $gameCount ); ?> games</em> in our database, with <em><? echo number_format( $growth ); ?> played every day</em>.  To date, <? echo number_format( $finalPot ); ?>gp has changed hands.
