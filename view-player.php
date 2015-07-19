<?php
	include_once "myzv-inc/config.php";

	$playerId = intval( $_REQ['id'] );

	$db->query( "SELECT myrunuo_characters.*, guild_name, guild_abbreviation, (SELECT COUNT(*) FROM PokerPlayers WHERE Serial = $playerId) AS PokerHandCount FROM myrunuo_characters LEFT JOIN myrunuo_guilds ON guild_id = char_guild WHERE char_id = $playerId" );

	$char = $db->fetchrow();

	if ( !$char ) {
		$SPIDER = "noindex,follow";
	} else {
		$pokerHandCount = $char['PokerHandCount'];
	}
?>


<? if ( !$char ) { ?>
	<em>Player not found.</em>
<? } else { ?>
<div style="float: left; height: 150px; margin-right: 30px;">
<!-- general information -->
<fieldset>
<legend><strong>General Information</strong></legend>
	<table>
	<tr>
		<td><label for="name-value">Name</label></td>
		<td id="name-value" align="right"><? echo $char['char_name']; ?></td>
	</tr>
	<? if ( $char['char_guild'] > 0 ) { ?>
	<tr>
		<td><label for="guild-value">Guild</label></td>
		<td id="guild-value" align="right"><? echo GetGuildAnchor( $char['char_guild'], $char['guild_name'] ) ?></td>
	</tr>
	<? } ?>
	<? if ( $char['char_faction'] > 0 ) { ?>
	<tr>
		<td><label for="faction-value">Faction</label></td>
		<td id="faction-value" align="right"><? echo GetFactionName( $char['char_faction'] ); ?></td>
	</tr>
	<tr>
		<td><label for="points-value">Points</label></td>
		<td id="points-value" align="right"><? echo $char['char_killpts']; ?></td>
	</tr>
<? } ?>
<? if ( $char['char_public'] > 0 && $char['char_counts'] >= 5 ) { ?>
	<tr>
		<td><label for="kills-value">Kills</label></td>
		<td id="kills-value" align="right"><? echo $char['char_counts']; ?></td>
	</tr>
<? } ?>
	</table>
</fieldset>
<!-- /general information -->
</div>

<div style="float: left; height: 150px; margin-right: 30px;">
<!-- character class -->
<? if ( $char['char_public'] > 0 ) { ?>
<fieldset>
<legend><strong>Attributes</strong></legend>
	<div style="display: table;">
	<div style="display: table-row;">
		<div style="display: table-cell;"><label for="strength-value">Strength</label></div>
		<div style="display: table-cell; text-align: right;" id="strength-value"><? echo $char['char_str']; ?></div>
	</div>
	<div style="display: table-row;">
		<div style="display: table-cell;"><label for="dexterity-value">Dexterity</label></div>
		<div style="display: table-cell; text-align: right;" id="dexterity-value"><? echo $char['char_dex']; ?></div>
	</div>
	<div style="display: table-row;">
		<div style="display: table-cell;"><label for="intelligence-value">Intelligence</label></div>
		<div style="display: table-cell; text-align: right;" id="intelligence-value"><? echo $char['char_int']; ?></div>
	</div>
	</div>
</fieldset>
</div>
<div style="float: right; margin-right: 30px;">
<fieldset>
<legend><strong>Skills</strong></legend>
	<div style="display: table;">
<?
$db->query( "SELECT skill_id, skill_value FROM myrunuo_characters_skills WHERE char_id = $playerId ORDER BY skill_value DESC, skill_id ASC" );
$c = 0;
while ( $sk = $db->fetchrow() ) {
	$c++; ?>
	<div style="display: table-row;">
		<div style="display: table-cell;"><label for="skill-value-<? echo $c; ?>"><? echo $SkillNames[$sk['skill_id']]; ?></label></div>
		<div style="display: table-cell; text-align: right;" id="skill-value-<? echo $c; ?>"><? echo sprintf( "%.01f", $sk['skill_value'] / 10 ); ?></div>
	</div>
<? } ?>
	</div>
</fieldset>
<? } else { ?>
<fieldset>
	<legend><strong>Character Class</strong></legend>
	<em><? echo $char['char_name']; ?> has chosen not to reveal this information.</em>
</fieldset>
<? } ?>
<!-- /character class -->
</div>
<? if ( $char['char_duelexp'] > 0 ) { ?>
<div style="float: left; margin-right: 30px;">
<?
	$db->query( "SELECT COUNT(*) as rank FROM myrunuo_characters WHERE char_duelexp >= ".$char['char_duelexp'] );
	$rank = $db->fetchfield( 'rank' ); ?>
			<div class="separator"></div>
			<!-- dueling statistics -->
			<fieldset>
				<legend><strong>Dueling Statistics</strong></legend>
				<table>
					<tr>
						<td><label for="rank-value">Rank</label></td>
						<td id="rank-value" align="right"><? echo GetRank( $rank ); ?></td>
					</tr>
					<tr>
						<td><label for="level-value">Level</label></td>
						<td id="level-value" align="right"><? echo GetDuelLevel( $char['char_duelexp'] ); ?></td>
					</tr>
					<tr>
						<td><label for="wins-value">Wins</label></td>
						<td id="wins-value" align="right"><? echo $char['char_duelwins']; ?></td>
					</tr>
					<tr>
						<td><label for="losses-value">Losses</label></td>
						<td id="losses-value" align="right"><? echo $char['char_duellosses']; ?></td>
					</tr>
				</table>
			</fieldset>
			<!-- /dueling statistics -->
</div>
<? } ?>
<div style="float: left; margin-right: 30px;">
<? if ( $pokerHandCount > 0 ) { ?>
	<div class="separator"></div>
	<!-- poker hands -->
	<fieldset>
		<legend><strong>Poker Hands</strong></legend>
		<div style="display: table">
<?
$db->query( "SELECT PokerGames.PokerGameId, PokerGames.InitialPlayers, CAST(Credit AS Signed) - CAST(Debit AS Signed) AS Winnings FROM PokerPlayers INNER JOIN PokerGames ON PokerGames.PokerGameId = PokerPlayers.PokerGameId WHERE Serial = $playerId ORDER BY PokerGames.PokerGameId DESC LIMIT 10" );
$c = 0;
while ( $pokerData = $db->fetchrow() ) {
	$c++; ?>
			<div style="display: table-row;">
				<div style="display: table-cell; text-align: right;"><? echo $c; ?>.</div>
				<div style="display: table-cell;"><? echo GetPokerAnchor( $pokerData['PokerGameId'], $pokerData['InitialPlayers'] . " players, " . number_format( $pokerData['Winnings'] ) . "gp" ); ?></div>
			</div>
<? } ?>
<? if ( $pokerHandCount > 10 ) { ?>
			<div style="display: table-row;">
				<div style="display: table-cell; text-align: right;">&hellip;</div>
				<div style="display: table-cell;"><a href="<? echo GetPokerSearchUrl( $playerId ); ?>" style="font-style: italic;">view more poker hands</a></div>
			</div>
<? } ?>
		</div>
	</fieldset>
	<!-- /poker hands -->
<? } ?>
</div>
<? } ?>
