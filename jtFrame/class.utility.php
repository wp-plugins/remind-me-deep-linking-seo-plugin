<?php 

class jtUtility {
		
	/**
	* Utility function to return a value from a named array or a specified default
	*/
	
	public function getParam( &$arr, $name, $def=null, $mask=0 ) {
		define( "__NOTRIM", 0x0001 );
		define( "__ALLOWHTML", 0x0002 );
		define( "__NOSCRIPTS", 0x0004 );
		
		//$return = null;
		if (isset( $arr[$name] )) {
			if (is_array($arr[$name])){
				while(list($key,$value) = each($arr[$name])){
					$arr[$name][$key] = jtUtility::getParam($arr[$name], $key, $value);
				}
			} else {
				if (($mask&__NOSCRIPTS)) {
					$arr[$name] = preg_replace( "'<script[^>]*>.*?</script>'si", '', $arr[$name] );
					$arr[$name] = preg_replace( '/<!--.+?-->/', '', $arr[$name] );
				}
	
				if (!($mask&__NOTRIM)) {
					$arr[$name] = trim( $arr[$name] );
				}
				if (!($mask&_HMD_ALLOWHTML)) {
					$arr[$name] = strip_tags( $arr[$name] );
				}
				if (get_magic_quotes_gpc()) {
					$arr[$name] = jtUtility::stripslashes( $arr[$name] );
				}
				$arr[$name] = str_replace("`","'",$arr[$name]);
				$arr[$name] = mysql_real_escape_string($arr[$name]);
				//$arr[$name] = addslashes( $arr[$name] );
			}
	
			
			return $arr[$name];
		} else {
			return $def;
		}
	}
	
	/**
	* Copy the named array content into the object as properties
	* only existing properties of object are filled. when undefined in hash, properties wont be deleted
	* @param array the input array
	* @param obj byref the object to fill of any class
	* @param string
	* @param boolean
	*/
	public function bindArrayToObject( $array, &$obj, $ignore="", $prefix=NULL, $checkSlashes=true ) {
		if (!is_array( $array ) || !is_object( $obj )) {
			return (false);
		}
	
		if ($prefix) {
			foreach (get_object_vars($obj) as $k => $v) {
				if (strpos( $ignore, $k) === false) {
					if (isset($array[$prefix . $k ])) {
						$obj->$k = ($checkSlashes && get_magic_quotes_gpc()) ? jtUtility::stripslashes( $array[$k] ) : $array[$k];
					}
				}
			}
		} else {
			foreach (get_object_vars($obj) as $k => $v) {
				if (strpos( $ignore, $k) === false) {
					if (isset($array[$k])) {
						$obj->$k = ($checkSlashes && get_magic_quotes_gpc()) ? jtUtility::stripslashes( $array[$k] ) : $array[$k];
					}
				}
			}
		}
	
		return true;
	}
	
	/**
	* Strip slashes from strings or arrays of strings
	* @param value the input string or array
	*/
	public function stripslashes(&$value)
	{
		$ret = '';
	    if (is_string($value)) {
			$ret = stripslashes($value);
		} else {
		    if (is_array($value)) {
		        $ret = array();
		        while (list($key,$val) = each($value)) {
		            $ret[$key] = jtUtility::stripslashes($val);
		        } // while
		    } else {
		        $ret = $value;
			} // if
		} // if
	    return $ret;
	}
	
	public function getConfig($model, $configString){
		
		$config = get_option($configString);
		$mFrame = jtMainFrame::getInstance();
		if (!is_array($config)){
			if (class_exists($model)){
				$model = new $model($mFrame->db);
				$config = $model->getDefaults();	
			} else {
				$config = array();
			}
		}
		return $config;
	}
	
	public function cleanTxt($txt, $br="<br />"){
		$txt = trim($txt);
		$txt = eregi_replace("\r",$br,$txt);
		$txt = eregi_replace("\n","",$txt);

		return $txt;
	}	
}
?>