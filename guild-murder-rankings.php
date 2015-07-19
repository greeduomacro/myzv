<?php
	$TOP_COUNT = 10;
	include_once "myzv-inc/config.php";
?>
<div id="guild-murder-rankings" style="display: table;">
	<div style="display: table-header-group;">
		<div style="display: table-cell; text-align: center; width: auto; padding-right: 10px;">Rank</div>
		<div style="display: table-cell; text-align: left; width: auto; padding-right: 10px;">Guild</div>
		<div style="display: table-cell; width: auto; padding-right: 10px;"></div>
		<div style="display: table-cell; text-align: center; width: auto;">Kills</div>
	</div><?
	$charq = $db->query( "SELECT SUM(char_counts) as kills, guild_id, guild_abbreviation, guild_name FROM myrunuo_characters INNER JOIN myrunuo_guilds on char_guild = guild_id GROUP BY guild_id, guild_abbreviation, guild_name ORDER BY kills DESC LIMIT $TOP_COUNT" );
	$i = 0;
	while ( ($char = $db->fetchrow( $charq )) && $i++ <= $TOP_COUNT ) { ?>
	<div style="display: table-row;">
		<div style="display: table-cell; text-align: center; width: auto;"><? echo GetRank( $i ); ?></div>
		<div style="display: table-cell; text-align: left; width: auto;"><? echo GetGuildAnchor( $char['guild_id'], $char['guild_name'] ); ?></div>
		<div style="display: table-cell; text-align: center; width: auto;"><? echo GetTitledAnchor( GetGuildUrl( $char['guild_id'] ), $char['guild_name'], $char['guild_abbreviation'] ); ?></div>
		<div style="display: table-cell; text-align: center; width: auto;"><? echo $char['kills']; ?></div>
	</div>
	<? } ?>
</div>
