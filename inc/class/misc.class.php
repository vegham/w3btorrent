<?php

/*
PHP class with a collection of usefull functions
Copyright (C) 2005, 2008  Vegard Hammerseth <vegard@hammerseth.com> (http://vegard.hammerseth.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

v1.0.4
*/


class misc
{

	// calculate byte into human format
	public function byteChange($argByte,$argDecimals = "2",$argType = "")
	{
		if (!is_numeric($argByte.$argDecimals))
		{
			return $argByte;
		}
		
		$units = array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
		
		for ($x = count($units)-1;$x >= 0;$x--)
		{
			$nr = pow(1024,$x);
			if (abs($argByte) >= $nr || strtoupper($argType) == $units[$x])
			{
				$x--;
				break;
			}
		}
		$x++;

		$return = @number_format($argByte/$nr,$argDecimals);
		//$return = sprintf('%01.'.$argDecimals.'f',$return);
		
		// we already know unit so we don't need it
		if (empty($argType) && $x >= 0)
		{
			$return .= " ".$units[$x];
		}
		
		return $return;
	} // 1.0.5

	

	// show time left in a human format v1.0.3
	public function humanTimeLeft($argTime,$argDetail = 0,$argLength = "short",$argLastUnit = "sec")	// number of details
	{
		$return = "";
		$detail = 0;
		$length = ($argLength == "long"?$argLength:"short");
		$time   = $argTime;
		$names['long']  = array("year","day","hour","minute","second");
		$names['short'] = array("yr","day","hr","min","sec");
		$times = array(31536000,86400,3600,60,1);
		
		// loop through all times
		for ($x=0;$x<count($times);$x++)
		{
			$word = $names[$length][$x];
			$out=0;
			
			// find out what time
			while ($time >= $times[$x])
			{
				$time -= $times[$x];
				$out++;
			}
			
			if ($out > 0)
			{
				$return .= misc::numberWord($out,$word,$word."s").", ";
				$detail++;
			}

			// should we stop unit here?
			if ($argLastUnit == $word || $detail == $argDetail)
			{
				break;
			}
		}
		return substr($return,0,-2);
	}



 	// function to print "1 hour" and not "1 hours"
	public function numberWord($argReturn,$argWord,$argWords)
	{
		if ($argReturn == "1") {
			$argReturn .= " ".$argWord;
		}
		else
		{
			$argReturn .= " ".$argWords;
		}
		
		return $argReturn;
	} // 1.0.0



	// add '...' after words being to long
	public function subWord($argString,$argLength)
	{
		if (strlen($argString) <= $argLength+3)
		{
			return $argString;
		}

		return substr($argString,0,$argLength)."...";
	} // 1.0.0



	// remove a file or directory v1.0.4
	public function rmrf($argPath)
	{
		// only remove symoblic link if our path is a symbolic link
		if (is_link($argPath))
		{
			unlink($argPath);
			return true;
		}

		// file don't exists
		if (!file_exists($argPath))
		{
			return;
		}

		@chmod($argPath,0777); // just in case
		// we got a file
		if (is_file($argPath))
		{
			if (@unlink($argPath))
			{
				return true;
			}
			return;
		}
		else if (!$dopen = opendir($argPath))	// it's a path
		{
			return;
		}
		
		// loop through all files in $argPath
		while ($f = readdir($dopen))
		{
			if ($f == "." || $f == "..")
			{
				continue;
			}
			
			if (is_dir("$argPath/$f"))
			{
				// run function again, since we got a dir
				misc::rmrf("$argPath/$f");
			}
			else
			{
				@unlink("$argPath/$f");
			}
		}
		if (is_resource($dopen))	// has no effect, apparently
		{
			@fclose($dopen);	// this can give errors unless @ is appended
		}
		
		// finally, remove desired path
		if (@rmdir($argPath))
		{
			return true;
		}
	}



	// find executable path for program
	public function findExecPath($argCmd)
	{
   		$paths = explode(":",$_SERVER['PATH']);
		$paths = array_merge($paths,array("/usr/bin","/bin","/usr/local/bin","/usr/sbin","/sbin"));
		$paths = array_unique($paths);

		// find and execute path
		foreach ($paths as $path)
		{
			if (is_executable($path."/".$argCmd))
			{
				return realpath($path."/".$argCmd);
			}
		}
	} // 1.0.1




	// remove an file extention, if any
	public function removeExt($argString,$argExt = "")
	{
		if (empty($argExt))
		{
			return substr($argString,0,-strlen(strrchr($argString,".")));
		}
		elseif (substr($argString,-strlen(".$argExt")) == ".$argExt")
		{
			return substr($argString,0,-strlen(".$argExt"));
		}
		else
		{
			return $argString;
		}
	} // 1.0.0



	/* valid url */
	public function validUrl($argUrl)
	{
		if (strlen($argUrl) > 10 && preg_match("/(http(s?)|ftp):\/\//i",$argUrl) && @fopen($argUrl,"r"))
		{
			return true;
		}
	} // 1.0.1


	// is path in web tree?
	public function inWebTree($argPath)
	{
		$path = realpath($argPath);
		if (!file_exists($path))
		{
			return false;
		}

		// check root
		if ($_SERVER['DOCUMENT_ROOT'] == substr($path,0,strlen($_SERVER['DOCUMENT_ROOT'])))
		{
			return true;
		}
	} // 1.0.0
	
	
	/* check if a file is in use */
	public function inUse($argPath)
	{
		if (file_exists($argPath) && filemtime($argPath) > time()-2)
		{
			return true;
		}
	} // 1.0.0
	
	
	/* does client use WAP protocol? */
	public function isWap()
	{
		if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || preg_match("/(MIDP|MMP|WAP|MOBILE|SYMBIAN)/i",$_SERVER['HTTP_USER_AGENT']))
		{
			return true;
		}
	} // 1.0.0
	
	
	/* print chunk's to prevent memory problems */
	public function readFileChunk($argFile,$argChunkSize=1024)
	{
		if (!is_file($argFile) || !is_readable($argFile))
		{
			return;
		}
		$fd = fopen($argFile,"r");
		while (!feof($fd))
		{
			echo fread($fd,4096);
			flush();
		}
		fclose($fd);
	} // 1.0.0
	
	
	
	public function createSubArray($argArray)
	{
		$key = array_shift($argArray);
		if (count($argArray) > 0)
		{
			return array($key=>misc::createSubArray($argArray));
		}
		return array($key=>"");
	} // 1.0.0
	
	
	/* be able to sort by a selected subarray's value, very nice for sorting out list's */
	public function subArrayValueSort($argArray,$argSubArrayIndex = 0)
	{
		$return = array();
		foreach ($argArray as $subArray)
		{			
			$c = 0;
			unset($added);
			foreach ($return as $subArray2)
			{
				/* figure out witch is "bigger" */
				$sort = array(strtolower($subArray[$argSubArrayIndex]),strtolower($subArray2[$argSubArrayIndex]));
				arsort($sort);
				
				if (array_pop($sort) == $subArray[$argSubArrayIndex])
				{
					$added  = 1;
					$return = array_merge(array_slice($return,0,$c),array($subArray),array_slice($return,$c,count($return)+1));
					break;
				}
				$c++;
			}
			
			if (!isset($added))
			{
				$return[] = $subArray;
			}
		}
		return $return;
	}
	
	
	/* returns in what oreder you want something from $_GET */
	public function order($argGetId,$argExpectedValues = array())
	{
		$return = join(array_slice($argExpectedValues,0,1));
		if (isset($_GET[$argGetId]) && in_array($_GET[$argGetId],$argExpectedValues))
		{
			$return = $_GET[$argGetId];
		}
		return $return;
	}
	
	
	// get random string by length and offset v1.0.2
	function randomString($LENGTH=8,$OFFSET = "")
	{
		$return = $OFFSET;
		while (strlen($return) < $LENGTH)
		{
			$a 	 = array(mt_rand(48,57),mt_rand(65,90),mt_rand(97,122));
			$return .= chr($a[mt_rand(0,2)]);
		}
		return $return;
	}


	// format URL v1.0.2
	public function url($argOption = 0)
	{
		$url = "http://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']!=80?":".$_SERVER['SERVER_PORT']:"").$_SERVER['REQUEST_URI'];
		
		if ($argOption == 1)
		{
			$url = "http://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']!=80?":".$_SERVER['SERVER_PORT']:"")."/";
		}
		elseif ($argOption == 2)
		{
			$url = $_SERVER['REQUEST_URI'];
		}
		elseif ($argOption == 3 && substr($url,-1) != "/")
		{
			$url = dirname($url)."/";
		}
		
		return $url;
	}
	
	// get uptime on unix system v1.0.1
	public function uptime()
	{
		$file = "/proc/uptime";
		if (!is_file($file))
		{
			return;
		}
		$uptime = explode(" ",file_get_contents($file));
		
		if (is_numeric($uptime[0]))
		{
			return $uptime[0];
		}
	}

	// valid email v1.0.1
	function validEmail($argEmail,$domainCheck = true)
	{
		if (!preg_match("/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/",$argEmail))
		{
			return;
		}
		list($user,$domain) = explode("@",$argEmail);
		if (!$domainCheck || ($domainCheck && checkdnsrr($domain,"MX")))
		{
			return true;
		}
	}


	// clean ugly filenames v1.0.0
	function cleanFilename($argName)
	{
		return str_replace("//","/",$argName);
	}

	// clean strings v1.0.1
	public function cleanString($argString,$argCleaner = "")
	{
		$garbage = array("@",",",".","(",")","[","]","{","}");
		$result = str_replace($garbage,array_fill(0,count($garbage),$argCleaner),$argString);
		while (strpos($result,$argCleaner.$argCleaner) !== false)
		{
			$result = str_replace($argCleaner.$argCleaner,$argCleaner,$result);
		}
		return trim($result);
	}
	public function xml2array($xml)
	{
        $array = json_decode(json_encode($xml), TRUE);
        
        foreach ( array_slice($array, 0) as $key => $value ) {
            if ( empty($value) ) $array[$key] = NULL;
            elseif ( is_array($value) ) $array[$key] = misc::xml2array($value);
        }

        return $array;
	}
}

?>
