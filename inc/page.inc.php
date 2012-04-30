<?php

$PAGES['admin'] = array
(
	"d"=>"downloads",
	"u"=>"upload",
	"f"=>"files",
	"l"=>"log",
	"s"=>"settings",
	"a"=>"account"
);
$PAGES['user'] = array
(
	"d"=>"downloads",
	"u"=>"upload",
	"f"=>"files",
	"a"=>"account",
	"s"=>"settings"
);
//print_r($_SESSION);
// all the page names defined for w3btorrent
$PAGE = (isset($_GET['p'])?$_GET['p']:"");
if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	if ($PAGE != "a" && isset($_SESSION[$_SERVER['REMOTE_ADDR']]['setup']))
	{
		$PAGE = "settings";
	}
	// client already has a page stored
	else if (empty($PAGE) && isset($_SESSION[$_SERVER['REMOTE_ADDR']]['page']))
	{
		$PAGE = $_SESSION[$_SERVER['REMOTE_ADDR']]['page'];
	}
	// admin user's default page
	elseif (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['admin']))
	{
		if (!isset($PAGES['admin'][$PAGE]))
		{
			$PAGE = "upload";
		}
		else
		{
			$PAGE = $PAGES['admin'][$PAGE];	
		}
	}
	// regular user default page
	elseif (!isset($PAGES['user'][$PAGE]))
	{
		$PAGE = "upload";
	}
	else
	{
		$PAGE = $PAGES['user'][$PAGE];
	}
	
	$_SESSION[$_SERVER['REMOTE_ADDR']]['page'] = $PAGE;
}
else
{
	$PAGE = "account"; // give us the login page
}

?>
