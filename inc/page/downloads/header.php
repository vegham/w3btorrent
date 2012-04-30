<?php

require_once("inc/class/misc.class.php");
require_once("inc/class/rtorrent.class.php");

// torrent actions
if (isset($_POST['action']) && in_array($_POST['action'],array("stop","start","stopAll","startAll","delete")))
{
	if (isset($_GET['hash']) && is_array($_GET['hash']))
	{
		if ($_POST['action'] == "stop")
		{
			foreach ($_GET['hash'] as $hash)
			{
				rtorrent::stop($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$hash);
			}
		}
		else if ($_POST['action'] == "start")
		{
			foreach ($_GET['hash'] as $hash)
			{
				rtorrent::start($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$hash);
			}
		}
		else if ($_POST['action'] == "delete")
		{
			foreach ($_GET['hash'] as $hash)
			{
				rtorrent::delete($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$hash);
			}
		}
	}
	else if ($_POST['action'] == "stopAll")
	{
		rtorrent::stopAll($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc']);
		sleep(1);
	}
	else if ($_POST['action'] == "startAll")
	{
		rtorrent::startAll($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc']);
		sleep(1);
	}
	sleep(1);	// give it some time before asking for status
}



$status = rtorrent::status($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc']);

// only display torrent JSON
if (isset($_POST['ajax'],$_POST['refresh']))
{
	echo json_encode($status);
	exit();
}






?>