<form id="upload" method="post" action="?p=u" enctype="multipart/form-data">
	<div class="topic"><img src="<?php echo $CONFIG['icon']['upload']['upload']; ?>" alt="" /> Upload torrent - use torrent from your computer<hr /></div>
	<div class="belowSpace">
		<div class="col1">Start when added</div>
		<div><input type="checkbox" name="start" /></div>
	</div>
	<div>
		<div class="col1"></div>
		<div><input class="p69 belowSpace" type="file" name="upload" value="" onchange="this.form.submit();" /></div>
	</div>
	<br />
	<span class="textLeft status" id="uploadStatus"><?php if (isset($status['upload'])) { echo $status['upload']; } ?></span>
	<br />
	<br />
</form>