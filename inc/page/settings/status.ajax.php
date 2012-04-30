<?php

chdir("../../../");
require_once("inc/default.inc.php");
require_once("inc/class/misc.class.php");
require_once("inc/class/w3btorrent.class.php");
require_once("inc/class/rtorrent.class.php");

$crontab = $CONFIG['icon']['status']['fail'];
$crontabMsg = "";
if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['crontab']))
{
	if (w3btorrent::crontabIsInstalled($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['crontab']))
	{
		$crontab = $CONFIG['icon']['status']['ok'];
	}
	else
	{
		$crontabMsg = "(unable to add cronjob)";
	}
}
else
{
	$crontabMsg = "(you don't have crontab and w3btorrent is not configured to work with anything else)";
}


$pid = rtorrent::getPid($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['ps'],$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['rtorrent']);

$rtorrent 	= $CONFIG['icon']['status']['fail'];
$rtorrentMsg = "";
if ($pid)
{
	$rtorrent = $CONFIG['icon']['status']['ok'];
}
elseif($crontabMsg != "")
{
	$rtorrentMsg = "(automatically started within 5 minutes)";
}

echo json_encode(array("rtorrent"=>array("icon"=>$rtorrent,"msg"=>$rtorrentMsg),"crontab"=>array("icon"=>$crontab,"msg"=>$crontabMsg)));

?>