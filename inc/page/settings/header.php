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

require_once("inc/class/mysql.class.php");
require_once("inc/class/w3btorrent.class.php");
require_once("inc/class/settings.class.php");
require_once("inc/class/misc.class.php");



// config settings to be stored in xml-file will be done in this header.php
// SQL settings will be stored used external classes

// load config
$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
$_SESSION[$_SERVER['REMOTE_ADDR']]['cfg'] = misc::xml2array($cfg);

if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])
{
	mysql::connect
	(
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['hostname'],
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['username'],
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['password']
	);
}

// include header files
foreach (glob("inc/page/settings/*.header.php") as $file)
{
	include($file);
}


// save config
if (isset($update))
{
	if (file_put_contents($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg'],$cfg->asXML()))
	{
		require_once("inc/init.inc.php"); // reset all
	}
	else
	{
		$status[$update] = "Unable to store changes. Possible permissions issue.";
	}
}

if (isset($_POST['ajax'],$_POST['submit'],$status))
{
	echo array_pop($status);
	exit();
}

?>
