<?php

/*
This file will verify w3btorrent each time it's being run.
This means that it will:
- check if index.php is in download folder or create it if not
- make sure .rtorrent.rc is in download folder
- make sure rtorrent is running
*/

// requirements
require_once("inc/default.inc.php");
require_once("inc/class/misc.class.php");
require_once("inc/class/hdc.class.php");
require_once("inc/class/rtorrent.class.php");
require_once("inc/class/w3btorrent.class.php");

// load config to get dDir
require_once("inc/init.inc.php");
w3btorrent::init();

session_write_close();
$ps 			= (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['ps'])?$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['ps']:"");
$rtorrent 	= (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['rtorrent'])?$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['rtorrent']:"");

//
// dDir
//
echo "download directory...";
if (!empty($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']))	// a valid dDir has not been provided
{
	echo "OK\n";
	// prohibit file listing access
	if (!is_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']."index.php"))
	{
		file_put_contents($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']."index.php",'<?php header("location: '.misc::url(3).'"); ?>');
	}
	
	// create initial file so we are able to connect to rtorrent
	if (!is_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].".rtorrent.rc"))
	{
		/*print_r(rtorrent::getSettings($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc']));*/
		$content  = "scgi_port = ".w3btorrent::getScgi()."\n";
		$content .= "directory = ".$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']."\n";
		file_put_contents($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].".rtorrent.rc",$content);
	}
}
else
{
	echo "missing\n";
}


//
// CHECK IF RTORRENT IS RUNNING OR START IT
//
echo "rtorrent...";
if (!$pid = rtorrent::getPid($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$ps,$rtorrent))
{
	echo "FAIL\n";
	if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['screen']))
	{
		echo "Missing the program `screen`\n";
		return;
	}
	if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['rtorrent']))
	{
		echo "Missing the program `rtorrent`\n";
		return;
	}
	
	echo "Starting rtorrent...";
	$pid = rtorrent::execute($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['screen'],$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['rtorrent'],$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']);
	if ($pid)
	{
		echo "$pid (PID)\n\n";
	}
	else
	{
		echo "FAIL\n";
	}
	
	return;
}
else 
{
	echo "OK\n"; 
}


echo "crontab";

ini_set("max_execution_time",180);


?>
