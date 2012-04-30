<form id="path" method="post" action="?p=s">
	<div class="topic">Path - setup important paths<hr /></div>
	<div class="belowSpace">
		<div class="col1"><img src="<?php echo $CONFIG['icon']['path']['dDir']; ?>" alt="" /> Download dir</div>
		<div><input class="p74" type="text" name="ddir" value="<?php echo $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']; ?>" onkeyup="$('#userDdir').val(this.value+'/w3btorrent_users');" /></div>
	</div>
	<div>
		<div class="col1"><img src="<?php echo $CONFIG['icon']['path']['userDir']; ?>" alt="" /> User dir</div>
		<div><input class="p74" type="text" id="userDdir" name="userDdir" value="<?php echo $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir']; ?>" /></div>
	</div>
	<br />
	
	<span class="textLeft"><input id="submitPath" type="submit" value="Apply" onclick="return submitForm(['submitPath'],'path','pathError');" /></span>
	<span class="textLeft error" id="pathError"><?php if (isset($status['path'])) { echo $status['path']; } ?></span>
	<br />
	<br />
</form>
