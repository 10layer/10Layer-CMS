<?php
	function url_to_text($url,$link=true,$join=" &gt; ") {
		if (substr($url,0,1)=="/") {
			$url=substr($url,1);
		}
		$tmp=explode("/",$url);
		$result=array();
		$tmpurl="";
		foreach($tmp as $s) {
			$tmpurl.="/".trim($s);
			$s=str_replace(array("_","-")," ",$s);
			$s=trim(ucwords($s));
			if ($link) {
				$result[]="<a href='".site_url($tmpurl)."'>$s</a>";
			} else {
				$result[]=$s;
			}
		}
		return implode($join,$result);
	}
	
	function cookiecrumb() {
		$ci=&get_instance();
		$parts=$ci->uri->uri_string();
		return url_to_text($parts);
	}
	
/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('site_url'))
{
	function site_url($uri = '') {
		$CI =& get_instance();
		return $CI->config->site_url($uri);
	}
}

	function wordlimit($s,$limit=20, $elipse="...") {
		$tmp=explode(" ",$s);
		$tmp2=array();
		if (sizeof($tmp) < $limit) {
			$limit=sizeof($tmp);
		}
		for($x=0; $x<$limit; $x++) {
			$tmp2[]=$tmp[$x];
		}
		$s=implode(" ",$tmp2);
		return trim($s).$elipse;
	}
?>