<?php
	
	/* DISABLE: Magic quotes */
    if(get_magic_quotes_gpc() == 1){
        # Définition de la fonction récursive.
        function remove_magic_quotes(&$array){
            foreach($array as $key => $val){
                # Si c'est un array, recurssion de la fonction, sinon suppression des slashes
                if(is_array($val)){
                    remove_magic_quotes($array[$key]);
                } else if(is_string($val)){
                    $array[$key] = stripslashes($val);
                }
            }
        }
        # Appel de la fonction pour chaque variables.
        remove_magic_quotes($_POST);
        remove_magic_quotes($_GET);
        remove_magic_quotes($_REQUEST);
        remove_magic_quotes($_SERVER);
        remove_magic_quotes($_COOKIE);
    }

	function pathto($abs) {
		if($abs && $abs[0]=="/") {
			return $_SERVER['DOCUMENT_ROOT'].'/WS'.$abs;
		} else {
			return $abs;
		}
	}

    function seems_utf8($str) {
        $length = strlen($str);
        for ($i=0; $i < $length; $i++) {
        $c = ord($str[$i]);
        if ($c < 0x80) $n = 0; # 0bbbbbbb
        elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
        elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
        elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
        elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
        elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
        else return false; # Does not match any model
        for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
        if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
        return false;
        }
        }
        return true;
    }

	function utf8_obj_encode($val) {
		if(is_array($val)) { 
            // recurse on array elements
			$newval = array();
			foreach($val as $key => $value) {
				$newval[$key] = utf8_obj_encode($value); 
			}
			return $newval;
        } else if (is_string($val)) { 
            // encode string values
            if(!seems_utf8($val)) return utf8_encode($val); 
            return $val;
        } else {
			// do nothing on other objects
			return $val;
		}

	}
	
	function js_encode($obj) {
		return json_encode(utf8_obj_encode($obj));
	}
	
	function hex2str($hexstr) {
		$hexstr = str_replace(' ', '', $hexstr);
		$hexstr = str_replace('\x', '', $hexstr);
		$retstr = pack('H*', $hexstr);
		return $retstr;
	}

	function str2hex($string) {
		$hexstr = unpack('H*', $string);
		return array_shift($hexstr);
	}
	
	require_once(pathto('/API/CORE/throw.php'));
	require_once(pathto('/API/CORE/cache.php'));
	require_once(pathto('/API/CORE/sql.php'));
	require_once(pathto('/API/CORE/auth.php'));

?>