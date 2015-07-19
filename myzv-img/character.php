<?php
	include_once "../myzv-inc/config.php";

	$MUL_PATH = "../myzv-mul/"; // must end in a slash
	$Y_OFFSET = -40;
	$X_OFFSET = 0;

	function Stop( $msg = FALSE )
	{
		global $tiles, $hues, $gumps, $gidx, $img;

		if ( $tiles )
			@fclose( $tiles );
		if ( $hues )
			@fclose( $hues );
		if ( $gumps )
			@fclose( $gumps );
		if ( $gidx )
			@fclose( $gidx );
		if ( $img )
			@imagedestroy( $img );

		if ( $msg )
			die( $msg );
		else
			die;
	}

	function ReadInt( $fp )
	{
		$data = unpack( "V1val", fread( $fp, 4 ) );
		return (int)$data['val'];
	}

	function ReadShort( $fp )
	{
		$data = unpack( "v1val", fread( $fp, 2 ) );
		return (int)$data['val'];
	}

	$id = intval($_REQ['id']);

	if ( $id <= 0 )
		die( "Invalid character id" );

	$m = new Memcached();
	$m->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE);
	$m->setSaslAuthData( getenv('MEMCACHIER_USERNAME'), getenv('MEMCACHIER_PASSWORD') );
	$parts = explode(":", getenv('MEMCACHIER_SERVERS'));
	$m->addServer($parts[0], $parts[1]);

	$mkey = "char-$id";
	$media = $m->get($mkey);

	if (!$m->getResultCode()) {
		header('Content-Type: image/png');
		header("Content-Disposition: filename=character-$id.png");
		echo $media;
		return;
	}

	$tiles = fopen( $MUL_PATH."tiledata.mul", "rb" );
	$hues = fopen( $MUL_PATH."hues.mul", "rb" );
	$gumps = fopen( $MUL_PATH."gumpart.mul", "rb" );
	$gidx = fopen( $MUL_PATH."gumpidx.mul", "rb" );

	$landoffset = (512*((4+2+20)*32+4));

	if ( !( $tiles && $hues && $gumps && $gidx ) )
		 Stop( "Cant open files: " . $tiles ." ". $hues ." ". $gumps ." ". $gidx );

	$db->query( "SELECT char_female, char_hue FROM myrunuo_characters WHERE char_id = ".$id );
	if ( !$char = $db->fetchrow() )
		Stop( "Character not found." );

	$img = imagecreatetruecolor( 180, 200 );
	imagesavealpha($img, true);

	$color = imagecolorallocatealpha($img,0x00,0x00,0x00,127);
	imagefill($img, 0, 0, $color);

	$img_width = imagesx( $img );
	$img_height = imagesy( $img );

	$HueTable = array();
	$DefTable = FALSE;

	$items = array();
	$items[0] = array( 'item_id' => -1, 'item_hue' => $char['char_hue'] );
	$i = 1;

	$db->query( "SELECT item_id, item_hue FROM myrunuo_characters_layers WHERE char_id = ".$id );
	while ( $item=$db->fetchrow() )
		$items[$i++] = $item;

	foreach( $items as $item )
	{
		if ( $item['item_id'] != -1 )
		{
			$to = $landoffset;
			$to += intval($item['item_id'])*37 + (intval($item['item_id']/32)+1) * 4;
			fseek( $tiles, $to, SEEK_SET );
			$flags = ReadInt( $tiles );
			fseek( $tiles, 6, SEEK_CUR );
			$gp = ($char['char_female'] ? 60000 : 50000) + ReadShort( $tiles );
		}
		else
		{
			$flags = 0;
			$gp = 0xC + $char['char_female'];
		}

		fseek( $gidx, $gp*12, SEEK_SET );

		$pos = ReadInt( $gidx );
		$len = ReadInt( $gidx );
		$height = ReadShort( $gidx );
		$width = ReadShort( $gidx );

		if ( $pos < 0 )
		{
			/*if ( !$DefTable )
			{
				$DefTable = array();

				if ( $def = @fopen( $MUL_PATH . "gump.def", "rb" ) )
				{
					while ( !feof( $def ) )
					{
						$line = trim( fgets( $def ) );
						if ( $line[0] == '#' || $line == "" )
							continue;
						$vals = split( "[ \t\{\}]", $line );

						$DefTable[intval($vals[0])] = array( intval($vals[2]), intval($vals[4]) );
					}
					@fclose( $def );
				}
			}

			if ( is_array( $DefTable[$gp] ) )
			{
				fseek( $gidx, $DefTable[$gp][0]*12, SEEK_SET );
				$item['item_hue'] = $DefTable[$gp][1];

				$pos = ReadInt( $gidx );
				$len = ReadInt( $gidx );
				$height = ReadShort( $gidx );
				$width = ReadShort( $gidx );
			}
			else
			{
				print_r( $DefTable );
			}


			if ( $pos < 0 )*/
				continue;
		}

		if ( $item['item_hue'] > 0 )
		{
			if ( ($flags&0x00040000) == 0 )
				$item['item_hue'] ^= 0x8000;

			$skin = ($item['item_hue']&0x8000) == 0;

			$item['item_hue'] = ($item['item_hue']&0x3FFF) - 1;
			if ( !isset( $HueTable[$item['item_hue']] ) && $item['item_hue'] >= 0 )
			{
				$hp = intval($item['item_hue']/8)*708 + 4 + ($item['item_hue']%8)*88;
				fseek( $hues, $hp, SEEK_SET );

				$hue = array();
				for($i=0;$i < 34;$i++)
					$hue[$i] = ReadShort( $hues )|0x8000;

				$HueTable[$item['item_hue']] = $hue;
			}
		}
		else
		{
			$item['item_hue'] = -1;
		}

		fseek( $gumps, $pos, SEEK_SET );

		$lookups = array();
		for ($y=0;$y < $height;$y++)
			$lookups[$y] = $pos + (ReadInt( $gumps )*4);

		for ($y=0;$y < $height && $y+$Y_OFFSET < $img_height;$y++)
		{
			fseek( $gumps, $lookups[$y], SEEK_SET );

			for ($x = 0; $x < $width; )
			{
				$color = ReadShort( $gumps );
				$count = ReadShort( $gumps );

				if ( $color == 0 || $color == 1 || $x+$X_OFFSET >= $img_width )
				{
					$x += $count;
				}
				else if ( $count > 0 )
				{
					if ( $item['item_hue'] >= 0 )
					{
						$h = ($color >> 10)&0x1F;
						if ( !$skin || ( $h == (($color>>5)&0x1F) && $h == ($color&0x1F) ) )
							$color = $HueTable[$item['item_hue']][$h];
					}

					// if images appear blue, or red and blue are mixed up, switch color transforms below:
					//$color = (($color&0x7C00) >> 7) | (($color&0x03E0) << 6) | (($color&0x001F) << 19);
					$color = (($color&0x7C00) << 9) | (($color&0x03E0) << 6) | (($color&0x001F) << 3);

					$end = $x+$count;
					for( ; $x < $end ; $x++ )
					{
						if ( $x+$X_OFFSET < $img_width )
							imagesetpixel( $img, $x+$X_OFFSET, $y+$Y_OFFSET, $color );
					}
				}
			}
		}
	}

	ob_start();
	header('Content-Type: image/png');
	header("Content-Disposition: filename=character-$id.png");
	imagepng( $img );
	$m->set($mkey, ob_get_contents(), 3600);
	ob_end_flush();
	Stop();
?>
