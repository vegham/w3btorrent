<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

require_once("CONFIG.php");
session_name("w3btorrent2");
session_start();
$_SESSION[$_SERVER['REMOTE_ADDR']]['title'] = "w3btorrent ".$CONFIG['version'];

// need to load config file?
if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['path']))
{
	// initialize w3btorrent
	require_once("init.inc.php");
}

?>
