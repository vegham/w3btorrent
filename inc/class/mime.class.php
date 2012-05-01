<?php

/*
PHP class for getting mime content in series of ways
Copyright (C) 2008  Vegard Hammerseth <vegard@hammerseth.com> (http://vegard.hammerseth.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or any later version.

v1.0.1
*/

class mime
{
	private static $type = array
	(
		"unknown"		=>"unknown",
		"mp3"		=>"audio/mp3",
		"ogg"		=>"audio/x-vorbis+ogg",
		"wav"		=>"audio/x-wav",
		"mpc"		=>"audio/x-musepack",
		
		"jpg"		=>"image/jpeg",
		"jpeg"		=>"image/jpeg",
		"png"		=>"image/png",
		"bmp"		=>"image/bmp",
		"gif"		=>"image/gif",
		"ico"		=>"image/x-ico",
		"xcf"		=>"image/x-xcf",
		"svg"		=>"image/svg+xml",	
		
		"avi"		=>"video/xvid",
		"wmv"		=>"video/x-ms-wmv",
		"mp4"		=>"video/mp4",
		"flv"		=>"video/flash",
		"mov"		=>"video/quicktime",
		"(mpeg|mpg)"	=>"video/mpeg",
		"asf"		=>"video/x-ms-asf",
		
		"gz"			=>"application/x-gzip",
		"zip"		=>"application/x-zip",
		"tar"		=>"application/x-tar",
		"bz2"		=>"application/x-bzip2",
		"exe"		=>"application/x-dosexec",
		"bin"		=>"application/x-executable",
		"iso"		=>"application/x-iso9660-image",
		"phps"		=>"text/x-httpd-php-source",
		"(rar|r(\d+))"	=>"application/x-rar",
		"deb"		=>"application/x-debian-package",
		"torrent"		=>"application/x-bittorrent",
		"(nrg|img|mds|mdf)"		=>"application/octet-stream",
		"(run|sh)"	=>"application/x-shellscript",
		"db"			=>"application/x-ole-storage",
		"cue"		=>"application/x-cue",
		"pdf"		=>"application/pdf",
		"php"		=>"application/x-php",
		"js"			=>"application/javascript",
		"doc"		=>"application/msword",
		"odt"		=>"application/vnd.oasis.opendocument.text",
		"swf"		=>"application/x-shockwave-flash",
		"ttf"		=>"application/x-font-ttf",

		"(htm|html|xhtml)"	=>"text/html",
		"(txt|log|nfo|ccd)"	=>"text/plain",
		"xml"		=>"text/xml",
		"(srt|sub)"	=>"text/subtitle",
		"css"		=>"text/css",
	);
	
	
	public function contentType($argFile)
	{
		if (!is_file($argFile) || !is_readable($argFile))
		{
			return "unknown";
		}

		$mimeType = "unknown";
		if (function_exists("mime_content_type"))
		{
			$mimeType = mime_content_type($argFile);
		}
		elseif (function_exists("finfo_open") && function_exists("finfo_file") && function_exists("finfo_close") && $finfo = finfo_open(FILEINFO_MIME))
		{
			$mimeType = finfo_file($finfo,$argFile);
			finfo_close($finfo);
		}
		elseif (function_exists("exec"))
		{
			$mimeType = trim(exec("file -bi ".escapeshellarg($argFile)));
		}
		elseif (function_exists("shell_exec"))
		{
			$mimeType = trim(shell_exec("file -bi ".escapeshellarg($argFile)));
		}
		
		return $mimeType;
	} // 1.0.1



	public function typeByString($argString)
	{
		$ext = array_pop(explode(".",$argString));
		foreach (mime::$type as $expr=>$value)
		{
			if (preg_match("/^".$expr."$/i",$ext))
			{
				return $value;
			}
		}
	}
	
}

?>