<?php

/* 

unknown creator, please fill in this blank

*/

class Lightbenc
{
    public static function bdecode($s, &$pos=0) {
        if($pos>=strlen($s)) {
            return null;
        }
        switch($s[$pos]){
        case 'd':
            $pos++;
            $retval=array();
            while ($s[$pos]!='e'){
                $key=Lightbenc::bdecode($s, $pos);
                $val=Lightbenc::bdecode($s, $pos);
                if ($key===null || $val===null)
                    break;
                $retval[$key]=$val;
            }
            $retval["isDct"]=true;
            $pos++;
            return $retval;

        case 'l':
            $pos++;
            $retval=array();
            while ($s[$pos]!='e'){
                $val=Lightbenc::bdecode($s, $pos);
                if ($val===null)
                    break;
                $retval[]=$val;
            }
            $pos++;
            return $retval;

        case 'i':
            $pos++;
            $digits=strpos($s, 'e', $pos)-$pos;
            $val=round((float)substr($s, $pos, $digits));
            $pos+=$digits+1;
            return $val;

    //	case "0": case "1": case "2": case "3": case "4":
    //	case "5": case "6": case "7": case "8": case "9":
        default:
            $digits=strpos($s, ':', $pos)-$pos;
            if ($digits<0 || $digits >20)
                return null;
            $len=(int)substr($s, $pos, $digits);
            $pos+=$digits+1;
            $str=substr($s, $pos, $len);
            $pos+=$len;
            //echo "pos: $pos str: [$str] len: $len digits: $digits\n";
            return (string)$str;
        }
        return null;
    }

    public static function bencode(&$d){
        if(is_array($d)){
            $ret="l";
            if($d["isDct"]){
                $isDict=1;
                $ret="d";
                // this is required by the specs, and BitTornado actualy chokes on unsorted dictionaries
                ksort($d, SORT_STRING);
            }
            foreach($d as $key=>$value) {
                if($isDict){
                    // skip the isDct element, only if it's set by us
                    if($key=="isDct" and is_bool($value)) continue;
                    $ret.=strlen($key).":".$key;
                }
                if (is_int($value) || is_float($value)){
                    $ret.="i${value}e";
                }else if (is_string($value)) {
                    $ret.=strlen($value).":".$value;
                } else {
                    $ret.=Lightbenc::bencode ($value);
                }
            }
            return $ret."e";
        } elseif (is_string($d)) // fallback if we're given a single bencoded string or int
            return strlen($d).":".$d;
        elseif (is_int($d) || is_float($d))
            return "i${d}e";
        else
            return null;
    }

    public static function bdecode_file($filename){
        $f=file_get_contents($filename, FILE_BINARY);
        return Lightbenc::bdecode($f);
    }

    public static function bdecode_getinfo($filename){
        $t = Lightbenc::bdecode(file_get_contents($filename, FILE_BINARY));
        $t['info_hash'] = sha1(Lightbenc::bencode($t['info']));

        if(is_array($t['info']['files'])){ //multifile
            $t['info']['size'] = 0;
            $t['info']['filecount'] = 0;

            foreach($t['info']['files'] as $file){
                $t['info']['filecount']++;
                $t['info']['size']+=$file['length'];
            }
        }else{
            $t['info']['size'] = $t['info']['length'];
            $t['info']["filecount"] = 1;
            $t['info']['files'][0]['path'] = $t['info']['name'];
            $t['info']['files'][0]['length'] = $t['info']['length'];
        }
        return $t;
    }
}


?>