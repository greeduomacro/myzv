<?php
        include_once "myzv-inc/config.php";

        $m = new Memcached();
        $m->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
        $m->setSaslAuthData( getenv('MEMCACHIER_USERNAME'), getenv('MEMCACHIER_PASSWORD') );
        $parts = explode(":", getenv('MEMCACHIER_SERVERS'));
        $m->addServer($parts[0], $parts[1]);

        $rankings = $m->get('faction-guild-rankings');

        if (!$m->getResultCode()) {
                echo $rankings;
                return;
        }

ob_start();
?>
<?
$gq = $db->query( "SELECT SUM(char_killpts) as pts, count(*) as player_count, char_faction, guild_name, guild_id, guild_abbreviation FROM myrunuo_characters, myrunuo_guilds WHERE char_guild=guild_id AND char_faction > 0 GROUP BY(char_guild) ORDER BY pts DESC LIMIT 10" );

if (!$gq) {
	ob_end_flush();
	return;
}

while ( $rank=$db->fetchrow($gq) ) {
	print '<div id="guild' . $rank['guild_id'] . '">';
?>
<div><? echo GetFactionIcon( $rank['char_faction'] ); ?>
<? echo GetGuildAnchor( $rank['guild_id'], $rank['guild_name'] ); ?></div>
<div style="font-size: 75%;"><em><? echo $rank['pts']; ?></em> points, <em><? echo $rank['player_count']; ?></em> players</div>
</div>
<? }
$m->set('faction-guild-rankings', ob_get_contents(), 3600);
ob_end_flush();
?>
