<?php

if (!function_exists("xmlrpc_encode_request"))
{
	return;
}

?>
<form id="rtorrent" method="post" action="?p=s">
	<div class="topic">rTorrent - setup your torrent client<hr /></div>
	<div class="belowSpace">
		<div class="col2"><img src="<?php echo $CONFIG['icon']['rtorrent']['rpc']; ?>" alt="" /> RPC address</div>
		<div><input type="text" name="rpc" value="<?php echo $_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc']; ?>" /></div>
	</div>
	<div class="belowSpace">
		<div class="col2"><img src="<?php echo $CONFIG['icon']['rtorrent']['download']; ?>" alt="" /> Max. download rate (KB/s)</div>
		<div><input type="text" name="downRate" value="<?php echo rtorrent::get($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],'get_download_rate')/1024; ?>" /> (0 = off)</div>
	</div>
	<div>
		<div class="col2"><img src="<?php echo $CONFIG['icon']['rtorrent']['upload']; ?>" alt="" /> Max. upload rate (KB/s)</div>
		<div><input type="text" name="upRate" value="<?php echo rtorrent::get($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],'get_upload_rate')/1024; ?>" /> (0 = off)</div>
	</div>
	<br />
	
	<span class="textLeft"><input id="submitRtorrent" type="submit" value="Apply" onclick="return submitForm(['submitRtorrent'],'rtorrent','rtorrentError');" /></span>
	<span class="textLeft error" id="rtorrentError"><?php if (isset($status['rtorrent'])) { echo $status['rtorrent']; } ?></span>
	<br />
	<br />
</form>
