<?php
	include_once "myzv-inc/request.php";
	$playerId = intval( $_REQ['id'] );
?>
<div class="paperdoll">
<div><img src="https://myzv.herokuapp.com/myzv-img/paperdoll.php?id=<?php echo $playerId; ?>" alt="Paperdoll" /></div>
<div style="margin-left: 1px;">
	<img style="float: left;" src="https://myzv.herokuapp.com/myzv-mul/paperdoll_left.png" border="0">
	<div style="float: left; height: 64px; width: 198px; background-image: url('https://myzv.herokuapp.com/myzv-mul/paperdoll_text.png');">The Friendly Player</div>
	<img style="float: none;" src="https://myzv.herokuapp.com/myzv-mul/paperdoll_right.png" border="0">
</div>
</div>
