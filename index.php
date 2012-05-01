<?php

/*	this file is part of w3btorrent
	all requests are through this page, ajax or not	*/
	

error_reporting(E_ALL);

// need PHP 5 or greater to work
if (phpversion() < 5)
{
	echo "w3btorrent now need's PHP 5 to work properly, please upgrade. This error can be removed from line 12-13 in '".$_SERVER["PHP_SELF"]."'.";
	exit();
}

require_once("inc/default.inc.php");
require_once("inc/page.inc.php");
require_once("header.php");

if (isset($_POST['ajax']))
{
	// session has been lost
	if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
	{
		echo '<script type="text/javascript">location.href ="'.misc::url().'";</script>';
		exit();
	}
	require_once("inc/page/$PAGE/index.php");
	return;
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title><?php echo $_SESSION[$_SERVER['REMOTE_ADDR']]['title']; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<meta name="description" content="w3btorrent - keep it simple" />
	<link rel="shortcut icon" href="pix/w3btorrent.ico" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/class.css" />
	<link rel="stylesheet" type="text/css" href="css/list.css" />
	<script src="script/jquery.min.js"></script>
	<script src="script/w3btorrent.js"></script>
	<script type="text/javascript">
		var ACTIVE_TAB;
		$(document).ready(function() {
			page = '<?php echo $PAGE; ?>'; 
			$('#'+page).addClass("active");
			ACTIVE_TAB = $('#'+page);
		});
	</script>
</head>
<body>
<?php

//print_r($_SESSION);
if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))
{
	require_once("inc/menu.inc.php");
	echo '<div id="content">';
	require_once("inc/page/$PAGE/index.php");
	echo '</div>';
}
else
{
	require_once("inc/page/$PAGE/index.php");
}

?>
</body>
</html>
