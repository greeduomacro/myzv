<?php
	include_once "myzv-inc/config.php";

	$guildId = intval( $_REQ['id'] );

	$db->query( "SELECT myrunuo_guilds.*, gm.char_id as gm_id, gm.char_name as gm_name, gm.char_faction as gm_faction FROM myrunuo_guilds LEFT JOIN myrunuo_characters AS gm ON guild_master = gm.char_id WHERE guild_id = $guildId" );

	$guild = $db->fetchrow();

	if ( !$guild ) {
		$SPIDER = "noindex,follow";
	} else {
		$PAGE_TITLE = $guild['guild_name'] . " - Guild Details - My UOGamers";
	}
?>


<? if ( !$guild ) { ?>
<table cellspacing="2" cellpadding="2" width="100%">
	<tr>
		<td valign="top">
			<table cellspacing="2" cellpadding="2" width="100%">
				<tr>
					<td>
						<fieldset>
							<legend>General Information</legend>
							<em>Guild not found.</em>
						</fieldset>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<? } else { ?>
<table cellspacing="2" cellpadding="2" width="100%">
	<tr>
		<td valign="top">
			<table cellspacing="2" cellpadding="2" width="100%">
				<tr>
					<td>
						<!-- guild details -->
						<fieldset>
							<legend>General Information</legend>
							<table cellpadding="3" cellspacing="1" width="100%">
								<tr>
									<td><label for="name-value">Name</label></td>
									<td id="name-value" align="right"><? echo $guild['guild_name']; ?></td>
								</tr>
								<tr>
									<td><label for="abbreviation-value">Abbreviation</label></td>
									<td id="abbreviation-value" align="right"><? echo $guild['guild_abbreviation']; ?></td>
								</tr>
								<tr>
									<td><label for="guildmaster-value">Guildmaster</label></td>
									<td id="guildmaster-value" align="right"><? echo GetPlayerAnchor( $guild['gm_id'], $guild['gm_name'] ); ?></td>
								</tr>
								<? if ( $guild['gm_faction'] > 0 ) { ?>
								<tr>
									<td><label for="faction-value">Faction</label></td>
									<td id="faction-value" align="right"><? echo GetFactionName( $guild['gm_faction'] ); ?></td>
								</tr>
								<? } ?>
								<? if ( $guild['guild_type'] > 0 ) { ?>
								<tr>
									<td><label for="alignment-value">Alignment</label></td>
									<td id="alignment-value" align="right"><? echo $guild['guild_type'] == 1 ? "Chaos" : "Order"; ?></td>
								</tr>
								<? } ?>
								<? if ( $guild['guild_website'] != "" ) { ?>
								<tr>
									<td><label for="website-value">Website</label></td>
									<td id="website-value" align="right">
<?
$ws = $guild['guild_website'];
$pos = strpos($ws, 'http');

if ($pos === false || $pos != 0)
	echo '<a href="http://'.$ws.'">'.$ws.'</a>';
else
	echo '<a href="'.$ws.'">'.$ws.'</a>';
?>
									</td>
								</tr>
								<? } ?>
								<? if ( $guild['guild_charter'] != "" ) { ?>
								<tr>
									<td><label for="charter-value">Charter</label></td>
									<td id="charter-value" align="right"><? echo $guild['guild_charter']; ?></td>
								</tr>
								<? } ?>
							</table>
						</fieldset>
						<!-- /guild details -->
					</td>
				</tr>
				<tr><td><div class="separator"></div></td></tr>
				<tr>
					<td>
						<!-- members -->
						<fieldset>
							<legend>Members</legend>
							<table cellpadding="3" cellspacing="1" width="100%">
<?
$db->query( "SELECT char_id, char_name, char_guildtitle FROM myrunuo_characters WHERE char_guild = $guildId ORDER BY char_name ASC" );

$c = 0;
while ( $char=$db->fetchrow() ) {
	$c++;

	if ( (($c-1) % 2) == 0 ) { ?>
								<tr>
<? } ?>
									<td class="entry" align="center">
										<div class="subtext">[<? if ( $char['char_guildtitle'] ) { echo $char['char_guildtitle'].", "; } echo $guild['guild_abbreviation']; ?>]</div>
										<div class="player-name"><? echo GetPlayerAnchor( $char['char_id'], $char['char_name'] ); ?></div>
									</td>
<?	if ( (($c-1) % 2) == 1 ) { ?>
								</tr>
<?	}
}

if ( ($c % 2) == 1 ) { ?>
								</tr>
<? } ?>
							</table>
						</fieldset>
						<!-- /members -->
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table cellspacing="2" cellpadding="2" width="100%">
				<tr>
					<td>
						<!-- skills -->
						<fieldset>
							<legend>Skill Averages</legend>
							<table cellpadding="3" cellspacing="1" width="100%">
<?
$db->query( "SELECT SUM(skill_value) as sum, skill_id FROM myrunuo_characters INNER JOIN myrunuo_characters_skills ON myrunuo_characters.char_id = myrunuo_characters_skills.char_id WHERE char_guild = $guildId GROUP BY skill_id ORDER BY sum DESC, skill_id ASC" );

$c = 0;
while ( $sk=$db->fetchrow() ) { ?>
								<tr>
									<td class="entry full"><? echo $SkillNames[$sk['skill_id']]; ?></td>
									<td class="entry" align="right"><? echo sprintf( "%.02f", (($sk['sum'] / $guild['guild_members']) / 10.0) ); ?></td>
								</tr>
<? } ?>
							</table>
						</fieldset>
						<!-- /skills -->
					</td>
				</tr>
				<tr>
					<td>
						<!-- wars -->
						<fieldset>
							<legend>Wars</legend>
							<table cellpadding="3" cellspacing="1" width="100%">
<?
$warq = $db->query( "SELECT guild_id, guild_name, guild_abbreviation FROM myrunuo_guilds, myrunuo_guilds_wars WHERE (guild_id=guild_1 AND guild_2 = $guildId) OR (guild_id=guild_2 AND guild_1 = $guildId) ORDER BY guild_name ASC" );

$c = 0;
while ( $war=$db->fetchrow($warq) ) {
	$c++; ?>
								<tr>
									<td class="entry full"><? echo GetGuildAnchor( $war['guild_id'], $war['guild_name'] ); ?></td>
									<td class="entry" align="center"><? echo GetTitledAnchor( GetGuildUrl( $war['guild_id'] ), $war['guild_name'], $war['guild_abbreviation'] ); ?></td>
								</tr>
<? }

if ( $c == 0 ) { ?>
								<tr>
									<td class="entry" colspan="2"><em>This guild is not at war.</em></td>
								</tr>
<? } ?>
							</table>
						</fieldset>
						<!-- /wars -->
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<? } ?>
