<?php

/*	this file is part of w3btorrent
	it loads headers for all browsing pages	*/

// load header of page
if (isset($PAGE) && is_file("inc/page/$PAGE/header.php"))
{
	require_once("inc/page/$PAGE/header.php");
}

?>
