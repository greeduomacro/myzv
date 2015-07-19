<?php
        include_once "myzv-inc/config.php";

        $m = new Memcached();
        $m->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
        $m->setSaslAuthData( getenv('MEMCACHIER_USERNAME'), getenv('MEMCACHIER_PASSWORD') );
        $parts = explode(":", getenv('MEMCACHIER_SERVERS'));
        $m->addServer($parts[0], $parts[1]);

        $rankings = $m->get('faction-rankings');

        if (!$m->getResultCode()) {
                echo $rankings;
                return;
        }

ob_start();
?>
<?
$fr = $db->query( "SELECT fac_id, fac_killpoints, fac_members FROM myrunuo_factions ORDER BY fac_killpoints DESC" );

if (!$fr) {
	ob_end_flush();
	return;
}

while ( $fac=$db->fetchrow() ) {
	print '<div id="faction' . $fac['fac_id'] . '">';
?>
<div><? echo GetFactionIcon( $fac['fac_id'] ); ?>
<? echo GetFactionName( $fac['fac_id'] ); ?></div>
<div style="font-size: 75%;"><em><? echo $fac['fac_killpoints']; ?></em> points, <em><? echo $fac['fac_members']; ?></em> players</div>
</div>
<? }
$m->set('faction-rankings', ob_get_contents(), 3600);
ob_end_flush();
?>
