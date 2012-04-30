<span style="float:right;"><img src="pix/cross.png" alt="" /><a href="?p=a&logout" style="position:relative;top:-4px;">Logout</a></span>
<ul id="menu">
	<?php if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['setup'])) { ?>
	<li><a href="?p=d" onclick="return pt(this);" class="" id="downloads"><img src="<?php echo $CONFIG['icon']['menu']['downloads']; ?>" alt="" />Downloads</a></li> 
	<li><a href="?p=u" onclick="return pt(this);" class="" id="upload"><img src="<?php echo $CONFIG['icon']['menu']['upload']; ?>" alt="" />Upload</a></li> 
	<li><a href="?p=f" onclick="return pt(this);" class="" id="files"><img src="<?php echo $CONFIG['icon']['menu']['files']; ?>" alt="" />Files</a></li>
	<?php } ?>
	<li><a href="?p=s" onclick="return pt(this);" class="" id="settings"><img src="<?php echo $CONFIG['icon']['menu']['settings']; ?>" alt="" />Settings</a></li>
</ul>
