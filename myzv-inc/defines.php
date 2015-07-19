<?php
	$SkillNames = array( "Alchemy", "Anatomy", "Animal Lore", "Item Identification", "Arms Lore", "Parrying", "Begging", "Blacksmithy", 
		"Bowcraft / Fletching", "Peacemaking", "Camping", "Carpentry", "Cartography", "Cooking", "Detecting Hidden", "Discordance", 
		"Evaluating Intelligence", "Healing", "Fishing", "Forensic Evaluation", "Herding", "Hiding", "Provocation", "Inscription", "Lockpicking", 
		"Magery", "Resisting Spells", "Tactics", "Snooping", "Musicianship", "Poisoning", "Archery", "Spirit Speak", 
		"Stealing", "Tailoring", "Animal Taming", "Taste Identification", "Tinkering", "Tracking", "Veterinary", "Swordsmanship", 
		"Mace Fighting", "Fencing", "Wrestling", "Lumberjacking", "Mining", "Meditation", "Stealth", "Trap Removal", 
		"Necromancy", "Focus", "Chivalry", "Bushido", "Ninjitsu", "Spellweaving" );

	$__short_levels = array( 1, 2, 3, 3, 4, 4, 5, 5, 5, 6, 6, 6, 7, 7, 7, 7, 8, 8, 8, 8, 9, 9, 9, 9, 9 );
	function GetDuelLevel( $exp )
	{
		if ( $exp >= 22500 )
		{
			return 50;
		}
		else if ( $exp >= 2500 )
		{
			return intval(10 + (($exp - 2500) / 500));
		}
		else
		{
			global $__short_levels;

			if ( $exp < 0 )
				$exp = 0;
			return $__short_levels[intval($exp/100)];
		}
	}

	function GetVideoSearchUrl( $playerId ) {
		if ( $playerId == 0 ) {
			return NULL;
		}

		return "http://videos.zenvera.com/search.php?playerId=$playerId";
	}

	function GetVideoUrl( $videoId ) {
		if ( $videoId == 0 ) {
			return NULL;
		}

		return "http://videos.zenvera.com/view.php?videoId=$videoId";
	}

	function GetPokerSearchUrl( $playerId ) {
		return "myzv-poker-player.html?id=$playerId";
	}

	function GetPokerUrl( $handId ) {
		return "myzv-poker-hand?id=$handId";
	}

	function GetPokerAnchor( $handId, $handName ) {
		return GetAnchor( GetPokerUrl( $handId ), $handName );
	}

	function GetVideoAnchor( $videoId, $videoName ) {
		return GetAnchor( GetVideoUrl( $videoId ), $videoName );
	}

	function GetGuildUrl( $guildId ) {
		if ( $guildId == 0 ) {
			return NULL;
		}

		return "myzv-view-guild.html?id=$guildId";
	}

	function GetGuildAnchor( $guildId, $guildName ) {
		return GetAnchor( GetGuildUrl( $guildId ), $guildName );
	}

	function GetPlayerUrl( $playerId ) {
		return "/myzv-view-player.html?id=$playerId";
	}

	function GetPlayerAnchor( $playerId, $playerName ) {
		return GetAnchor( GetPlayerUrl( $playerId ), $playerName );
	}

	function GetAnchor( $url, $text ) {
		if ( $url === NULL ) {
			return "";
		}

		return "<a href=\"$url\">$text</a>";
	}

	function GetTitledAnchor( $url, $title, $text ) {
		if ( $url === NULL ) {
			return "";
		}

		return "<a href=\"$url\" title=\"$title\">$text</a>";
	}

	function GetFactionUrl( $f ) {
		switch ( $f ) {
			case 1: return "myzv-view-faction.html?id=1";
			case 2: return "myzv-view-faction.html?id=2";
			case 3: return "myzv-view-faction.html?id=3";
			case 4: return "myzv-view-faction.html?id=4";

			default: return "myzv-factions.html";
		}
	}

	function GetRank( $i ) {
		switch ( ( $i % 100 ) > 20 ? $i%10 : $i )
		{
			case 1: return $i."st";
			case 2: return $i."nd";
			case 3: return $i."rd";
			default: return $i."th";
		}
	}

	function GetFactionIcon( $f )
	{
		$str = '<img src="'.GetFactionIconUrl( $f ).'" alt="'.GetFactionNameNoLink( $f ).'" style="float: left;" />';

		return GetFactionAnchor( $f, $str, true );

		return $str;
	}

	function GetFactionIconUrl( $f )
	{
		switch ( $f )
		{
			case 1: return "//storage.googleapis.com/cdn-1.appspot.com/myzv/images/minax-icon.gif";
			case 2: return "//storage.googleapis.com/cdn-1.appspot.com/myzv/images/council-icon.gif";
			case 3: return "//storage.googleapis.com/cdn-1.appspot.com/myzv/images/honor-icon.gif";
			case 4: return "//storage.googleapis.com/cdn-1.appspot.com/myzv/images/shadow-icon.gif";
			default:return "//storage.googleapis.com/cdn-1.appspot.com/myzv/images/null-icon.gif";
		}
	}

	function GetFactionNameNoLink( $f )
	{
		switch ( $f )
		{
			case 1: return "Followers of Minax";
			case 2: return "Council of Mages";
			case 3: return "True Britannians";
			case 4: return "Shadowlords";
			default:return "None";
		}
	}

	function GetFactionAnchor( $f, $input, $title ) {
		if ( $title ) {
			return GetTitledAnchor( GetFactionUrl( $f ), GetFactionNameNoLink( $f ), $input );
		} else {
			return GetAnchor( GetFactionUrl( $f ), $input );
		}
	}

	function GetFactionName( $f ) {
		return GetFactionAnchor( $f, GetFactionNameNoLink( $f ), false );
	}

	function GetFactionAbbriv( $f )
	{
		$abbriv = "";
		switch ( $f )
		{
		case 1: $abbriv = "Minax"; break;
		case 2: $abbriv = "CoM"; break;
		case 3: $abbriv = "True Brit"; break;
		case 4: $abbriv = "SL"; break;
		}

		if ( !empty( $abbriv ) )
			return "<a href=\"myzv-view-faction.html?id=$f\">$abbriv</a>";
		else
			return "None";
	}

	function renderHandTable( $db, $queryFilter, $sortOrder, $limit, $showPlayer = false ) {
		$query = $db->query( "SELECT PokerGames.PokerGameId,PokerGames.InitialPlayers, PokerGames.FinalPlayers, CAST(Credit AS Signed) - CAST(Debit AS Signed) AS winnings, HoleCards, Folded, char_id, char_name FROM PokerPlayers INNER JOIN PokerGames ON PokerGames.PokerGameId = PokerPlayers.PokerGameId LEFT JOIN myrunuo_characters ON char_id = Serial WHERE $queryFilter ORDER BY $sortOrder LIMIT $limit" );

		renderHandQuery( $db, $query, $showPlayer );
	}

	function renderHandQuery( $db, $query, $showPlayer = false ) {
		$rank = 0;
		while ( $hand = $db->fetchrow( $query ) ) {
			$rank++;
			$winnings = $hand['winnings'];
			if ( $showPlayer && $hand['char_id'] ) {
				$title = $hand['char_name'] . " ";
				if ( !$hand['Folded'] && $hand['FinalPlayers'] >= 2 ) {
					$title .= "with ";
				}
			} else {
				$title = $hand['InitialPlayers'] . " players, ";
			}
			if ( $hand['Folded'] ) {
				$title .= "folded";
			} else if ( $hand['FinalPlayers'] >= 2 ) {
				$title .= FormatCard( $hand['HoleCards'], 0 ) . " " .FormatCard( $hand['HoleCards'], 1 );
			} else {
				$title .= "mucked";
			}
			?>
			<div id="pokerhand" style="clear: both;">
			<div><? echo GetHandIcon( $hand['PokerGameId'] ); ?>
			<? echo GetHandAnchor( $hand['PokerGameId'], $title ); ?></div>
			<div style="font-size: 75%; margin-top: -10px;"><?
			if ( $winnings > 0 ) {
				?> <span class="won">(+<? echo number_format( $winnings ); ?>gp)</span><?
			} else if ( $winnings < 0 ) {
				?> <span class="lost">(<? echo number_format( $winnings ); ?>gp)</span><?
			} ?>
			</div>
			</div><?
		}
	}

	function renderPlayerTable( $db, $queryFilter, $sortOrder, $limit ) {
		$query = $db->query( "SELECT char_id, char_name, SUM(Credit) - SUM(Debit) as winnings FROM PokerGames g, PokerPlayers p, myrunuo_characters c WHERE g.StartTime > SUBDATE(NOW(),7) AND g.PokerGameId = p.PokerGameId AND char_id = serial GROUP BY char_id HAVING $queryFilter ORDER BY $sortOrder LIMIT $limit" );
		renderPlayerQuery( $db, $query );
	}

	function renderPlayerQuery( $db, $query ) {
		$rank = 0;
		while ( $player = $db->fetchrow( $query ) ) {
			$rank++;
			$char_id = $player['char_id'];
			$char_name = $player['char_name'];
			$winnings = $player['winnings']; ?>
		<div id="pokerplayer" style="clear: both;">
			<div><? echo GetSharkIcon( $char_id, $char_name ); ?>
			<strong><? echo GetSharkAnchor( $char_id, $char_name ); ?></strong>
			</div>
			<div style="font-size: 75%; margin-top: -10px;"><?
					echo number_format( $winnings );
				?>gp<?
					$daily = $db->query( "SELECT SUM(Credit) - SUM(Debit) AS delta FROM PokerPlayers INNER JOIN PokerGames ON PokerGames.PokerGameId = PokerPlayers.PokerGameId WHERE Serial = $char_id AND EndTime >= DATE_SUB( NOW(), INTERVAL 8 HOUR )" );
					$delta = $db->fetchfield( "delta" );
				if ( $delta > 0 ) {
					?> <span class="won">(+<? echo number_format( $delta ); ?>gp)</span><?
				} else if ( $delta < 0 ) {
					?> <span class="lost">(<? echo number_format( $delta ); ?>gp)</span><?
				} ?></div>
		</div><?
		}
	}

	function GetHandIcon( $handId ) {
		$str = '<img src="//storage.googleapis.com/cdn-1.appspot.com/myzv/images/hand-icon.png" alt="Hand Details" style="float: left;"/>';
		return GetAnchor( GetHandUrl( $handId ), $str );
	}

	function GetSharkIcon( $playerId, $playerName ) {
		$str = '<img src="//storage.googleapis.com/cdn-1.appspot.com/myzv/images/hand-icon.png" alt="$playerName" style="float: left;"/>';
		return GetAnchor( GetSharkUrl( $playerId ), $str );
	}

	function GetSharkUrl( $playerId ) {
		return "/myzv-poker-player.html?id=$playerId";
	}

	function GetSharkAnchor( $playerId, $playerName ) {
		return GetAnchor( GetSharkUrl( $playerId ), $playerName );
	}

	function GetHandUrl( $handId ) {
		return "/myzv-poker-hand.html?id=$handId";
	}

	function GetHandAnchor( $handId, $handName ) {
		return GetAnchor( GetHandUrl( $handId ), $handName );
	}

	function formatTime( $ms ) {
		$secs = (int)( $ms / 1000 );
		$mins = (int)( $secs / 60 );
		$hours = (int)( $mins / 60 );
		$mins %= 60;
		$secs %= 60;
		$str = "";
		if ( $hours > 0 ) {
			$str .= friendlyAmount( $hours, "hour", "hours" );
		}
		if ( $mins > 0 ) {
			if ( $hours > 0 ) {
				$str .= ", ";
			}
			$str .= friendlyAmount( $mins, "minute", "minutes" );
		}
		if ( $secs > 0 ) {
			if ( $hours > 0 || $mins > 0 ) {
				$str .= ", ";
			}
			$str .= friendlyAmount( $secs, "second", "seconds" );
		}
		return $str;
	}

	function friendlyAmount( $value, $singular, $plural ) {
		if ( $value == 1 ) {
			return $value . " " . $singular;
		} else {
			return $value . " " . $plural;
		}
	}

	function FormatCard( $str, $index ) {
		return substr( $str, $index * 2, 2 );
	}
?>
