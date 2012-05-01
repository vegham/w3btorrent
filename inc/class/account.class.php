<?php

/* this file is part of w3btorrent, it handles session for w3btorrent

v1.0.1
*/

class account
{
	private static $users = "w3btorrent_users";
	private function userTable()
	{
               $sql = "
                        create table
                                `".account::$users."`
                        (
                                `userId` int not null auto_increment primary key,
                                `username` varchar(32),
                                `password` char(32), /* md5, sha1 */
                                `type` int(1),
                                `path` varchar(32),
                                `lastLogin` timestamp default current_timestamp,
                                `lastLoginIp` varchar(15)
                        )
                ";
                return $sql;

	}
	
	public function login($argUser,$password)
	{
		// using SQL
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])
		{
			$sql = "
				select
					`userId`,
					`username`,
					`password`,
					`type`,
					`path`
				from
					`".account::$users."`
				where
					`username` = '".mysql::escape($argUser)."'
					&&
					`password` = '".md5(sha1($password))."'
			";
			$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],account::userTable(),$sql);
			if (isset($query[0][0]))
			{
				if ($query[0][3] == 0)
				{
					$_SESSION[$_SERVER['REMOTE_ADDR']]['admin'] = true;
					$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'] 	= $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].$query[0][4]."/";
				}
				else
				{
					$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'] 	= $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir'].$query[0][4]."/";
				}
				$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'] 		= str_replace("//","/",$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir']);
				$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['userId'] 		= $query[0][0];
				$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['username'] 	= $query[0][1];
				$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['password'] 	= $query[0][2];
				$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['type'] 		= $query[0][3];
				
				if (!w3btorrent::init())
				{
					$_SESSION[$_SERVER['REMOTE_ADDR']]['setup'] = "crontab";
				}
				return true;
			}
		}
		else
		{
			$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
			foreach ($cfg->users->user as $user)
			{
				// found user
				if ($argUser == (string)$user->username && md5(sha1($password)) == (string)$user->password)
				{
					if ((int)$user->type === 0)	// admin
					{
						$_SESSION[$_SERVER['REMOTE_ADDR']]['admin'] = true;
						$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'] = $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].(string)$user->path."/";
					}
					else 
					{
						$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'] = $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDdir'].(string)$user->path."/";
					}
					$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'] 		= str_replace("//","/",$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir']);
					$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['type']		= (int)$user->type;
					$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['username']	= (string)$user->username;
					$_SESSION[$_SERVER['REMOTE_ADDR']]['account']['password']	= (string)$user->password;
					
					
					if (!w3btorrent::init())
					{
						$_SESSION[$_SERVER['REMOTE_ADDR']]['setup'] = "crontab";
					}
					return true;
				}
			}
		}
	}
	
	public function add($username,$password,$type,$path)
	{
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])
		{
			$sql = "
				insert into
					`".account::$users."`
				values
				(
					'',
					'".mysql::escape($username)."',
					'".md5(sha1($password))."',
					'".(int)$type."',
					'".mysql::escape($path)."',
					'',
					''
				)
			";
			$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],account::userTable(),$sql);
			
			if (empty($query[0][0]))
			{
				return true;
			}
		}
		else
		{
			$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
			$id = count($cfg->users->user);
			$cfg->users->user[$id] 			= "";
			$cfg->users->user[$id]->username 	= $username;
			$cfg->users->user[$id]->password 	= md5(sha1($password));	
			$cfg->users->user[$id]->type 		= $type;
			$cfg->users->user[$id]->path 		= $path;
			if (file_put_contents($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg'],$cfg->asXML()))
			{
				$_SESSION[$_SERVER['REMOTE_ADDR']]['cfg'] = misc::xml2array($cfg);
				return true;
			}
		}
	}
	
	public function admins()
	{
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])	// should we use SQL?
		{
			$sql = "
				select
					*
				from
					`".account::$users."`
				where
					`type` = 0
			";
			$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],account::userTable(),$sql);
			
			if (isset($query[0][0]))
			{
				return $query;
			}
			return array();
		}
		else if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['cfg']['users']))
		{
			$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
			$admins = array();
			$i = 0;
			foreach ($cfg->users->user as $user)
			{
				if ((string)$user->type == 0)
				{
					$admins[] = array($i,(string)$user->username,(string)$user->password,(string)$user->type,(string)$user->path);
				}
				$i++;
			}

			return $admins;
		}
		else
		{
			return array();
		}
	}
	public function isUser($username)
	{
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])	// should we use SQL?
		{
			$sql = "
				select
					`userId`
				from
					`".account::$users."`
				where
					`username` = '".mysql::escape($username)."'
			";
			$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],account::userTable(),$sql);
			
			if (isset($query[0][0]))
			{
				return true;
			}
		}
		else
		{
			$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
			foreach ($cfg->users->user as $user)
			{
				if ((string)$user->username == $username)
				{
					return true;
				}
			}
		}
		return;
	}

	public function getUserByUserId($userId)
	{
		$sql = "
			select
				*
			from
				`".account::$users."`
			where
				`userId` = '".mysql::escape($userId)."'
		";
		$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],account::userTable(),$sql);
		
		if (isset($query[0][0]))
		{
			return $query[0];
		}
		return array();
	}

	public function getUserByUsername($username)
	{
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])	// should we use SQL?
			{
			$sql = "
				select
					*
				from
					`".account::$users."`
				where
					`username` = '".mysql::escape($username)."'
			";
			$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],account::userTable(),$sql);
			
			if (isset($query[0][0]))
			{
				return $query[0];
			}
		}
		else
		{
			$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
			$_SESSION[$_SERVER['REMOTE_ADDR']]['cfg'] = misc::xml2array($cfg);
			$i = 0;
			foreach ($_SESSION[$_SERVER['REMOTE_ADDR']]['cfg']['users']['user'] as $user)
			{
				if ($user['username'] == $username)
				{
					return array($i,$username,$user['password'],$user['type'],$user['path']);
				}
				$i++;
			}			
		}
	}

	public function userList()
	{
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])	// should we use SQL?
		{
			$sql = "
				select
					`userId`,
					`username`,
					`type`
				from
					`".account::$users."`
				order by
					`username`
			";
			$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],account::userTable(),$sql);
			
			if (isset($query[0][0]))
			{
				return $query;
			}
			return array();
		}
		else
		{
			$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
			$users = array();
			$i = 0;
			foreach ($cfg->users->user as $user)
			{
				$users[] = array($i,(string)$user->username,(string)$user->type);
				$i++;
			}
			return $users;
		}
	}
	public function update($id,$username,$password,$type)
	{
		$id = (int)$id;
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])	// should we use SQL?
		{
			if (empty($password))
			{
				$sql = "
					update
						`".account::$users."`
					set
						`username` = '".mysql::escape($username)."',
						`type`	   = ".(int) $type."
					where
						`userId` = ".(int) $id."
				";
			}
			else
			{
				$sql = "
					update
						`".account::$users."`
					set
						`username` = '".mysql::escape($username)."',
						`password` = '".md5(sha1($password))."',
						`type`	   = ".(int) $type."
					where
						`userId` = ".(int) $id."
				";
			}

			$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],account::userTable(),$sql);
		
			if (!isset($query[0][0]))
			{
				return true;
			}
		}
		else
		{
			$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
			
			// have to find ID
			if ($id < 0)
			{
				$i = 0;
				foreach ($cfg->users as $user)
				{
					if ($username == (string)$user->user->username)
					{
						$id = $i;
						break;
					}
					$i++;
				}
			}
			
			if ($id < 0)
			{
				return;
			}
			
			$cfg->users->user[$id]->username 	= $username;
			if (!empty($password))
			{
				$cfg->users->user[$id]->password 	= md5(sha1($password));
			}	
			$cfg->users->user[$id]->type 	= $type;
			//$cfg->users->user[$id]->path 	= $path;
			if (file_put_contents($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg'],$cfg->asXML()))
			{
				$_SESSION[$_SERVER['REMOTE_ADDR']]['cfg'] = misc::xml2array($cfg);
				return true;
			}
		}
		return false;
	}
	
	
	
	public function delete($userId)
	{
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])	// should we use SQL?
		{
			$user = account::getUserByUserId($userId);
			$path = $user[4];
			
			$sql = "
				delete from
					`".account::$users."`
				where
					`userId` = ".(int) $id."
				";

			$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],account::userTable(),$sql);
		
			if (!isset($query[0][0]))
			{
				if (misc::rmrf($path))
				{
					return true;
				}
			}
		}
		else
		{
			$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
			$id = (int) $userId;
			unset($cfg->users->user[$id]);
			if (file_put_contents($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg'],$cfg->asXML()))
			{
				$_SESSION[$_SERVER['REMOTE_ADDR']]['cfg'] = misc::xml2array($cfg);
				return true;
			}
		}
		return false;
	}
	
	
	
	public function setup($argCfg,$argUser)
	{
		// set program path's
		w3btorrent::programs($argCfg);
			
		// do we need setup ?
		if ($setup = w3btorrent::setup($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'],$argCfg))
		{
			// download user is logging in when w3btorrent needs to be setup, not allowed to login
			if ($argUser != (string)$argCfg->admin->username)
			{
				w3btorrent::write2log("account","Unable to use regular user to login, w3btorrent need's to be setup.");
				unset($_SESSION[$_SERVER['REMOTE_ADDR']]['account'],$_SESSION[$_SERVER['REMOTE_ADDR']]['type']);
				return true;
			}
			else
			{
				$_SESSION[$_SERVER['REMOTE_ADDR']]['setup'] = $setup;
			}
		}
	}
	
	
	public function logout()
	{
		w3btorrent::write2log("account",$_SESSION[$_SERVER['REMOTE_ADDR']]['account']." logout.");
		unset($_SESSION[$_SERVER['REMOTE_ADDR']]['account'],$_SESSION[$_SERVER['REMOTE_ADDR']]['page'],$_SESSION[$_SERVER['REMOTE_ADDR']]['setup'],$_SESSION[$_SERVER['REMOTE_ADDR']]['admin'],$_SESSION[$_SERVER['REMOTE_ADDR']]['path']);
		header("location: ./");
		exit();
	}
}

?>
