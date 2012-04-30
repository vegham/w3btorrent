<form method="post" action="?p=s">
	<div class="topic">Status<hr /></div>
	<div class="belowSpace">
		<div class="col2"><img src="<?php echo $CONFIG['icon']['status']['crontab']; ?>" alt="" /> Crontab</div>
		<div><img id="crontabIcon" src="<?php echo $CONFIG['icon']['status']['loading']; ?>" alt="" /> <span id="crontabMsg"></span></div>
	</div>
	<div class="belowSpace">
		<div class="col2"><img src="<?php echo $CONFIG['icon']['status']['rtorrent']; ?>" alt="" /> rTorrent</div>
		<div><img id="rtorrentIcon" src="<?php echo $CONFIG['icon']['status']['loading']; ?>" alt="" /> <span id="rtorrentMsg"></span></div>
	</div>
	<div>
		<div class="col2"><img src="<?php echo $CONFIG['icon']['status']['connection']; ?>" alt="" /> rTorrent connection</div>
		<div><?php if (!function_exists("xmlrpc_encode_request")) { echo "You don't have XMLRPC installed."; } else { echo '<img src="'.$CONFIG['icon']['status']['ok'].'" alt="" /> (not yet reliable)'; } ?></div>
	</div>
	<br />
	<br />
</form>
<script type="text/javascript">
function getStatus()
{
	$.ajax({
		url:'inc/page/settings/status.ajax.php',
		type: 'POST',
		data: {ajax:1},
		timeout: 5000,
		error: function(request,error)
		{
			setTimeout("getStatus()",1000);		
		},
		success: function(json)
		{
			var json = jQuery.parseJSON(json);
			$('#crontabIcon').attr('src',json['crontab']['icon']);
			$('#crontabMsg').html(json['crontab']['msg']);
			$('#rtorrentIcon').attr('src',json['rtorrent']['icon']);
			$('#rtorrentMsg').html(json['rtorrent']['msg']);
		}
	});
}
$(document).ready(getStatus());
</script>
