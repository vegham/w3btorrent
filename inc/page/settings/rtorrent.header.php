<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	return;
}
require_once("inc/class/rtorrent.class.php");

if (isset($_POST['rtorrent'],$_POST['rpc']))
{
	settings::setRpc($_POST['rpc']);
	$_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'] = $_POST['rpc'];
		
	$status['rtorrent'] = "Settings updated.";
}

?>