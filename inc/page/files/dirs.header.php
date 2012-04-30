<?php

//
//	HANDLE DIRS
//


if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))	// security
{
	return;
}

// get dirs
$dirs2 	= hdc::listDirs($realPath);
asort($dirs2);

$dirs = array();
foreach ($dirs2 as $dir)
{
	$dirs[] = array
	(
		"icon"=>(is_executable($realPath.$dir) && is_readable($realPath.$dir)?$CONFIG['icon']['files']['folder']:$CONFIG['icon']['files']['folderDeny']),
		"href"=>(is_executable($realPath.$dir) && is_readable($realPath.$dir)?"?p=f&path=".urlencode($path.$dir."/"):""),
		"path"=>(is_executable($realPath.$dir) && is_readable($realPath.$dir)?urlencode($path.$dir."/"):""),
		"name"=>$dir
	);
}



?>