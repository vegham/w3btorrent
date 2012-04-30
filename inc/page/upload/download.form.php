<script type="text/javascript">
function downloadTorrent(link)
{
	if (link != '' && $('#downloadLink').val() == link && $('#submitDownload').attr('disabled') != 'disabled')
	{
		submitForm(['submitDownload'],'download','downloadStatus');
	}
}
$(document).ready(function() {
        $("#downloadLink").bind('paste', function(e) {
        	setTimeout('downloadTorrent(\''+$('#downloadLink').val()+'\')',1000);
        });
});
</script>
<form id="download" method="post" action="?p=u">
	<div class="topic">Download torrent - get torrents from another server<hr /></div>
	<div>
		<div class="col2"><img src="<?php echo $CONFIG['icon']['upload']['download']; ?>" alt="" /> http, ftp or magnet link</div>
		<div><input class="p69" type="text" id="downloadLink" name="downloadLink" onkeyup="setTimeout('downloadTorrent(\''+this.value+'\')',2500);" onchange="setTimeout('downloadTorrent(\''+this.value+'\')',2500);" /></div>
	</div>
	<span class="textLeft"><input id="submitDownload" type="submit" value="Download" onclick="return submitForm(['submitDownload'],'download','downloadStatus');" /></span>
	<span class="textLeft status" id="downloadStatus"><?php if (isset($status['download'])) { echo $status['download']; } ?></span>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
</form>
