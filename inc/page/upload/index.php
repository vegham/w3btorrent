<div style="min-width:400px;width:50%;float:left;">
<?php

foreach (glob("inc/page/upload/*.form.php") as $file)
{
	include($file);
}
	
?>
</div>
<div style="min-width:400px;float:right;width:50%;">
	<div class="topic">Status<hr /></div>
	<textarea style="width:98%;height:50%;position:relative;left:10px;" cols=10 rows=10 readonly>Not in use in this version of w3btorrent.</textarea>
</div>
<div style="clear:left;"></div>