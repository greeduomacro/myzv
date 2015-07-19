<?php
	$TOP_COUNT = 5;
	include_once "myzv-inc/config.php";

	renderHandTable( $db, "SmallBlind < 25000 AND Folded = 0 AND PokerPlayerId = ( SELECT A.PokerPlayerId FROM PokerPlayers A WHERE A.PokerGameId = PokerGames.PokerGameId AND A.Credit > A.Debit ORDER BY (A.Credit - A.Debit) ASC LIMIT 1)", "PokerGames.PokerGameId DESC", $TOP_COUNT, true );
?>
