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
}
?>