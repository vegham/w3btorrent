<?php

//
// THIS FILES BUILDS TREE OVER BROWSED PATHs
//

if (!isset($_SESSION[$_SERVER['REMOTE_ADDR']]['account']))	// security
{
	return;
}

$tree = array(array("name"=>"home","href"=>"?p=f&path=".urlencode("/")));

$t = "/";
foreach (explode("/",$path) as $branch)
{
	if ($branch == "")
	{
		continue;
	}
	$t .= $branch."/";
	$tree[] = array
	(
		"href"=>"?p=f&path=".urlencode($t),
		"name"=>$branch
	);
}

unset($t,$branch);

?>