<?php
	$TOP_COUNT = 10;
	include_once "myzv-inc/config.php";
?>
<div id="guild-size-rankings" style="display: table;">
	<div style="display: table-header-group">
		<div style="display: table-cell; text-align: center; width: auto; padding-right: 10px;">Rank</div>
		<div style="display: table-cell; text-align: left; width: auto; padding-right: 10px;">Guild</div>
		<div style="display: table-cell; width: auto; padding-right: 10px;"></div>
		<div style="display: table-cell; text-align: center; width: auto;">Members</div>
	</div><?
	$charq = $db->query( "SELECT guild_id, guild_abbreviation, guild_name, guild_members FROM myrunuo_guilds ORDER BY guild_members DESC LIMIT $TOP_COUNT" );
	$i = 0;
	while ( ($char = $db->fetchrow( $charq )) && $i++ <= $TOP_COUNT )
	{ ?>
	<div style="display: table-row;">
		<div style="display: table-cell; text-align: center; width: auto;"><? echo GetRank( $i ); ?></div>
		<div style="display: table-cell; text-align: left; width: auto;"><? echo GetGuildAnchor( $char['guild_id'], $char['guild_name'] ); ?></div>
		<div style="display: table-cell; text-align: center; width: auto;"><? echo GetTitledAnchor( GetGuildUrl( $char['guild_id'] ), $char['guild_name'], $char['guild_abbreviation'] ); ?></div>
		<div style="display: table-cell; text-align: center; width: auto;"><? echo $char['guild_members']; ?></div>
	</div><?
	} ?>
</div>
