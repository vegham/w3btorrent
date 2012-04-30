<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	return;
}

// fix this crap
foreach ($_POST as $key=>$val)
{
	$_POST[$key] = stripslashes($val);
}

foreach (glob("inc/page/upload/*.header.php") as $file)
{
	include($file);
}

if (isset($_POST['ajax'],$_POST['submit'],$status))
{
	echo array_pop($status);
	exit();
}

?>