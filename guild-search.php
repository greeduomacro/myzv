<?php
	include_once "myzv-inc/config.php";
	$resultCount = 50;
	$term = $_REQ['term'];
	if ( !empty( $term ) ) {
		$term = str_replace( '%', ' ', $term );
	}
?>
<table>
<caption>Search results for "<? echo $term; ?>"</caption>
  <thead>
    <tr>
      <th>Guild</th>
      <th align="center">Abbreviation</th>
    </tr>
  </thead>
  <tbody>
    <?
	if ( strlen( $term ) > 0 )
	{
		$db->query( "SELECT guild_name, guild_id, guild_abbreviation FROM myrunuo_guilds WHERE guild_name LIKE \"%$term%\" OR guild_abbreviation = \"$term\" LIMIT $resultCount" );
		$c = 0;
		while ( $guild=$db->fetchrow() ) {
			$c++; ?>
    <tr>
      <td>
        <? echo GetGuildAnchor( $guild['guild_id'], $guild['guild_name'] ); ?>
      </td>
      <td align="center">
        <? echo $guild['guild_abbreviation']; ?>
      </td>
    </tr>
    <?
		}
	}
		if ( $c == 0 ) { ?>
    <tr>
      <td colspan="2">No matches found.</td>
    </tr>
    <?
		} else if ( $c == $resultCount ) { ?>
    <tr>
      <td colspan="2">
        <em>
          Only the first <? echo $resultCount; ?> results have been displayed.  Try narrowing your search.
        </em>
      </td>
    </tr>
  </tbody><?
		}
	?>
</table>
