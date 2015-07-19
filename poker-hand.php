<?php
	include_once "myzv-inc/config.php";

	$pokerGameId = intval( $_REQ['id'] );

	$mpoker = "pg-".$pokerGameId;

        $m = new Memcached();
        $m->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
        $m->setSaslAuthData( getenv('MEMCACHIER_USERNAME'), getenv('MEMCACHIER_PASSWORD') );
        $parts = explode(":", getenv('MEMCACHIER_SERVERS'));
        $m->addServer($parts[0], $parts[1]);

        $pokerGame = $m->get($mpoker);

        if ($m->getResultCode()) {
		$db->query( "SELECT * FROM PokerGames WHERE PokerGameId = $pokerGameId" );
		$pokerGame = $db->fetchrow();

		if ($pokerGame)
			$m->set($mpoker, $pokerGame);
	}

	if ( !$pokerGame ) {
		$SPIDER = "noindex,follow";
	}
?>
<? if ( !$pokerGame ) { ?>
<fieldset>
	<legend>General Information</legend>
	<em>Hand not found.</em>
</fieldset>
<? } else { ?>
<table>
	<tr>
		<td valign="top" class="half">
			<!-- general information -->
			<fieldset>
				<legend>General Information</legend>
				<table cellpadding="3" cellspacing="1" width="100%">
					<tr>
						<td><label for="played-on-value">Date</label></td>
						<td id="played-on-value" align="right" class="nowrap"><? echo date( "F ", strtotime( $pokerGame['StartTime'] ) ).GetRank( date( "j", strtotime( $pokerGame['StartTime'] ) ) ).date( ", Y", strtotime( $pokerGame['StartTime'] ) ); ?></td>
					</tr>
					<tr>
						<td><label for="time-stamp-value">Time</label></td>
						<td id="time-stamp-value" align="right" class="nowrap"><? echo date( "g:i A", strtotime( $pokerGame['StartTime'] ) ); ?></td>
					</tr>
					<tr>
						<td><label for="duration-value">Length</label></td>
						<td id="duration-value" align="right" class="nowrap"><? echo formatTime( (strtotime( $pokerGame['EndTime'] ) - strtotime( $pokerGame['StartTime'] )) * 1000 ); ?></td>
					</tr>
					<tr>
						<td><label for="pot-value">Pot Size</label></td>
						<td id="pot-value" align="right" class="nowrap"><? echo number_format( $pokerGame['FinalPot'] ); ?>gp</td>
					</tr>
				</table>
			</fieldset>
			<!-- /general information -->
		</td>
		<td valign="top" class="half">
			<!-- players -->
			<fieldset>
				<legend>Players</legend>
				<table cellpadding="3" cellspacing="1">
<?
$players = $db->query( "SELECT char_id, char_name, Bankroll FROM PokerPlayers LEFT JOIN myrunuo_characters ON char_id = Serial WHERE PokerGameId = $pokerGameId ORDER BY PokerPlayerId" );

			$c = 0;
			$p = 0;

			while ( ($player = $db->fetchrow( $players )) ) { ?>
								<tr>
									<td>
										<?

				$c++;
				$p++;

				echo $c, ". ";
				if ( $player['char_id'] ) {
					echo GetSharkAnchor( $player['char_id'], $player['char_name'] );
				} else {
					echo "n/a";
				} ?>, <span class="subtext"><? echo number_format( $player['Bankroll'] ), "gp"; ?></div>
									</td>
								</tr><?
			} ?>
							</table>
						</fieldset>
						<!-- /players -->
					</td>
				</tr>
				<tr>
					<td valign="top">
						<!-- actions -->
						<fieldset>
							<legend>Actions</legend>
							<table cellpadding="3" cellspacing="1">
<?
$actions = $db->query( "SELECT PokerActionId, Stage, Type, Amount, char_id, char_name FROM PokerActions INNER JOIN PokerPlayers ON PokerPlayers.PokerGameId = PokerActions.PokerGameId AND PokerPlayers.PokerPlayerId = PokerActions.PokerPlayerId LEFT JOIN myrunuo_characters ON char_id = Serial WHERE PokerActions.PokerGameId = $pokerGameId ORDER BY PokerActionId" );

			$c = 0;
			$s = 0;
			$a = 0;

			while ( ($action = $db->fetchrow( $actions )) ) {
				$id  = $action['PokerActionId'];
				$type = $action['Type'];
				$amount = $action['Amount'];
				$stage = $action['Stage'];

				$a = $type;

				if ( $stage > $s ) {
					?>
								<tr>
									<td><h3><?
					if ( $s == 0 ) {
						echo "Pre Flop";
					} else if ( $s == 1 ) {
						echo "Flop";
					} else if ( $s == 2 ) {
						echo "Turn";
					} else if ( $s == 3 ) {
						echo "River";
					} ?></h3></td>
									<td><?
					if ( $s == 1 ) {
						echo "<em>", formatCard( $pokerGame['Community'], 0 ), " ", formatCard( $pokerGame['Community'], 1 ), " ", formatCard( $pokerGame['Community'], 2 ), "</em>";
					} else if ( $s == 2 ) {
						echo formatCard( $pokerGame['Community'], 0 ), " ", formatCard( $pokerGame['Community'], 1 ), " ", formatCard( $pokerGame['Community'], 2 ), " <em>", formatCard( $pokerGame['Community'], 3 ), "</em> ";
					} else if ( $s == 3 ) {
						echo formatCard( $pokerGame['Community'], 0 ), " ", formatCard( $pokerGame['Community'], 1 ), " ", formatCard( $pokerGame['Community'], 2 ), " ", formatCard( $pokerGame['Community'], 3 ), " <em>", formatCard( $pokerGame['Community'], 4 ), "</em>";
					}

					$c = 0;

					$s = $stage; ?></td>
								</tr><?
				} ?>

								<tr>
									<td colspan="2"><?

				$c++;

				echo $c, ". ";

				if ( $action['char_id'] ) {
					echo GetSharkAnchor( $action['char_id'], $action['char_name'] );
				} else {
					echo "n/a";
				}

				if ( $type == 0 ) {
					echo ' <span class="fold">folds</span>';
				} else if ( $type == 1 ) {
					echo ' <span class="check">checks</span>';
				} else if ( $type == 2 ) {
					echo ' <span class="call">calls ', number_format( $amount ), "gp</span>";
				} else if ( $type == 3 ) {
					if ( $id <= 2 ) {
						echo ' <span class="post">posts ', number_format( $amount ), "gp</span>";
					} else {
						echo ' <span class="bet">raises ', number_format( $amount ), "gp</span>";
					}
				}
				?>
									</td>
								</tr><?
			} ?>
							</table>
						</fieldset>
						<!-- /actions -->
					</td><?

			if ( $pokerGame['FinalPlayers'] >= 2 ) { ?>
					<td valign="top">
						<!-- showdown -->
						<fieldset>
							<legend>Showdown</legend>
							<table cellpadding="3" cellspacing="1">
								<tr>
									<td><h3>Community</h3></td>
									<td><? echo formatCard( $pokerGame['Community'], 0 ), " ", formatCard( $pokerGame['Community'], 1 ), " ", formatCard( $pokerGame['Community'], 2 ), " ", formatCard( $pokerGame['Community'], 3 ), " ", formatCard( $pokerGame['Community'], 4 ); ?></td>
								</tr><?
$showdown = $db->query( "SELECT PokerPlayers.*, char_id, char_name FROM PokerPlayers LEFT JOIN myrunuo_characters ON char_id = Serial WHERE PokerPlayers.PokerGameId = $pokerGameId AND Folded = 0 ORDER BY Credit ASC" );

			$c = 0;
			$s = 0;

				while ( ($showinfo = $db->fetchrow( $showdown )) ) {

					?>
								<tr>
									<td colspan="2"><?

					$c++;

					echo $c, ". ";

					if ( $showinfo['char_id'] ) {
						echo GetSharkAnchor( $showinfo['char_id'], $showinfo['char_name'] );
					} else {
						echo "n/a";
					}

					echo " shows <em>", FormatCard( $showinfo['HoleCards'], 0 ), " ", FormatCard( $showinfo['HoleCards'], 1 ), "</em>";

					if ( $showinfo['Credit'] > 0 ) {
						echo ' for <span class="won">', number_format( $showinfo['Credit'] ), "gp</span>";
					}

				}
				?>
									</td>
								</tr>
							</table>
						</fieldset>
						<!-- /showdown -->
					</td><?
			} ?>
				</tr>
			</table>
<? } ?>
