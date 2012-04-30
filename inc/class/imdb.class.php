<?php

//Sun Feb  5 00:04:16 CET 2012

class imdb
{
	private static $data;
	public function formatSearchString($aSearch,$level = 0)
	{
		$replace = array("_",".","[","]");
		$with = array(" "," "," "," ");

		$search = str_replace($replace,$with,$aSearch);
		$search = preg_replace("#(\d{4}.).*#","\\1",$search);	// remove anything right for year
		$search = preg_replace("#([^ ]*rip).*#i","",$search); // remove anything right for anything with rip in it
		$search = preg_replace("#(\d{4})#","(\\1)",$search); // replace year with parenthesis
		$search = str_replace(array("((","))"),array("(",")"),$search);
		if ($level < 1)
		{
			$search = preg_replace("#([A-Z]{3,}+).*#","",$search); // remove anything right for UPPER CASE WORDS
		}
		
		if (empty($search))	// it was to strict
		{
			$search = imdb::formatSearchString($aSearch,1);
		}

		$search = strtolower($search); // this does in fact make a matter!
		
		return trim($search);
	}
	public function pickSearchHit($data)
	{
		if (preg_match('#esult(s?)\)<table><tr> <td valign="top"><a href="(.[^"]*)" onClick="#',$data,$m))
		{
echo "Picked search hit: ".$m[2]."\n";
			return "http://www.imdb.com".$m[2];
		}
	}
	public function search($search,$type="tt")
	{
		$opts = array(
		  'http'=>array(
		    'method'=>"GET",
		    'user_agent'=>"Mozilla 5.0"
		  )
		);

		$context = stream_context_create($opts);

		if (!$search = imdb::formatSearchString($search))
		{
			return;
		}

		$url = str_replace(" ","%20","http://www.imdb.com/find?q=".$search."&s=".$type);
		$data = file_get_contents($url,false,$context);

		if ($url = imdb::pickSearchHit($data))
		{
			$data = file_get_contents($url,false,$context); // the URL we fetched which should be our movie
		}

		if (!$data)
		{
			return; // not possible to download
		}

		return imdb::parse($data,$type);
	}
	public function parse($html,$type)
	{
		if ($type == "tt")
		{
			return imdb::parseVideo($html);
		}
	}
	public function parseVideo($html)
	{
		$result = array();
		file_put_contents("/tmp/html",$html);
		
		if (preg_match('<meta property="og:url" content="(.*?)" />',$html,$m))
		{
			$result['url'] = $m[1];
		}

		if (preg_match('#<h1 class="header" itemprop="name">\s+(.[^<]*)\s+<span>#i',$html,$m))
		{
			$result['title'] = trim($m[1]);
		}
		
		if (preg_match('#\(<a href="/year/\d+/">([^\)]\d+)</a>\)</span>#i',$html,$m))
		{
			$result['year'] = trim($m[1]);
		}

		if (preg_match('#<time itemprop="duration" [^<]*>(.[^<]*)</time>#i',$html,$m))
		{
			$result['time'] = $m[1];
		}

		if (preg_match('#<time itemprop="datePublished" [^<]*>(.[^<]*)</time>#i',$html,$m))
		{
			$result['publish'] = $m[1];
		}

		if (preg_match_all('#href="/genre/(.[^"]*)"#i',$html,$m))
		{
			$result['genre'] = array_unique($m[1]);
		}

		if (preg_match('#<div class="star-box-giga-star">(.[^<]*)</div>#is',$html,$m))
		{
			$result['rate'] = trim($m[1]);
		}

		if (preg_match('#<p itemprop="description">(.[^<]*)</p>#i',$html,$m))
		{
			$result['description'] = $m[1];
		}

		if (preg_match_all('#<a [^<]*>(.[^<]*)</a>\s+</td>\s+<td class="ellipsis">\s+\.\.\.\s+</td>\s+<td class="character">\s+<div>\s+(.[^<]*)#i',$html,$m))
		{
			for ($x=0;$x<count($m[1]);$x++)
			{
				$actor = str_replace("\n"," ",strip_tags($m[2][$x]));
				while (strpos($actor,"  ") !== false)
				{
					$actor = str_replace("  "," ",$actor);
				}
				$result['cast'][$m[1][$x]] = trim($actor);
			}
		}

		return $result;
	}
}

?>
