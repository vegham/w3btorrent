<form id="upload" method="post" action="?p=u" enctype="multipart/form-data">
	<div class="topic">Upload torrent - use torrent from your computer<hr /></div>
	<img src="<?php echo $CONFIG['icon']['upload']['upload']; ?>" alt="" /><input class="p74 belowSpace" type="file" name="upload" value="" onchange="this.form.submit();" />
	<br />
	<span class="textLeft status" id="uploadStatus"><?php if (isset($status['upload'])) { echo $status['upload']; } ?></span>
	<br />
	<br />
</form>