<?php

/*
PHP class to get all dir/file's in a directory with info about the file/dir's
Copyright (C) 2006, 2008, 2012  Vegard Hammerseth <vegard@hammerseth.com> (http://vegard.hammerseth.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

v1.0.4 on Tue Jan  3 08:35:44 CET 2012
- fixed Warning: filesize(): stat failed for symbolic links in hdc.class.php on line 71
- added listLinks()

v1.0.3
- fixed Warning: @fclose() [function.@fclose]: 12 is not a valid stream resource

v1.0.2
*/



class hdc // handle directory contents
{
	// get all directorys in a directory
	public function listDirs($argDir)
	{
		$dirs = array();
		if (!is_dir($argDir) || !$dopen = @opendir($argDir))
		{
			return $dirs;
		}
		
		while ($f = readdir($dopen))
		{
			if ($f == "." || $f == ".." || !is_dir($argDir."/$f"))
			{
				continue;
			}
			$dirs[] = $f;
		}
		if (is_resource($dopen))
		{
			@fclose($dopen);
		}
		
		return $dirs;
	}



	// get real dir size, including all files v1.0.1
	public function realDirSize($argDir)
	{
		if (!is_dir($argDir) || !$dopen = opendir($argDir))
		{
			return;
		}
		
		$s = 0;
		while ($f = readdir($dopen))
		{
			if ($f == "." || $f == "..")
			{
				continue;
			}
			$f = $argDir."/$f";
			if (is_dir($f) || is_link($f))
			{
				$s += hdc::realDirSize($f);
			}
			else if (is_file($f))
			{
				$s += filesize($f);
			}
		}

		return $s;
	}


	
	// get all files in a directory
	public function listFiles($argDir)
	{
		$files = array();
		if (!is_dir($argDir) || !$dopen = @opendir($argDir))
		{
			return $files;
		}
		
		while ($f = readdir($dopen))
		{
			if ($f == "." || $f == ".." || !is_file($argDir."/$f"))
			{
				continue;
			}
			$files[] = $f;
		}
		if (is_resource($dopen))
		{
			@fclose($dopen);
		}
		
		return $files;
	}


	// get all symbolic links in a directory
	public function listLinks($argDir)
	{
		$links = array();
		if (!is_dir($argDir) || !$dopen = @opendir($argDir))
		{
			return $links;
		}
		
		while ($f = readdir($dopen))
		{
			if ($f == "." || $f == ".." || !is_link($argDir."/$f"))
			{
				continue;
			}
			$links[] = $f;
		}
		if (is_resource($dopen))
		{
			@fclose($dopen);
		}
		
		return $links;
	}
	
	
	// get all files in a directory recursively
	public function listFilesRecursive($argDir,$argPath = "")
	{
		if ($argPath == "")
		{
			$argPath = $argDir."/";
		}
		$files = array();
		if (!is_dir($argDir) || !$dopen = opendir($argDir))
		{
			return $files;
		}
		
		while ($f = readdir($dopen))
		{
			if ($f == "." || $f == "..")
			{
				continue;
			}
			elseif (is_dir("$argDir/$f"))
			{
				$files = array_merge($files,hdc::listFilesRecursive($argDir."/$f","$argDir/$f/"));
			}
			else
			{
				$files[] = realpath($argPath.$f);
			}
		}
		if (is_resource($dopen))
		{
			@fclose($dopen);
		}
		return $files;
	}
	
	
	public function getBiggestDirFile($argDir)
	{
		$return = array("",0);
		foreach (hdc::listFilesRecursive($argDir) as $file)
		{
			$size = filesize($file);
			if ($size > $return[1])
			{
				$return = array($file,$size);
			}
		}
		
		return $return[0];
	}
	
}

?>
