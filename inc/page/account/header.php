<?php

require_once("inc/class/w3btorrent.class.php");
require_once("inc/class/account.class.php");
require_once("inc/class/mysql.class.php");
require_once("inc/class/misc.class.php");

if (isset($_POST['url'],$_POST['user'],$_POST['password']))
{
	// connect to mysql if we are using mysql
	if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])
	{
		mysql::connect
		(
			$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['hostname'],
			$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['username'],
			$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['password']
		);
	}	
	if (!$user = account::login($_POST['user'],$_POST['password']))
	{
		$status = "Wrong user/password.";
		w3btorrent::write2log("account","Invalid login.");
	}
	else
	{
		header("location: ".$_POST['url']);
		exit();
	}
}
else if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account'],$_GET['logout']))
{
	w3btorrent::write2log("account",$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['username']." logout.");
	unset($_SESSION[$_SERVER['REMOTE_ADDR']]);
	header("location: ./");
	exit();
}

?>
