<?php
	$TOP_COUNT = 5;

	include_once "myzv-inc/config.php";

	$playerId = intval( $_REQ['id'] );

	$db->query( "SELECT myrunuo_characters.*, guild_name, guild_abbreviation, (SELECT COUNT(*) FROM PokerPlayers WHERE Serial = $playerId) AS HandCount, (SELECT SUM(CAST(Credit AS Signed) - CAST(Debit AS Signed)) FROM PokerPlayers WHERE Serial = $playerId) AS earnings  FROM myrunuo_characters LEFT JOIN myrunuo_guilds ON guild_id = char_guild WHERE char_id = $playerId" );

	$char = $db->fetchrow();

	if ( !$char ) {
		$SPIDER = "noindex,follow";
	}
?>
<? if ( !$char ) { ?>
	<em>Player not found.</em>
<? } else { ?>
<div style="float: left; margin-right: 50px;">
<fieldset><legend><h2>Recent Hands</h2></legend>
<? renderHandTable( $db, "Serial = $playerId", "PokerGameId DESC", "20" ); ?>
</fieldset></div>
<div>
	<!-- general information -->
	<fieldset>
		<legend><h2>General Information</h2></legend>
		<table cellpadding="3" cellspacing="1" width="100%">
			<tr>
				<td><label for="name-value">Name</label></td>
				<td id="name-value" align="right"><? echo GetPlayerAnchor( $playerId, $char['char_name'] ); ?></td>
			</tr>
	<? if ( $char['char_guild'] > 0 ) { ?>
			<tr>
				<td><label for="guild-value">Guild</label></td>
				<td id="guild-value" align="right"><? echo GetGuildAnchor( $char['char_guild'], $char['guild_name'] ) ?></td>
			</tr>
	<? } ?>
			<tr>
				<td><label for="hands-played-value">Hands Played</label></td>
				<td id="hands-played-value" align="right"><? echo number_format( $char['HandCount'] ); ?></td>
			</tr>
			<tr>
				<td><label for="earnings-value">Earnings</label></td>
				<td id="earnings-value" align="right"><? echo number_format( $char['earnings'] ) . "gp"; ?></td>
			</tr>
			<tr>
			<td colspan="2" align="right">
					<em><? echo GetPlayerAnchor( $playerId, "&hellip; more information" ); ?></em>
			</td>
		</tr>
		</table>
	</fieldset>
	<!-- /general information -->
</div>
<div>
<fieldset><legend><h2>Best Hands</h2></legend>
<? renderHandTable( $db, "Serial = $playerId AND Credit > Debit", "winnings DESC", "8" ); ?>
</fieldset></div>
<div>
<fieldset><legend><h2>Worst Hands</h2></legend>
<? renderHandTable( $db, "Serial = $playerId AND Credit < Debit", "winnings ASC", "8" ); ?>
</fieldset>
</div>
<? } ?>
