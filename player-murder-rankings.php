<?php
	$TOP_COUNT = 10;
	include_once "myzv-inc/config.php";
?>
<div id="player-murder-rankings" style="display: table;">
	<div style="display: table-header-group;">
		<div style="display: table-cell; text-align: center; width: auto; padding-right: 10px;">Rank</div>
		<div style="display: table-cell; text-align: center; width: auto; padding-right: 10px;">Guild</div>
		<div style="display: table-cell; text-align: left; width: auto; padding-right: 10px;">Player</div>
		<div style="display: table-cell; text-align: center; width: auto;">Kills</div>
	</div>
	<?
	$charq = $db->query( "SELECT char_id, char_name, char_counts, guild_id, guild_abbreviation, guild_name FROM myrunuo_characters LEFT JOIN myrunuo_guilds on char_guild = guild_id WHERE char_counts > 0 ORDER BY char_counts DESC LIMIT $TOP_COUNT" );
	$i = 0;
	while ( ($char = $db->fetchrow( $charq )) && $i++ <= $TOP_COUNT ) { ?>
	<div style="display: table-row;">
		<div style="display: table-cell; text-align: center; width: auto;"><? echo GetRank( $i ); ?></div>
		<div style="display: table-cell; text-align: center; width: auto;"><? echo GetTitledAnchor( GetGuildUrl( $char['guild_id'] ), $char['guild_name'], $char['guild_abbreviation'] ); ?></div>
		<div style="display: table-cell; text-align: left; width: auto;"><? echo GetPlayerAnchor( $char['char_id'], $char['char_name'] ); ?></div>
		<div style="display: table-cell; text-align: center; width: auto;"><? echo $char['char_counts']; ?></div>
	</div><? } ?>
</div>
