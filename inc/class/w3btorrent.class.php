<?php

/*

*/



class w3btorrent
{
	private static $options = "w3btorrent_options";
	public function options()
	{
		return w3btorrent::$options;
	}
	public function optionsTable()
	{
		$sql = "
			create table
				`".w3btorrent::$options."`
			(
				`name` varchar(255),
				`value` varchar(255)
			)";
		return $sql;
	}
	
	//
	// DDIR
	//
	public function getDdir()
	{
		// possible ddirs
		$dirs = array();
		
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])// get saved ddir
		{
			$dirs[] = w3btorrent::getDdirFromDb();
		}
		elseif (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['cfg']['path']['dDir']))
		{
			$dirs[] = $_SESSION[$_SERVER['REMOTE_ADDR']]['cfg']['path']['dDir'];
		}
		
		if (isset($CONFIG['dDir']))
		{
			$dirs[] = $CONFIG['dDir'];
		}
		
		// guessing dirs
		$dirs[] = "downloads/";
		$dirs[] = "/tmp/w3btorrent/";
		foreach ($dirs as $dir)
		{
			if (!is_dir($dir))	// try to create the directory
			{
				@mkdir($dir);
			}
			$dir = realpath($dir)."/";
			if (w3btorrent::validDdir($dir))
			{
				return $dir;
			}
		}
		return;
	}
	
	public function getDdirFromDb()
	{
		$sql = "
			select
				`value`
			from
				`".w3btorrent::$options."`
			where
				`name` = 'dDir'
			";
		
		$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],w3btorrent::optionsTable(),$sql);
		if (isset($query[0][0]))
		{
			return $query[0][0];
		}
		return;
	}
	
	//
	// USER DDIR
	public function getUserDdir()
	{
		// possible dirs
		$dirs = array();

		// get saved ddir
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])
		{
			$dirs[] = w3btorrent::getUserDdirFromDb();
		}
		elseif (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['cfg']['path']['userDdir']))
		{
			$dirs[] = $_SESSION[$_SERVER['REMOTE_ADDR']]['cfg']['path']['userDdir'];
		}
	
		if (isset($CONFIG['userDdir']))
		{
			$dirs[] = $CONFIG['userDdir'];
		}
		
		// guessing dirs
		$dirs[] = $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']."w3btorrent_users/";
		$dirs[] = $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir']."users/";
		$dirs[] = "/tmp/w3btorrent/w3btorrent_users/";
		foreach ($dirs as $dir)
		{
			if (!is_dir($dir))	// try to create the directory so we have something to start at
			{
				@mkdir($dir);
			}
			$dir = realpath($dir)."/";	
			if (w3btorrent::validDdir($dir))
			{
				return $dir;
			}
		}
		return;
	}
	
	public function getUserDdirFromDb()
	{
		$sql = "
			select
				`value`
			from
				`".w3btorrent::$options."`
			where
				`name` = 'userDdir'
			";
		
		$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],w3btorrent::optionsTable(),$sql);
		if (isset($query[0][0]))
		{
			return $query[0][0];
		}
		return;
	}
	
	//
	//	RPC
	//
	public function getRpc()
	{
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])
		{
			$rpc = w3btorrent::getRpcFromDb();
		}
		else if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['cfg']['rtorrent']['rpc']))
		{
			$rpc = $_SESSION[$_SERVER['REMOTE_ADDR']]['cfg']['rtorrent']['rpc'];
		}
		
		// do a good first guess
		if (empty($rpc))
		{
			$rpc = "http://localhost/RPC2";
		}
		
		return $rpc;
	}
	public function getRpcFromDb()
	{
		$sql = "
			select
				`value`
			from
				`".w3btorrent::$options."`
			where
				`name` = 'rpc'
			";
		
		$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],w3btorrent::optionsTable(),$sql);
		if (isset($query[0][0]))
		{
			return $query[0][0];
		}
		
		return;
	}
	public function getScgi()
	{
		if ($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['enabled'])
		{
			$rpc = w3btorrent::getScgiFromDb();
		}
		else
		{
			$rpc = $_SESSION[$_SERVER['REMOTE_ADDR']]['cfg']['rtorrent']['scgi'];
		}
		
		// do a good first guess
		if (empty($rpc))
		{
			$rpc = "localhost:5000";
		}
		
		return $rpc;
	}
	public function getScgiFromDb()
	{
		$sql = "
			select
				`value`
			from
				`".w3btorrent::$options."`
			where
				`name` = 'scgi'
			";
		
		$query = mysql::query($_SESSION[$_SERVER['REMOTE_ADDR']]['mysql']['db'],w3btorrent::optionsTable(),$sql);
		if (isset($query[0][0]))
		{
			return $query[0][0];
		}
		
		return;
	}
	
	
	
	public function init()
	{
		// find execution paths
		$BINS = array
		(
			"ps"		=>"",			
			"crontab"	=>"",
			"zip"	=>"",
			"rar"	=>"",
			"unrar"	=>"",
			"rtorrent"=>"",
			"screen"	=>""
		);
		$DOWNLOADERS = array
		(
			"wget"	=>"-q -T 5 -O /dev/null",
			"GET"	=>"",
			"curL"	=>"",
		);
		// find 
		foreach ($BINS as $bin=>$argument)
		{
			if ($path = misc::findExecPath($bin))
			{
				$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin'][$bin] = $path." ".$argument;
			}
		}
		// setup downloader
		foreach ($DOWNLOADERS as $bin=>$argument)
		{
			if ($path = misc::findExecPath($bin))
			{
				$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['downloader'] = $path." ".$argument;
				break;
			}
		}
		
		// check if we can install crontab
		if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['crontab'],$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['downloader']))
		{
			return false;
		}
		
		// install crontab
		if (!w3btorrent::crontabIsInstalled($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['crontab']))
		{
			if (!w3btorrent::installCrontab($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['crontab'],$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['bin']['downloader']))
			{
				return false;
			}
		}
		
		return true;
	}
	
	public function internalFile($argFile)
	{
		if (basename($argFile) == "index.php")
		{
			return true;
		}
		if (substr($argFile,-5) == ".meta")
		{
			return true;
		}
		if (substr($argFile,-8) == ".torrent")
		{
			return true;
		}
		if ($argFile == $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].".rtorrent.rc")
		{			
			return true;
		}
	}

	public function validDdir($argDir)
	{
		if (is_dir($argDir) && is_readable($argDir) && is_writeable($argDir) && is_executable($argDir))
		{
			return true;
		}
	}
	
	public function crontabIsInstalled($crontabBin)
	{
		$url = misc::url(1);
		
		// execute crontab query
		$exec = shell_exec($crontabBin.' -l');
		
		// for each crontab line
		foreach (explode("\n",$exec) as $line)
		{
			// look for $url in crontab
			if (strpos($line,$url) > -1 && strpos($line,"cron.php") > -1)
			{
				return true; 
			}
		}
	}
	public function installCrontab($crontabBin,$downloaderBin)
	{
		$url = misc::url(3)."cron.php";
		
		$file = "/tmp/w3btorrentCrontab";
		$content = "0,5,10,15,20,25,30,35,40,45,50,55 * * * * ".$downloaderBin." ".$url."\n";
		if (!file_put_contents($file,$content))
		{
			return false;
		}
		
		$result = shell_exec($crontabBin.' '.escapeshellarg($file));
		unlink($file);
		
		if (empty($result))
		{
			return true;
		}
	}
	
	public function write2log($section,$text)
	{
		// don't do jack
	}
	
}

?>
