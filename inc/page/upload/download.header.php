<?php

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	return;
}

require_once("inc/class/Lightbenc.class.php");
require_once("inc/class/rtorrent.class.php");
require_once("inc/class/w3btorrent.class.php");

if (isset($_POST['downloadLink']))
{
	if (!preg_match("#^(http|ftp|magnet):+#",$_POST['downloadLink']))
	{
		$status['download'] = "Error: invalid link provided.";
	}
	elseif (substr($_POST['downloadLink'],0,7) == "magnet:")// magnet link?
	{
		if (preg_match("#^magnet\:\?xt\=urn\:btih:([^\&]*)&#i",$_POST['downloadLink'],$m))
		{
			$hash 	= strtoupper($m[1]);
			$file 	= $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].$hash.".torrent";
			$content = "d10:magnet-uri".strlen($_POST['downloadLink']).":".$_POST['downloadLink']."e";
			
			if (preg_match("#dn=([^\&]*)#",$_POST['downloadLink'],$m))
			{
				$dirName = str_replace(array("+","%20"),array(" "," "),urldecode($m[1])); // this is already urlencoded by its nature
				$saveDir = $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].$dirName."/";
			}
			else
			{
				$dirName = $hash;
				$saveDir = $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].$hash."/";
			}
		}
		else 
		{
			$status['download'] = "Error: invalid magnet link.";
		}
	}
	elseif (!$content = file_get_contents($_POST['downloadLink'],0,stream_context_create(array('http' => array('timeout' => 5000)))))
	{
		$status['download'] = "Error: unable to download torrent.";
	}
	elseif (!$meta = Lightbenc::bdecode($content))
	{
		$status['download'] = "Error: unable to parse file, not a torrent file?";
	}
	elseif (!isset($meta['info']['name']))
	{
		$status['download'] = "Error: invalid torrent file.";
	}
	else
	{
		$dirName = str_replace(array("+","%20"),array(" "," "),urlencode($meta['info']['name']));
		$file 	= $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].urlencode($meta['info']['name']).".torrent";
		$saveDir = $_SESSION[$_SERVER['REMOTE_ADDR']]['path']['dDir'].$dirName."/";
	}
	
	if (!isset($status['download']))
	{
		file_put_contents($file,$content);
		if (!rtorrent::load($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$file))
		{
			$status['download'] = "Error: unable to load '".$file."' in rtorrent.";
			w3btorrent::write2log("upload","Unable to load '".$file."' in rtorrent."); 
		}
		else
		{
			// where we will be saving our torrent
			if (!file_exists($saveDir))
			{
				mkdir($saveDir);
			}
			
			if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['admin']) && !is_link($_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'].$dirName))
			{
				//echo $saveDir."<br />".$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'].$dirName;
				symlink($saveDir,$_SESSION[$_SERVER['REMOTE_ADDR']]['path']['userDir'].$dirName);
			}

			rtorrent::setSaveDir($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$saveDir,$hash);
			
			// magnet link
			if (isset($hash))
			{
				rtorrent::start($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$hash);
				w3btorrent::write2log("upload","Downloaded magnet-link to '".$file."'.");
				$status['download'] = "Magnet link is now downloading and the torrent can be started in a minute.<script type='text/javascript'>$('#downloadLink').val('');</script>";
			}
			else
			{
				$hash = rtorrent::getHashByFile($_SESSION[$_SERVER['REMOTE_ADDR']]['rtorrent']['rpc'],$file);
				w3btorrent::write2log("upload","Downloaded '".$file."'.");
				$status['download'] = "Torrent downloaded.<script type='text/javascript'>$('#downloadLink').val('');</script>";
			}
			
		}
	}
}

















?>