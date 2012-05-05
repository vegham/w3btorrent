<?php

require_once("inc/class/misc.class.php");
require_once("inc/class/rtorrent.class.php");

// torrent actions
if (isset($_POST['action']))
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
	else if ($_SESSION[$_SERVER['REMOTE_ADDR']]['admin']) // only admin can change these global settings
	{
		if ($_POST['action'] == "stopAll")
		{
			rtorrent::stopAll($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc']);
			sleep(2);// give it some time before asking for status
		}
		else if ($_POST['action'] == "startAll")
		{
			rtorrent::startAll($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc']);
			sleep(2);// give it some time before asking for status
		}
		else if (isset($_POST['value']) && in_array($_POST['action'],array("set_download_rate","set_upload_rate","set_max_peers","set_max_peers_seed")))
		{
			$value = trim(strtolower($_POST['value']));
			if ($value == "off" || $value < 0)
			{
				$value = 0;
			}
			if ($_POST['action'] == "set_download_rate" || $_POST['action'] == "set_upload_rate")
			{
				$value = str_replace("b","",$value);
				if (is_numeric(substr($value,-1)))
				{
					$value .= "k";
				}
			}
			rtorrent::set($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$_POST['action'],$value);
			unset($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['lastUpdate']);
		}
	}
}


$status = rtorrent::status($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc']);

// SPACE
$size						= $status['total']['size'];
$freeSpace 					= disk_free_space($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']);

$status['total']['freeSpace'] 	= misc::byteChange($freeSpace,0);
$status['total']['size']		= misc::byteChange($status['total']['size'],0);

// if we have same unit, save HTML pace
if (substr($status['total']['size'],-3) == substr($status['total']['freeSpace'],-3))
{
	$status['total']['size'] = substr($status['total']['size'],0,-3);	// remove unit	
}
// graphical warning if size is over disk limit
if ($size > $freeSpace)
{
	$status['total']['size'] = '<span class=\"error\">'.$status['total']['size'].'</span>';
}


// LOADED
$status['total']['down']['loaded'] 	= misc::byteChange($status['total']['down']['loaded'],3);
$status['total']['up']['loaded'] 		= misc::byteChange($status['total']['up']['loaded'],3);

// RATE
$status['total']['down']['rate'] 	= misc::byteChange($status['total']['down']['rate'],2);
$status['total']['up']['rate'] 	= misc::byteChange($status['total']['up']['rate'],2);

if ($_SESSION[$_SERVER['REMOTE_ADDR']]['admin'])
{
	// only run each 5 min 
	if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['lastUpdate']) || $_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['lastUpdate'] < time()-300)
	{
		$_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['lastUpdate'] 			= time();
		$_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['set']['down']['rate'] 	= rtorrent::get($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],'get_download_rate');
		$_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['set']['up']['rate'] 	= rtorrent::get($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],'get_upload_rate');
		$_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['set']['maxSeeds'] 	= rtorrent::get($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],'get_max_peers_seed');
		$_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['set']['maxLeech'] 	= rtorrent::get($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],'get_max_peers');
	}
	
	foreach (array("down","up") as $type)
	{
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['set'][$type]['rate'] > 0)
		{
			$unit = substr($status['total'][$type]['rate'],-2);
			if ($unit == " B")
			{
				$unit = "KB";
			}
			else
			{
				$status['total'][$type]['rate']	= substr($status['total'][$type]['rate'],0,-3);
			}
			$status['total'][$type]['setRate'] = misc::byteChange($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['set'][$type]['rate'],0,"KB").$unit;
		}
		else
		{
			$status['total'][$type]['setRate'] = "off";
		}
	}
	
	$status['total']['maxSeeds'] 	= (string)($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['set']['maxSeeds']>0?$_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['set']['maxSeeds']:"off");
	$status['total']['maxLeech'] 	= (string)($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['set']['maxLeech']>0?$_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['set']['maxLeech']:"off");

}


// only display torrent JSON
if (isset($_POST['ajax'],$_POST['refresh']))
{
	echo json_encode($status);
	exit();
}



?>