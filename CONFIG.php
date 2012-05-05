<?php

/*	this file is part of w3btorrent which is used to setup paths etc.

	global config file for w3btorrent, this file will override online configuration!
	set values to "" if you want to use the online configuration
*/

// if the mysql-variables below is set, the XML config is skipped 
$CONFIG['mysql']['enabled']	= false;	// set this to true and mysql settings online are completly ignored
$CONFIG['mysql']['hostname']	= "";
$CONFIG['mysql']['username']	= "";
$CONFIG['mysql']['password']	= "";
$CONFIG['mysql']['db'] 		= "";






// this has no effect if mysql is enabled
$CONFIG['cfg'] 	= ".htconfig.xml"; //etc/w3btorrent/config.xml

//$CONFIG['userDdir']	= "w3btorrent_users";
//$CONFIG['dDir']	= "/var/cache/w3btorrent/";



//
//	MISC
//
$CONFIG['homePage']		= "http://code.google.com/p/w3btorrent/";
$CONFIG['helpPage']		= "http://code.google.com/p/w3btorrent/w/list";
$CONFIG['version']		= "0.9.2";	// not sure what to do about this now that git has version controll..


//
// ICONS
//
$CONFIG['icon']['login']['user'] = "pix/icons/filesystems/user_identity.png";
$CONFIG['icon']['login']['password'] = "pix/icons/actions/password.png";

$CONFIG['icon']['menu']['downloads'] = "pix/icons/actions/edit_find_next.png";
$CONFIG['icon']['menu']['upload'] = "pix/icons/actions/edit_find_previous.png";
$CONFIG['icon']['menu']['files'] = "pix/icons/filesystems/folder.png";
$CONFIG['icon']['menu']['settings'] = "pix/icons/actions/configure.png";

$CONFIG['icon']['upload']['download'] 	= "pix/icons/actions/edit_find_next.png";
$CONFIG['icon']['upload']['upload'] 	= "pix/icons/actions/edit_find_previous.png";

$CONFIG['icon']['files']['folder'] = "pix/icons/filesystems/folder.png";
$CONFIG['icon']['files']['folderDeny'] = "pix/icons/filesystems/folder_red.png";
$CONFIG['icon']['files']['delete'] = "pix/icons/32x32/actions/edit_delete_mail.png";

$CONFIG['icon']['mysql']['tip'] = "pix/icons/apps/ktip.png";
$CONFIG['icon']['mysql']['enabled'] = "pix/icons/actions/dialog_ok.png";
$CONFIG['icon']['mysql']['hostname'] = "pix/icons/filesystems/network_server.png";
$CONFIG['icon']['mysql']['port'] = "pix/icons/actions/transform_move.png";
$CONFIG['icon']['mysql']['username'] = "pix/icons/filesystems/user_identity.png";
$CONFIG['icon']['mysql']['password'] = "pix/icons/actions/password.png";
$CONFIG['icon']['mysql']['db'] = "pix/icons/filesystems/network_server_database.png";

$CONFIG['icon']['path']['dDir'] = "pix/icons/filesystems/folder_downloads.png";
$CONFIG['icon']['path']['userDir'] = "pix/icons/filesystems/folder.png";

$CONFIG['icon']['account']['user'] = "pix/icons/filesystems/user_identity.png";
$CONFIG['icon']['account']['type'] = "pix/icons/actions/edit_find_user.png";
$CONFIG['icon']['account']['username'] = "pix/icons/actions/edit_user.png";
$CONFIG['icon']['account']['password'] = "pix/icons/actions/password.png";


$CONFIG['icon']['downloads']['stop'] = "pix/icons/32x32/actions/no.png";
$CONFIG['icon']['downloads']['stopAll'] = "pix/icons/32x32/actions/application_exit.png";
$CONFIG['icon']['downloads']['start'] = "pix/icons/32x32/actions/1rightarrow.png";
$CONFIG['icon']['downloads']['startAll'] = "pix/icons/32x32/actions/2rightarrow.png";
$CONFIG['icon']['downloads']['delete'] = "pix/icons/32x32/actions/edit_delete_mail.png";


$CONFIG['icon']['status']['rtorrent'] 		= "pix/icons/apps/ktorrent.png";
$CONFIG['icon']['status']['crontab'] 		= "pix/icons/actions/alarmclock.png";
$CONFIG['icon']['status']['connection'] 	= "pix/icons/devices/network_wireless.png";
$CONFIG['icon']['status']['ok'] 			= "pix/icons/actions/apply.png";
$CONFIG['icon']['status']['fail'] 		= "pix/icons/actions/edit_delete_mail.png";
$CONFIG['icon']['status']['loading'] 		= "pix/loading.gif";

$CONFIG['icon']['rtorrent']['rpc'] = "pix/icons/devices/network_wireless.png";
$CONFIG['icon']['rtorrent']['download'] = "pix/icons/actions/edit_find_next.png";
$CONFIG['icon']['rtorrent']['upload'] = "pix/icons/actions/edit_find_previous.png";
$CONFIG['icon']['rtorrent']['port'] = "pix/icons/actions/transform_move.png";



?>
