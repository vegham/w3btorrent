<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	return;
}

require_once("inc/class/rtorrent.class.php");

if (isset($_POST['rtorrent'],$_POST['rpc'],$_POST['downRate'],$_POST['upRate']))
{
	if (!is_numeric($_POST['downRate']) || $_POST['downRate'] < 0)
	{
		$status = "Download rate must be a positive number.";
	}
	else if(!is_numeric($_POST['upRate']) || $_POST['upRate'] < 0)
	{
		$status = "Upload rate must be a positive number.";
	}
	else
	{
		settings::setRpc($_POST['rpc']);
		$_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'] = $_POST['rpc'];
		
		$s['set_download_rate'] = $_POST['downRate']."k";
		$s['set_upload_rate'] = $_POST['upRate']."k";
		
		foreach ($s as $key=>$value)
		{
			rtorrent::set($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$key,$value);
		}
		$status['rtorrent'] = "Settings updated.";
	}
}

?>