<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	return;
}

require_once("inc/class/misc.class.php");
require_once("inc/class/hdc.class.php");
require_once("inc/class/w3btorrent.class.php");

// get path
$path = (isset($_GET['path'])?urldecode($_GET['path']):"/");

// clean and secure paths
$realPath = realpath($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'].$path);

// check path or someone is trying stuff
if (empty($realPath) || strlen($realPath) < strlen($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir']))
{
	$realPath = $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'];
	$path = "/";
}
elseif (is_dir($realPath))
{
	$realPath .= "/";
}

// some more cleaning just in case
$path 		= str_replace("//","/",$path);
$realPath 	= str_replace("//","/",$realPath);


//
// DELETE
//
if (isset($_POST['action'],$_GET['delete']) && $_POST['action'] == "delete" && is_array($_GET['delete']))
{
	foreach ($_GET['delete'] as $path2)
	{
		$realPath2 = realpath($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'].$path2);

		// verify security
		if (empty($realPath2) || strlen($realPath2) < strlen($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir']))
		{
			continue;	// some shady stuff is going on
		}
		misc::rmrf($realPath2);
		
		$path 		= dirname($path2)."/";
		$realPath 	= dirname($realPath2)."/";
	}
}


// include header files
foreach (glob("inc/page/files/*.header.php") as $file)
{
	include($file);
}

// make a big list with dir first
$list = array_merge(array($tree),array($dirs),array($files));

// only display JSON
if (isset($_POST['ajax']) && (isset($_POST['browse']) || isset($_POST['action'])))
{
	echo json_encode($list);
	exit();
}


?>
