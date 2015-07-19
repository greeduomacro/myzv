<?php
	include_once "myzv-inc/config.php";

        $m = new Memcached();
        $m->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
        $m->setSaslAuthData( getenv('MEMCACHIER_USERNAME'), getenv('MEMCACHIER_PASSWORD') );
        $parts = explode(":", getenv('MEMCACHIER_SERVERS'));
        $m->addServer($parts[0], $parts[1]);

        $status = $m->get('faction-town-status');

        if (!$m->getResultCode()) {
		echo $status;
		return;
        }

ob_start();
?>
<div style="width: 100px; float: left; left: 0; right: 0;">Owner</div>
<div style="width: 100px; float: left; left: 0; right: 0;">Sigil</div>
<div style="clear: right;">Town</div>
<?
$tq = $db->query( "SELECT town_owner, town_name, town_silver, town_prices, sheriff.char_id as sheriff_id, sheriff.char_name as sheriff_name, finance.char_id as finance_id, finance.char_name as finance_name, sigil_controller FROM myrunuo_faction_towns INNER JOIN myrunuo_faction_sigils on myrunuo_faction_towns.town_name = myrunuo_faction_sigils.sigil_town LEFT JOIN myrunuo_characters AS sheriff ON town_sherif = sheriff.char_id LEFT JOIN myrunuo_characters AS finance ON town_finance = finance.char_id ORDER BY town_name ASC" );

if (!tq) {
	ob_end_flush();
	return;
}

while ( $town=$db->fetchrow($tq) ) { ?>
<div style="clear: both;">
<div style="width: 100px; float: left; left: 0; right: 0;"><? echo GetFactionIcon( $town['town_owner'] ); ?></div>
<div style="width: 100px; float: left; left: 0; right: 0;"><? echo GetFactionIcon( $town['sigil_controller'] ); ?></div>
<div style="float: left;">
<div><? echo $town['town_name']; ?></div>
<div style="font-size: 75%;">
<?
if ( $town['sheriff_id'] > 0 ) { ?>
Sheriffed by <em><? echo GetPlayerAnchor( $town['sheriff_id'], $town['sheriff_name'] ); ?></em>
<? }
if ( $town['finance_id'] > 0 ) {
	if ( $town['sheriff_id'] > 0 ) {
		echo ", financed by ";
	} else {
		echo "Financed by ";
	} ?><em><? echo GetPlayerAnchor( $town['finance_id'], $town['finance_name'] ); ?></em><? } ?>
</div>
	<div style="font-size: 75%;"><em><? echo $town['town_prices']; ?></em> taxes, <em><? echo $town['town_silver']; ?></em> silver</div>
</div>
</div>
<? }
$m->set('faction-town-status', ob_get_contents(), 3600);
ob_end_flush();
?>
