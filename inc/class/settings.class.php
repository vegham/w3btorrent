<?php

/*

*/



class settings
{
	// set new ddir
	public function setDdir($dDir)
	{
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])	// use SQL
		{
			$currenDdir = w3btorrent::getDdirFromDb();
			
			if (empty($currenDdir))
			{
				$sql = "
					insert into
						`".w3btorrent::options()."`
					values
					(
						'dDir',
						'".mysql::escape($dDir)."'
					)
				";
				$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],w3btorrent::optionsTable(),$sql);
				if (empty($query))
				{
					return true;
				}
			}
			else
			{
				$sql = "
					update
						`".w3btorrent::options()."`
					set
						`value` = '".mysql::escape($dDir)."'
					where
						`name` = 'dDir'
					";
			
				$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],w3btorrent::optionsTable(),$sql);
				if (empty($query))
				{
					return true;
				}
			}
		}
		else
		{
			$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
			(string)$cfg->path->dDir = $dDir;
			if (file_put_contents($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg'],$cfg->asXML()))
			{
				$_SESSION[$_SERVER['REMOTE_ADDR']]['cfg'] = misc::xml2array($cfg);
				return true;
			}
		}
		return false;
	}
	
	// set new userdir
	public function setUserDdir($userDdir)
	{
		$currenDdir = w3btorrent::getUserDdirFromDb();
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])	// use SQL
		{
			if (empty($currenDdir))
			{
				$sql = "
					insert into
						`".w3btorrent::options()."`
					values
					(
						'userDdir',
						'".mysql::escape($userDdir)."'
					)
				";
				$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],w3btorrent::optionsTable(),$sql);
				if (empty($query))
				{
					return true;
				}
			}
			else
			{
				$sql = "
					update
						`".w3btorrent::options()."`
					set
						`value` = '".mysql::escape($userDdir)."'
					where
						`name` = 'userDdir'
					";
			
				$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],w3btorrent::optionsTable(),$sql);
				if (empty($query))
				{
					return true;
				}
			}
		}
		else
		{
			$cfg = simplexml_load_file($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg']);
			(string)$cfg->path->userDdir = $userDdir;
			if (file_put_contents($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['cfg'],$cfg->asXML()))
			{
				$_SESSION[$_SERVER['REMOTE_ADDR']]['cfg'] = misc::xml2array($cfg);
				return true;
			}
		}
		return false;
	}
	
	
	public function setRpc($rpc)
	{
		$currenRpc = w3btorrent::getRpcFromDb();
		
		if (empty($currenRpc))
		{
			$sql = "
				insert into
					`".w3btorrent::options()."`
				values
				(
					'rpc',
					'".mysql::escape($rpc)."'
				)
			";
			$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],w3btorrent::optionsTable(),$sql);
			if (empty($query))
			{
				return true;
			}
		}
		else
		{
			$sql = "
				update
					`".w3btorrent::options()."`
				set
					`value` = '".mysql::escape($rpc)."'
				where
					`name` = 'rpc'
				";
		
			$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],w3btorrent::optionsTable(),$sql);
			if (empty($query))
			{
				return true;
			}
		}
		return false;
	}
}

?>
