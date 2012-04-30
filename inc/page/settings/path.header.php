<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	return;
}
require_once("inc/class/rtorrent.class.php");

if (isset($_POST['path'],$_POST['ddir'],$_POST['userDdir']))
{
	if (empty($_POST['ddir']) || empty($_POST['userDdir']))
	{
		$status['path'] = "Error: missing ddir path.";
	}
	// let's try to create ddir
	elseif (!file_exists($_POST['ddir']) && !@mkdir($_POST['ddir']))
	{
		$status['path'] = "Error: directory does not exists.";
	}
	elseif (!is_dir($_POST['ddir']))
	{
		$status['path'] = "Error: not a directory.";
	}
	elseif (!is_readable($_POST['ddir']))
	{
		$status['path'] = "Error: directory is not readable (be able to read files in directory).";
	}
	elseif (!is_writeable($_POST['ddir']))
	{
		$status['path'] = "Error: directory is not writeable (be able to create new files in directory).";
	}
	elseif (!is_executable($_POST['ddir']))
	{
		$status['path'] = "Error: directory is not executable (be able to browse/read inside the directory).";
	}
	elseif (!file_exists($_POST['userDdir']) && !@mkdir($_POST['userDdir']))
	{
		$status['path'] = "Error: directory does not exists.";
	}
	elseif (!is_dir($_POST['userDdir']))
	{
		$status['path'] = "Error: not a directory.";
	}
	elseif (!is_readable($_POST['userDdir']))
	{
		$status['path'] = "Error: directory is not readable (be able to read files in directory).";
	}
	elseif (!is_writeable($_POST['userDdir']))
	{
		$status['path'] = "Error: directory is not writeable (be able to create new files in directory).";
	}
	elseif (!is_executable($_POST['userDdir']))
	{
		$status['path'] = "Error: user directory is not executable (be able to browse/read inside the directory).";
	}
	else
	{
		$ddir 		= str_replace("//","/",realpath($_POST['ddir'])."/");
		$userDdir 	= str_replace("//","/",realpath($_POST['userDdir'])."/");
		touch($ddir."index.php");
		$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'] 		= $ddir;
		$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir'] 	= $userDdir;
		w3btorrent::write2log("settings","Download directory changed to '".$ddir."'.");
		w3btorrent::write2log("settings","User download directory changed to '".$ddir."'.");
		$status['path'] = "Download directory updated. Logout and login for the changes to take effect.";
		
		settings::setDdir($ddir);
		settings::setUserDdir($userDdir);
		
		rtorrent::set($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],"set_directory",$ddir);	// send changes to rtorrent
		
		
	}
}
elseif (empty($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']))
{
	$status['path'] = "Error: you must specify a directory to be able to use w3btorrent properly.";
}
elseif (empty($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir']))
{
	$status['path'] = "Error: you must specify a user directory to be able to use w3btorrent properly.";
}
elseif (misc::inWebTree($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']))
{
	$status['path'] = "Warning: current download directory is within your web tree, this means anyone can access your files.";
}

?>