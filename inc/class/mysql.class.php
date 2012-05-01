<?php

/*
PHP class to easy handle database operations/queries
Copyright (C) 2006, 2007  Vegard Hammerseth <vegard@hammerseth.com> (http://vegard.hammerseth.com)

free for all use except in commercial applications

v1.0.3
- removed bad comments
- changed function names
- added error variable and function

v1.0.1
*/



class mysql
{
	private static $link = array();
	private static $error;
	
	// give error
	public function error()
	{
		return mysql::$error;
	}

	// connect
	public function connect($argHost,$argUser,$argPasswd,$argId = "0")
	{
		if (isset(mysql::$link[$argId]))
		{
			mysql::$error = "Already connected";
			return false;
		}
		if (!function_exists('mysql_connect'))
		{
			mysql::$error = "PHP-mysql not installed";
			return false;
		}

		if (!mysql::$link[$argId] = mysql_connect($argHost,$argUser,$argPasswd))
		{
			mysql::$error = mysql_error();
			return false;
		}
		
		return true;
	}


	
	// select db
	public function selectDb($argDb,$argId = "0")
	{
		if (!isset(mysql::$link[$argId]))
		{
			mysql::$error = "Not connected";
			return false;
		}

		if (!empty($argDb) && !mysql_selectDb($argDb,mysql::$link[$argId]))
		{
			mysql_query("create database `$argDb`",mysql::$link[$argId]);
			if (!mysql_selectDb($argDb,mysql::$link[$argId]))
			{
				mysql::$error = "Database '$argDb' dosen't exists and can't be created";
				return false;
			}
		}
		
		return true;
	}



	// $return[0][0] is $return['row_1']['field_1']
	public function fetchArrayData($argResource,$argId = "0")
	{
		while ($row = mysql_fetch_array($argResource,mysql::$link[$argId]))
		{
			$return[] = array_values($row);
		}
		
		if (isset($return))
		{
			return $return;
		}
	}



	// public function to escape dangerous SQL commands
	public function escape($argReturn,$argId = "0")
	{
		if (!isset(mysql::$link[$argId]))
		{
			mysql::$error = "Not connected";
			return false;
		}

		return mysql_real_escape_string($argReturn,mysql::$link[$argId]);
	}



	// our main query function, sending the info the db
	public function query($argDb,$argNewTable,$argQuery,$argId = "0")
	{
		if (!isset(mysql::$link[$argId]))
		{
			mysql::$error = "Not connected";
			return false;
		}

		if ($error = mysql::selectDb($argDb,$argId))
		{
			if ($error != "1")
			{
				mysql::$error = $error;
				return false;
			}
		}

		$mysql = mysql_query($argQuery,mysql::$link[$argId]);

		// create table if it don't exists
		if (empty($mysql) && preg_match("/^Table '$argDb\.(.*?)' doesn't exist$/",mysql_error(mysql::$link[$argId]),$m))
		{
			if (!mysql_query($argNewTable,mysql::$link[$argId]) || !$mysql = mysql_query($argQuery,mysql::$link[$argId]))
			{
				return mysql_error(mysql::$link[$argId]);
			}
		}

		if (empty($mysql))
		{
			return mysql_error(mysql::$link[$argId]);
		}

		$return = array();
		while ($row = @mysql_fetch_array($mysql,MYSQL_NUM))
		{
			$return[] = array_values($row);
		}
		
		return $return;
	}
	
	
}

?>
