<?php

// load header of page
if (isset($PAGE) && is_file("inc/page/$PAGE/header.php"))
{
	require_once("inc/page/$PAGE/header.php");
}

?>
