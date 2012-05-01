<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['admin']))
{
	return;
}

require_once("inc/class/account.class.php");

// account
if (empty($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']) && !isset($_POST['submit']))
{
	$status['account'] = "Error: you must specify a download directory before you can add users.";
}
else if (empty($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir']) && !isset($_POST['submit']))
{
	$status['account'] = "Error: you must specify a user directory before you can add users.";
}
else if (isset($_POST['account'],$_POST['id'],$_POST['type'],$_POST['username'],$_POST['password'],$_POST['passwordAgain']))
{
	//
	// ADD NEW USER
	//
	if (isset($_POST['add']))
	{
		if ($_POST['type'] != 0)
		{
			// check if another user has this path
			$path = $_POST['username'];
			$counter = 1;
			while (file_exists($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir']."/".$path))
			{
				$path = $_POST['username'].$counter;
				$counter++;
			}
		}

		if (empty($_POST['username']))
		{
			$status['account'] = "Error: missing username.";
		}
		elseif (strlen($_POST['username']) < 2)
		{
			$status['account'] = "Error: too short username.";
		}
		elseif (empty($_POST['password']) || empty($_POST['passwordAgain']))
		{
			$status['account'] = "Error: missing password.";
		}
		elseif ($_POST['password'] != $_POST['passwordAgain'])
		{
			$status['account'] = "Error: password mismatch, please enter to equal passwords.";
		}
		else if ($_POST['type'] != 0 && count(account::admins()) == 0)
		{
			$status['account'] = "The first account must be Admin.";
		}
		elseif (!is_dir($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir']))
		{
			$status['account'] = "Error: you don't have any user-dir in your download-dir. Reinstall it.";
		}
		else if (account::isUser($_POST['username']))
		{
			$status['account'] = 'Error: user already exists.';
		}
		else if ($_POST['type'] == 0)	// adding new admin
		{
			if (account::add($_POST['username'],$_POST['password'],$_POST['type'],"/"))
			{
				w3btorrent::write2log("settings","New user added with username '".$_POST['username']."'");
				$status['account'] = 'Account added.';
				if (count(account::admins()) == 1)
				{
					 $status['account'] .= ' Logout, login and enjoy w3btorrent.';
				}
			}
			else
			{
				$status['account'] = "Error: unable to add user for unknown reason.";
			}
		}
		// new regular user
		elseif (mkdir($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir']."/".$path))
		{
			if (account::add($_POST['username'],$_POST['password'],$_POST['type'],$path))
			{
				touch($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir']."/".$_POST['username']."/index.php");
				$status['account'] = 'Account added.<script type="text/javascript">$("#accountList").append("<option value=\"'.$_POST['username'].'\" label=\"'.$_POST['username'].'\">'.$_POST['type'].'</option>");</script>';
				
				w3btorrent::write2log("settings","New user added with username '".$_POST['username']."'");
			}
			else
			{
				$status['account'] = "Error: unable to add user for unknown reason.";
			}
		}
		else
		{
			$status['account'] = "Unable to create user directory. Maybe username is invalid?";
		}
	}
	elseif (isset($_POST['update']))
	{
		if (empty($_POST['id']))
		{
			$status['account'] = "Select a user first.";
		}
		else if ($_POST['password'] != $_POST['passwordAgain'])
		{
			$status['account'] = "Passwords must be equal.";
		}
		elseif (account::update($_POST['id'],$_POST['username'],$_POST['password'],$_POST['type']))
		{
			$status['account'] = 'Account updated.';
		}
		else
		{
			$status['account'] = "User don't exsist. Click add user.";
		}
	}
	else if (isset($_POST['delete']))
	{
		if (empty($_POST['id']))
		{
			$status['account'] = "Select a user first.";
		}
		else if($_SESSION[$_SERVER['REMOTE_ADDR']]['account']['username'] == $_POST['username'])
		{
			$status['account'] = "Error: you can't delete yourself.";
		}
		else if (account::delete($_POST['id']))
		{
			if (misc::rmrf($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'],$_POST['username']))
			{
				$status['account'] = 'Account deleted.';
			}
			else
			{
				$status['account'] = 'Account deleted, but user folder does still exist.';
			}
		}
		else
		{
			$status['account'] = "User don't exsist.";
		}
	}
	else
	{
		$status['account'] = "Unknown error occured. Maybe it's an browser issue. Please report this.";
	}
}
else if (count(account::admins()) < 1 && !isset($_POST['submit']))
{
	$status['account'] = "Error: there must be at least one user with admin rights.";
}

?>