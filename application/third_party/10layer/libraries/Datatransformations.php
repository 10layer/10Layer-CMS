<?php
class Datatransformations {
	public function urlid(&$sender, $value, $tableinfo, $usedate=true) {
		$ci=&get_instance();
		$ci->load->helper("smarturl");
		$urlid=smarturl($value, false, !$usedate);
		$table=explode(".",$tableinfo);
		if (sizeof($table)!=2) {
			show_error("Format for urlid transformation: 'urlid'=>'tablename.fieldname'");
		}
		$urlid=$this->safe_urlid($urlid, $table[0], $table[1]);
		
		return $urlid;
	}
	
	public function copy(&$sender, $value, $field) {
		return $sender->getField($field)->value;
	}
	
	public function copymultiple(&$sender, $value, $join="", $fields) {
		$tmp=array();
		if (!is_array($fields)) {
			show_error("Fields must be of type array");
			return "";
		}
		
		foreach($fields as $field) {
			$tmp[]=$sender->getField($field)->value;
		}
		return implode($join, $tmp);
	}
	
	public function concat(&$sender, $value, $s) {
		return $value.$s;
	}
	
	public function soundslide(&$sender, $value) {
		$s=exec("/usr/bin/unzip -o -d .".dirname($value)." .{$value} | grep soundslider.swf");
		$s=str_replace("./resources/uploads/files/original/","",$s);
		$parts=explode(" ",$s);
		return $parts[sizeof($parts)-1];
	}
	
	public function safe_urlid($urlid, $tablename, $field) {
		$ci=&get_instance();
		$query=$ci->db->get_where($tablename, array($field=>$urlid));
		$addnum=0;
		while ($query->num_rows()!=0) {
			$newnum="";
			while (is_numeric(substr($urlid,-1))) {
				$newnum=substr($urlid,-1).$newnum;
				$urlid=substr($urlid,0,-1);
			}
			if (empty($newnum)) {
				$urlid=$urlid."-1";
			} else {
				$addnum++;
				$urlid=$urlid.$addnum;
			}
			
			$query=$ci->db->get_where($tablename, array($field=>$urlid));
		}
		return $urlid;
	}
	
	public function str_replace(&$sender, $value, $search, $replace) {
		return str_replace($search, $replace, $value);
	}
	
	public function safetext(&$sender, $s) {
		
		/*$s=str_replace(array('Ò','Ó'),'"', $s);
		$s=str_replace(array(chr(145),'Õ'), "'", $s);
		$s=str_replace(chr(226).chr(128).chr(147), "-", $s);
		$s=str_replace("Ž", "e", $s);
		$s=str_replace("É", "É", $s);*/
		$s=strip_tags($s);
		$s=$this->convert_ascii($s);
		//$s=$this->msword_conversion($s);
		$encoding = mb_detect_encoding($s, "UTF-8,ISO-8859-1,WINDOWS-1252");
		
		if ($encoding != 'UTF-8') {
			$s=iconv($encoding, 'UTF-8//TRANSLIT', $s);
		}
		
		/*print $s;
		print "\n";
		for($x=0; $x<strlen($s); $x++) {
			print ord($s[$x]).",";
		}
		die();*/
		return $s;
	}
	
	/**
	 * Remove any non-ASCII characters and convert known non-ASCII characters 
	 * to their ASCII equivalents, if possible.
	 *
	 * @param string $string 
	 * @return string $string
	 * @author Jay Williams <myd3.com>
	 * @license MIT License
	 * @link http://gist.github.com/119517
	 */
	protected function convert_ascii($string) {
		// Replace Single Curly Quotes
		$search[]  = chr(226).chr(128).chr(152);
		$replace[] = "'";
		$search[]  = chr(226).chr(128).chr(153);
		$replace[] = "'";
		
		// Replace Smart Double Curly Quotes
		$search[]  = chr(226).chr(128).chr(156);
		$replace[] = '"';
		$search[]  = chr(226).chr(128).chr(157);
		$replace[] = '"';
		
		// Replace En Dash
		$search[]  = chr(226).chr(128).chr(147);
		$replace[] = '--';
		
		// Replace Em Dash
		$search[]  = chr(226).chr(128).chr(148);
		$replace[] = '---';
		
		// Replace Bullet
		$search[]  = chr(226).chr(128).chr(162);
		$replace[] = '*';
		
		// Replace Middle Dot
		$search[]  = chr(194).chr(183);
		$replace[] = '*';
		
		// Replace Ellipsis with three consecutive dots
		$search[]  = chr(226).chr(128).chr(166);
		$replace[] = '...';
		
		$search[]  = chr(195).chr(169);
		$replace[] = 'e';
		
		
		// Apply Replacements
		$string = str_replace($search, $replace, $string);
		
		// Remove any non-ASCII Characters
		$string = preg_replace("/[^\x01-\x7F]/","", $string);
		
		return $string; 
	}
}
?>