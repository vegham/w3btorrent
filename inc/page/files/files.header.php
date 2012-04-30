<?php

//
//	HANDLE FILES
//

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))	// security
{
	return;
}

require_once("inc/class/misc.class.php");
require_once("inc/class/mime.class.php");


//
// DOWNLOAD
//
if (is_file($realPath) && is_readable($realPath))
{
	w3btorrent::write2log("files","'$realPath' downloaded.");
	
	// file is in our tree
	if (misc::inWebTree($realPath))
	{
		$location = substr($realPath,strlen(dirname($_SERVER['SCRIPT_FILENAME']))+1);
		$location = str_replace("%2F","/",$location);
		header("location: $location");
		exit();
	}
	
	// let's be able to continue browsing:)
	session_write_close();
	
	// file is not in our web tree and need to download it through PHP
	if (!$type = mime::contentType($realPath))
	{
		$type = "application/octet-stream";
	}
	
	// make sure we don't timeout
	ini_set("max_execution_time",7200); // 48 hours
	header("cache-control: none");
	header("pragma: none");
	header("accept-ranges: bytes");
	header("content-type: ".$type);
	header("content-disposition: attachment; filename=\"".basename($realPath)."\";");
	header("content-length: ".filesize($realPath));
	
	// let's be smart printing out chunks
	misc::readFileChunk($realPath);
	exit();
}




// get files
$files2 	= hdc::listFiles($realPath);
asort($files2);

$files = array();
$icons = array();
foreach ($files2 as $filename)
{
	$file = $realPath.$filename;
	if (w3btorrent::internalFile($file))
	{
		continue;
	}
	$mime = mime::contentType($file);
	$mimeFile = str_replace(array("/","-"),array("_","_"),$mime);
	
	// save mime types for performance 
	if (!isset($icons[$mime]))
	{
		// Find icon. It is done this way because I didn't want to waste time writing them all in $CONFIG (but you can use a script for that! bla bla)
		if (is_file("pix/icons/mimetypes/".$mimeFile.".png"))
		{
			$icons[$mime] = "pix/icons/mimetypes/".$mimeFile.".png";
		}
		else
		{
			$icons[$mime] = "pix/icons/mimetypes/unknown.png";
		}
	}
	
	$files[] = array
	(
		"icon"=>$icons[$mime],
		"mime"=>$mime,
		"href"=>(is_readable($file)?"?p=f&path=".urlencode($path.$filename):""),
		"path"=>(is_readable($file)?urlencode($path.$filename):""),
		"name"=>$filename,
		"size"=>misc::byteChange(filesize($file)),
		"date"=>date("F d Y H:i:s",filemtime($file))
	);
}

// proper cleaning
unset($icons,$mime,$file,$filename,$mimeFile);

?>