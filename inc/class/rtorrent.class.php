<?php

/*
PHP class to handle torrent functions for w3btorrent
Copyright (C) 2005, 2007  Vegard Hammerseth <vegard@hammerseth.com> (http://vegard.hammerseth.com)

This file is part of w3btorrent.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

v1.0.0
*/

class rtorrent
{
	public function xmlrpc($address,$request,$timeout = 5)
	{
		$context = stream_context_create
		(
			array
			(
				"http" => array
				(
					"timeout"=>$timeout,
					"method" =>"POST",
					"header" =>"Content-Type: text/xml",
					"content"=>$request
				)
			)
		);
		
		if ($file = @file_get_contents($address, false, $context))
		{
			$file = str_replace("i8","double",$file);
	      	$file = utf8_encode($file); 
	      	return xmlrpc_decode($file);
		}
		else
		{
			return false;
	   	}
	}

	public function set($address,$key,$value)
	{
		return rtorrent::xmlrpc($address,xmlrpc_encode_request($key,array($value)));
	}
	public function get($address,$setting,$option = "")
	{
		return rtorrent::xmlrpc($address,xmlrpc_encode_request($setting,array($option)));		
	}

	//
	//	EXECUTE RTORRENT STARTING
	//
	public function execute($argScreen,$argRtorrent,$argDir = "")
	{
		if (!is_executable($argScreen) || !is_executable($argRtorrent))
		{
			//return; // there seems to be some sort of bug here, so we'll let it pass and assume it's working
		}
		
		// let's be able to start even tho a download directory has not been provided
		if (empty($argDir) || !is_dir($argDir) || !is_writable($argDir) || !is_readable($argDir))
		{
			$exec = trim(shell_exec($argScreen." -d -m ".$argRtorrent." & echo \$!"));
		}
		else
		{
			$exec = trim(shell_exec("export HOME=".escapeshellarg($argDir).";$argScreen -d -m ".$argRtorrent." -d ".escapeshellarg($argDir)." & echo \$!"));
		}

		if (is_numeric($exec))
		{
			$pid = $exec-1;
			for ($x=0;$x<10;$x++)	// find correct PID
			{
				$pid++;
				if (posix_getsid($pid) && posix_getpgid(posix_getpid()) == posix_getpgid($pid))
				{
					return $pid;
					break;
				}
			}
		}
	}
	
	//
	//	FUNCTION TO FIND 
	//
	public function getPid($address,$psBin,$rtorrentBin)	// this and
	{
		if (empty($address))
		{
			$pid = rtorrent::xmlrpc($address,xmlrpc_encode_request("system.pid",array("")),1);	
			if (is_numeric($pid) && $pid > 0)
			{
				return $pid;
			}
		}
		
		
		// use shell commands
		if (empty($psBin) || empty($rtorrentBin))		// this is not good enough, we can assume $psBin = /bin/ps ... fix it! ffs
		{
			return;
		}
		
		// check if rtorrent is running
		$result = shell_exec($psBin." x"); // ps x
		foreach (explode("\n",$result) as $line)
		{
			if (strpos($line,$rtorrentBin) > -1)
			{
				$pid = explode(" ",trim($line));
				$pid = $pid[0];
				return trim($pid);
			}
		}
	}
	
	public function load($address,$argPath)
	{
		$result = rtorrent::xmlrpc($address,xmlrpc_encode_request("load",array($argPath)));
		return (empty($result)?true:$result);
	}
	public function loadStart($address,$argPath)
	{
		return rtorrent::xmlrpc($address,xmlrpc_encode_request("load_start",array($argPath)));
	}
	public function setSaveDir($address,$argPath,$hash)
	{
		return rtorrent::xmlrpc($address,xmlrpc_encode_request("d.set_directory",array($hash,$argPath)));
	}
	public function getHashByFile($address,$argFile,$view = "main")
	{
		foreach (rtorrent::xmlrpc($address,xmlrpc_encode_request("d.multicall",array($view,"d.get_tied_to_file=","d.get_hash="))) as $result)
		{
			if ($result[0] == $argFile || $result[0] == "/".$argFile)
			{
				return $result[1];
			}
		}
	}


	public function getHashByFiles($address,$argFiles = array(),$view = "main")
	{
		$torrents = array();
		foreach ($argFiles as $torrent)
		{
			$torrent = misc::cleanFilename($torrent);
			$torrents[$torrent] = "";
		}
		foreach (rtorrent::xmlrpc($address,xmlrpc_encode_request("d.multicall",array($view,"d.get_tied_to_file=","d.get_hash="))) as $result)
		{
			if (isset($torrents[$result[0]]))
			{
				$torrents[$result[0]] = $result[1];
			}
			else if (isset($torrents[misc::cleanFilename($result[0])]))	// some stupid mistake done by rtorrent
			{
				unset($torrents[misc::cleanFilename($result[0])]);
				$torrents[$result[0]] = $result[1];
			}
		}
		return $torrents;
	}

	
	public function start($address,$hash)
	{
		rtorrent::xmlrpc($address,xmlrpc_encode_request("d.start",array("$hash")));
	}
	public function startAll($addressm,$view = "main")
	{
		rtorrent::xmlrpc($address,xmlrpc_encode_request("d.multicall",array($view,"d.start=")));
	}
	public function startByTorrentFileArray($address,$argFiles)
	{
		foreach (rtorrent::getHashByFiles($address,$argFiles) as $file=>$hash)
		{
			if (substr($file,-5) == ".meta")
			{
				rtorrent::setSaveDir($address,misc::removeExt(misc::removeExt($file,"torrent"),"meta"),$hash);
			}
			rtorrent::start($address,$hash);
			
		}
	}


	public function stop($address,$hash)
	{
		rtorrent::xmlrpc($address,xmlrpc_encode_request("d.stop",array("$hash")));	//d.stop can be used
	}
	public function stopAll($address,$view = "main")
	{
		rtorrent::xmlrpc($address,xmlrpc_encode_request("d.multicall",array($view,"d.stop=")));
	}
	public function stopByTorrentFileArray($address,$argFiles)
	{
		foreach (rtorrent::getHashByFiles($address,$argFiles) as $file=>$hash)
		{
			rtorrent::stop($address,$hash);
		}
	}


	public function delete($address,$hash)
	{
		$response = rtorrent::xmlrpc($address,xmlrpc_encode_request("d.erase",array("$hash")));
	}
	public function deleteByTorrentFileArray($address,$argFiles)
	{
		foreach (rtorrent::getHashByFiles($address,$argFiles) as $file=>$hash)
		{
			rtorrent::delete($address,$hash);
		}
	}

	public function status($address,$view = "main")
	{
		$torrents = array();
		$info = array
		(
			"d.get_base_filename=",
			"d.get_bytes_done=",
			"d.get_complete=",
			"d.get_completed_bytes=",
			"d.get_directory=",
			"d.get_size_bytes=",
			"d.get_down_rate=",
			"d.get_down_total=",
			"d.get_free_diskspace=",
			"d.get_hash=",
			"d.get_left_bytes=",
			"d.get_message=",
			"d.get_name=",
			"d.get_ratio=",
			"d.get_state=",
			"d.get_state_changed=",
			"d.get_tied_to_file=",
			"d.get_up_rate=",
			"d.get_up_total=",
			"d.get_peers_complete=",
			"d.get_peers_connected=",
			"d.get_peers_not_connected=",
			"d.is_open=",
			"d.is_active=",
			"d.get_hashing=",
			"d.get_size_chunks=",
			"d.get_chunks_hashed=",
			"d.get_connection_current="
			
		);
		
		$request = xmlrpc_encode_request("d.multicall",array_merge(array($view),$info));
		$response = rtorrent::xmlrpc($address,$request);
		
		$return = array();
		if (is_array($response) && !isset($response['faultCode']))
		{
			$i = 0;
			foreach($response as $item)
			{
				$j = 0;
				foreach ($info as $value)
				{
					$tmp = explode("=",$value);
					$tmp = explode(".",$tmp[0]);
					$key = implode(".",array_slice($tmp,1));
					if (substr($key,0,4) == "get_")
					{
						$key = substr($key,4);
					}
					$return[$i][$key] = $item[$j];
					$j++;
				}
				
				if ($return[$i]['is_active'] == 0)
				{
					$return[$i]['status'] = "Stopped";
				}
				if ($return[$i]['complete'] == 1)
				{
					$return[$i]['status']="Complete";
				}
				if ($return[$i]['is_active'] == 1 && $return[$i]['connection_current'] == "leech")
				{
					$return[$i]['status'] = "Leeching";
				}
				if ($return[$i]['is_active'] == 1 && $return[$i]['complete'] == 1)
				{
					$return[$i]['status'] = "Seeding";
				}
				if ($return[$i]['hashing']>0)
				{
				   $return[$i]['status']	= "Hashing";
				   $return[$i]['percent']	= @round(($return[$i]['chunks_hashed'])/($return[$i]['size_chunks'])*100);
				}
				else
				{			
					$return[$i]['percent'] 		= @floor(($return[$i]['completed_bytes'])/($return[$i]['size_bytes'])*100);
				}
				$return[$i]['down_left'] 	= ($return[$i]['size_bytes']-$return[$i]['completed_bytes']);			
				
				// ETA
				$return[$i]['eta'] = "";
				if ($return[$i]['percent'] < 100 && $return[$i]['is_active'] == 1)
				{
					$secLeft	= ($return[$i]['down_rate']<1?$return[$i]['down_left']:($return[$i]['down_left']/$return[$i]['down_rate']));
					$return[$i]['eta'] = misc::humanTimeLeft($secLeft,2);
				}
				
				
				// change to bytes
				foreach (array("size_bytes","down_total","up_total") as $key)
				{
					$return[$i][$key] = misc::byteChange($return[$i][$key]);  
				}
				foreach (array("down_rate","up_rate") as $key)
				{
					$return[$i][$key] = misc::byteChange($return[$i][$key])."/s"; 
				}
				$i++;
			}
		}
	
		return $return;		
	}

	public function getSettings($address)
	{
		$request = xmlrpc_encode_request
		(
			"d.multicall",
			array
			(
				"main",
				"get_directory="
			)
		);

		if (!$response = rtorrent::xmlrpc($address,$request))
		{
			return array();
		}
		
		$keys = array
		(
			
		);
		
		$index = 0;
		foreach($response AS $key=>$item)
		{
			print_r($key);
			print_r($item);
		}
	}
}

?>
