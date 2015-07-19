<?php
        include_once "myzv-inc/config.php";

        $m = new Memcached();
        $m->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
        $m->setSaslAuthData( getenv('MEMCACHIER_USERNAME'), getenv('MEMCACHIER_PASSWORD') );
        $parts = explode(":", getenv('MEMCACHIER_SERVERS'));
        $m->addServer($parts[0], $parts[1]);

        $rankings = $m->get('faction-player-rankings');

        if (!$m->getResultCode()) {
                echo $rankings;
                return;
        }

ob_start();
?>
<?
$cq = $db->query( "SELECT char_killpts, char_guild, guild_name, char_faction, char_name, char_id FROM myrunuo_characters LEFT JOIN myrunuo_guilds ON myrunuo_guilds.guild_id = myrunuo_characters.char_guild ORDER BY char_killpts DESC LIMIT 15" );

if (!$cq) {
	ob_end_flush();
	return;
}

while ( $rank=$db->fetchrow( $cq ) ) {
	print '<div id="player' . $rank['char_id'] . '">';
?>
<div><? echo GetFactionIcon( $rank['char_faction'] ); ?>
<? echo GetPlayerAnchor( $rank['char_id'], $rank['char_name'] ); ?></div>
<div style="font-size: 75%;"><em><? echo $rank['char_killpts']; ?></em> points<?
if ( $rank['char_guild'] > 0 ) { ?>, <em><? echo GetGuildAnchor( $rank['char_guild'], $rank['guild_name'] ); ?></em><? }
?></div>
</div>
<? }
$m->set('faction-player-rankings', ob_get_contents(), 3600);
ob_end_flush();
?>
