<?php

/*	this file is part of w3btorrent, it runs one time for each new session
	it loads DB info to session, important directories and rtorrent RPC address
	if this is the first time w3btorrent is being used, this file gives the user a free pass
*/

require_once("configCheck.inc.php");
require_once("inc/class/w3btorrent.class.php");

$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'] = false;
if (isset($CONFIG['mysql']['enabled']) && $CONFIG['mysql']['enabled'])
{
	$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql'] = $CONFIG['mysql'];
}
else
{
	require_once("inc/class/misc.class.php");
	// config path
	$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg'] 	= realpath($CONFIG['cfg']);
	$_SESSION[$_SERVER['REMOTE_ADDR']]['cfg']			= misc::xml2array($cfg);

	// mysql
	$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'] = 0;
	if (isset($cfg->mysql,$cfg->mysql->enabled,$cfg->mysql->hostname,$cfg->mysql->username,$cfg->mysql->password,$cfg->mysql->db))
	{
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled']	= ((string)$cfg->mysql->enabled == 1?true:false);
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['hostname'] 	= (string)$cfg->mysql->hostname;
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['username'] 	= (string)$cfg->mysql->username;
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['password'] 	= (string)$cfg->mysql->password;
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'] 		= (string)$cfg->mysql->db;
	}
}

// use database
if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])
{
	require_once("inc/class/mysql.class.php");
	mysql::connect
	(
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['hostname'],
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['username'],
		$_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['password']
	);
}

	
// if we don't have an admin, let this user do whatever he likes

require_once("inc/class/account.class.php");
if (count(account::admins()) == 0)	// free pass
{
	$_SESSION[$_SERVER['REMOTE_ADDR']]['setup'] 	= true;
	$_SESSION[$_SERVER['REMOTE_ADDR']]['admin'] 	= true;
	$_SESSION[$_SERVER['REMOTE_ADDR']]['account'] 	= true;
	if (!w3btorrent::init())
	{
		$_SESSION[$_SERVER['REMOTE_ADDR']]['setup'] = "crontab";
	}
}

// setup ddir and userdir
$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'] 		= w3btorrent::getDdir();
$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir'] 	= w3btorrent::getUserDdir();



$_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'] = w3btorrent::getRpc();



?>
