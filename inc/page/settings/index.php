<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	return;
}

if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['admin']))
{
	foreach (glob("inc/page/settings/*.form.php") as $file)
	{
		echo '<div style="min-width:400px;width:50%;float:left;">';
		include($file);
		echo '</div>';
	}
}
else
{
	foreach (glob("inc/page/settings/*.user.form.php") as $file)
	{
		echo '<div style="min-width:400px;width:50%;float:left;">';
		include($file);
		echo '</div>';
	}
}

?>



<div style="clear:both;"></div>
