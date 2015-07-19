<?php
	include_once "myzv-inc/config.php";

	$MAX_RESULT = 50;

	$term = $_REQ['term'];

	if ( !empty( $term ) ) {
		$term = str_replace( '%', ' ', $term );
	}
?>
<table>
<caption>Search results for "<? echo $term; ?>"</caption>
	<thead>
		<tr>
			<th>Player</th>
			<th align="center">Guild</th>
		</tr>
	</thead>
	<tbody><?
		if ( strlen( $term ) > 2 )
		{
			$db->query( "SELECT * FROM ( SELECT char_name, char_id, guild_id, guild_name FROM myrunuo_characters LEFT JOIN myrunuo_guilds on char_guild = guild_id WHERE char_name = \"$term\" UNION SELECT char_name, char_id, guild_id, guild_name FROM myrunuo_characters LEFT JOIN myrunuo_guilds on char_guild = guild_id WHERE char_name LIKE \"%$term%\" ) DATA LIMIT $MAX_RESULT" );
			$c = 0;
			while ( $guild=$db->fetchrow() ) {
				$c++; ?>
		<tr>
			<td><? echo GetPlayerAnchor( $guild['char_id'], $guild['char_name'] ); ?></td>
			<td align="center"><? echo GetGuildAnchor( $guild['guild_id'], $guild['guild_name'] ); ?></td>
		</tr><?
			}
		}
		if ( $c == 0 ) { ?>
		<tr>
			<td colspan="2">No matches found.</td>
		</tr><?
		} else if ( $c == $MAX_RESULT ) { ?>
		<tr>
			<td colspan="2"><em>Only the first <? echo $MAX_RESULT; ?> results have been displayed.  Try narrowing your search.</em></td>
		</tr>
	</tbody><?}?>
</table>
