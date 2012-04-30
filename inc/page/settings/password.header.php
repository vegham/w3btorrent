<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	return;
}

if (isset($_POST['passwordOld'],$_POST['passwordNew'],$_POST['passwordNewAgain']))
{
	if (empty($_POST['passwordOld']) || empty($_POST['passwordNew']) || empty($_POST['passwordNewAgain']))
	{
		$status['password'] = "Error: there are empty fields.";
	}
	else if (md5(sha1($_POST['passwordOld'])) != $_SESSION[$_SERVER['REMOTE_ADDR']]['account']['password'])
	{
		$status['password'] = "Error: old password is wrong.";
	}
	else if ($_POST['passwordNew'] != $_POST['passwordNewAgain'])
	{
		$status['password'] = "Error: new password does not match.";
	}
	else if (account::update(-1,$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['username'],$_POST['passwordNew'],$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['type']))
	{
		$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['password'] = md5(sha1($_POST['passwordNew']));
		$status['password'] = "Password updated.";
	}
	else 
	{
		$status['password'] = "Error: there was an error saving the change. Contact someone if this error continues.";
	}
}

?>