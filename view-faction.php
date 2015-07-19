<?php
	include_once "myzv-inc/config.php";

	$__factionId = intval( $_REQ['id'] );

	$db->query( "SELECT * FROM myrunuo_factions WHERE fac_id = $__factionId" );

	if ( !$fac=$db->fetchrow() )
		die( "Faction not found" );

	$name = GetFactionNameNoLink( $fac['fac_id'] );
?>

<div style="width: 100%;">
	<!-- faction details -->
	<div style="clear: both;">
		<div style="clear: both;">
			<div style="float: left;"><? echo GetFactionIcon( $fac['fac_id'] ); ?></div>
			<div><h2><? echo $name; ?></h2></div>
		</div>
		<div style="clear: both;">
			<div style="float: left; width: 150px;">Commander</div>
			<div style="float: left; text-align: right;">
<?
$db->query( "SELECT char_name FROM myrunuo_characters WHERE char_id=".$fac['fac_cmdr']);
if ( $cmdr=$db->fetchrow() ) {
	echo GetPlayerAnchor( $fac['fac_cmdr'], $cmdr['char_name'] );
}
?>
			</div>
		<div style="clear: both;">
			<div style="float: left; width: 150px;">Players</div>
			<div style="float: left; text-align: right;"><? echo $fac['fac_members']; ?></div>
		</div>
		<div style="clear: both;">
			<div style="float: left; width: 150px;">Points</div>
			<div style="float: left; text-align: right;"><? echo $fac['fac_killpoints']; ?></div>
		</div>
		<div style="clear: both;">
			<div style="float: left; width: 150px;">Silver</div>
			<div style="float: left; text-align: right;"><? echo $fac['fac_silver']; ?></div>
		</div>
	</div>
	<!-- /faction details -->
	<br/>
	<!-- player rankings -->
	<div style="float: left;">
		<div>
			<h2>Player Rankings</h2>
		</div>
<?
$cq = $db->query( "SELECT char_killpts, char_guild, guild_name, char_faction, char_name, char_id FROM myrunuo_characters LEFT JOIN myrunuo_guilds ON myrunuo_guilds.guild_id = myrunuo_characters.char_guild WHERE char_faction=".$fac['fac_id']." AND char_killpts > 0 ORDER BY char_killpts DESC LIMIT 10" );
while ( $rank=$db->fetchrow( $cq ) ) { ?>
		<div style="clear: both; height: 3em;">
			<div style="float: left;">
				<b><? echo GetPlayerAnchor( $rank['char_id'], $rank['char_name'] ); ?></b>
			</div>
			<div style="text-align: right;">
				<em><? echo $rank['char_killpts']; ?></em> points
			</div>
			<div style="clear: both; text-align: left; position: relative; bottom: .75em;">
				<span style="font-size: 75%;">
				<? if ( $rank['char_guild'] > 0 ) { ?><em><? echo GetGuildAnchor( $rank['char_guild'], $rank['guild_name'] ); ?></em><? } ?>
				</span>
			</div>
		</div>
<? } ?>
	</div>
	<!-- /player rankings -->
	<!-- guild rankings -->
	<div style="float: left; margin-left: 50px;">
		<div>
			<h2>Guild Rankings</h2>
		</div>
<?
$db->query( "SELECT SUM(char_killpts) as pts, count(*) as player_count, char_faction, guild_name, guild_id, guild_abbreviation FROM myrunuo_characters, myrunuo_guilds WHERE char_guild=guild_id AND char_faction=".$fac['fac_id']." GROUP BY(char_guild) ORDER BY pts DESC LIMIT 10" );
while ( $rank=$db->fetchrow() ) { ?>
		<div style="clear: both;">
			<div>
				<b><? echo GetGuildAnchor( $rank['guild_id'], $rank['guild_name'] ); ?></b>
			</div>
			<div style="text-align: left; margin-top: -.75em;">
				<span style="font-size: 75%;"><em><? echo $rank['pts']; ?></em> points, <em><? echo $rank['player_count']; ?></em> players</span>
			</div>
		</div>
<? } ?>
	</div>
	<!-- /guild rankings -->
<p style="clear: both; text-align: right; margin-top: 50px; font-size: 80%;">Icons &copy; Electronics Arts, Inc.</p>
</div>
