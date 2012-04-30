<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	return;
}

if (isset($_POST['mysql'],$_POST['hostname'],$_POST['port'],$_POST['username'],$_POST['password'],$_POST['db']))
{
	if (strlen($_POST['hostname']) < 3)
	{
		$status['mysql'] = "Invalid hostname.";
	}
	else if (!is_numeric($_POST['port']) || $_POST['port'] < 1)
	{
		$status['mysql'] = "Invalid port.";
	}
	else if (strlen($_POST['username']) < 1)
	{
		$status['mysql'] = "Invalid username.";
	}
	else if (strlen($_POST['db']) < 1)
	{
		$status['mysql'] = "Invalid database name.";
	}
	else if (!@fsockopen($_POST['hostname'],$_POST['port']))
	{
		$status['mysql'] = "Service not running. Maybe wrong hostname or port?";
	}
	else if (!mysql::connect($_POST['hostname'].":".$_POST['port'],$_POST['username'],$_POST['password']))
	{
		$status['mysql'] = mysql::error();
	}
	else if ($error = mysql::selectDb($_POST['db']))
	{
		if ($error != 1)
		{
			$status['mysql'] = mysql::error();
		}
		else
		{
			$update = "mysql";
			$cfg->mysql->hostname 	= $_POST['hostname'];
			$cfg->mysql->port 		= $_POST['port'];
			$cfg->mysql->username 	= $_POST['username'];
			$cfg->mysql->password 	= $_POST['password'];
			$cfg->mysql->db 		= $_POST['db'];
			$cfg->mysql->enabled 	= (isset($_POST['enabled'])?'1':'0');
			(string)$cfg->virgin = "bleed";
		}
	}
	
	// in case of disabling mysql
	if (!isset($update) && (isset($_POST['enabled'])?'1':'0') != $_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])
	{
		$update = "mysql";
		$cfg->mysql->enabled 	= (isset($_POST['enabled'])?'1':'0');
		$status['mysql'] = "Settings updated.";
	}
}

?>