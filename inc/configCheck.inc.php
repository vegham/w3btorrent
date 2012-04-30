<?php

require_once("CONFIG.php");

if ($CONFIG['mysql']['enabled'])
{
	return;
}

if (!function_exists("simplexml_load_file"))
{
	echo 'Error: The function \'<a href="http://php.net/manual/en/function.simplexml-load-file.php">simplexml_load_file()</a>\' is not available on this machine, please install XML support to make w3btorrent work properly.';
	exit();
}
elseif (!isset($CONFIG['cfg']))
{
	echo 'Error: You don\'t have any config path set, please edit \''.getcwd().'/CONFIG.php\' to correct this.';
	exit();
}
elseif (!file_exists($CONFIG['cfg']))
{
	echo 'Error: Given configfile does not exist, please edit \''.getcwd().'/CONFIG.php\' to correct this.';
	exit();
}
elseif (!is_file($CONFIG['cfg']))
{
	echo 'Error: Given configfile is not a file, dir? please edit \''.getcwd().'/CONFIG.php\' to correct this.';
	exit();
}
elseif (!is_readable($CONFIG['cfg']))
{
	echo 'Error: Given configfile is not readable, please do one of following: <ul><li>run `chmod 666 \''.realpath($CONFIG['cfg']).'\'` from a shell.</li><li>set \'+r\' on \''.realpath($CONFIG['cfg']).'\' through your FTP client.</li><li>edit \''.getcwd().'/CONFIG.php\' and select different path.</li></ul>';
	exit();
}
elseif (!is_writeable($CONFIG['cfg']))
{
	echo 'Error: Given configfile is not writeable, please do one of following: <ul><li>run `chmod 666 \''.realpath($CONFIG['cfg']).'\'` from a shell.</li><li>set \'+w\' on \''.realpath($CONFIG['cfg']).'\' through your FTP client.</li><li>edit \''.getcwd().'/CONFIG.php\' and select different path.</li></ul>';
	exit();
}
elseif (!$cfg = simplexml_load_file($CONFIG['cfg']))
{
	echo 'Error: Unable to load configfile, invalid XML format? Please correct this by editing \''.realpath($CONFIG['cfg']).'\' or \''.getcwd().'/CONFIG.php\'';
	exit();
}

?>
