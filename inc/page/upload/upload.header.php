<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	return;
}

require_once("inc/class/Lightbenc.class.php");
require_once("inc/class/rtorrent.class.php");
require_once("inc/class/w3btorrent.class.php");


if (isset($_FILES['upload']))
{
	if ($_FILES['upload'] < 1)
	{
		$status['upload'] = "Error: invalid file.";
	}
	elseif (!$content = file_get_contents($_FILES['upload']['tmp_name']))
	{
		$status['upload'] = "Error: unable to download torrent.";
	}
	elseif (!$meta = Lightbenc::bdecode($content))
	{
		$status['upload'] = "Error: unable to parse file, not a torrent file?";
	}
	elseif (!isset($meta['info']['name']))
	{
		$status['upload'] = "Error: invalid torrent file.";
	}
	else
	{
		$file = $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].urlencode($meta['info']['name']).".torrent";
		$saveDir = $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].str_replace(array("+","%20"),array(" "," "),urlencode($meta['info']['name']))."/";
	}
	
	if (!isset($status['upload']))
	{
		file_put_contents($file,$content);
		if (!rtorrent::load($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$file))
		{
			$status['upload'] = "Error: unable to load '".$file."' in rtorrent.";
			w3btorrent::write2log("upload","Unable to load '".$file."' in rtorrent."); 
		}
		else
		{
			$hash = rtorrent::getHashByFile($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$file);
			@mkdir($saveDir);
			rtorrent::setSaveDir($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$saveDir,$hash);
			w3btorrent::write2log("upload","Uploaded '".$file."'.");
			$status['upload'] = 'Torrent uploaded.';
		}
	}
}

















?>