<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
	/**
	 * 10Layer User Prefs Class
	 *
	 * Records user preferences so that stuff can automagically customise itself to user behaviour
	 *
	 * @package		10Layer
	 * @subpackage	Libraries
	 * @category	Libraries
	 * @author		Jason Norwood-Young
	 * @link		http://10layer.com
	 */
	
	class Tluserprefs {
		public $ci;
		
		public function __construct() {
			$this->ci=&get_instance();
			$this->ci->load->library("mongo_db");
		}
		
		public function user_setup() {
			$userid=$this->ci->session->userdata("id");
			$existing=$this->ci->mongo_db->where(array("userid"=>$userid))->get("userprefs");
			if (empty($existing[0]->userid)) {
				$this->ci->mongo_db->insert("userprefs",array("userid"=>$userid, "last_login"=>time(), "login_count"=>1));
			} else {
				$this->ci->mongo_db->where(array("userid"=>$userid))->update("userprefs",array("last_login"=>time()));
				$this->ci->mongo_db->where(array("userid"=>$userid))->increment("userprefs",array("login_count"=>1));
			}
		}
		
		public function click_menu($menuitem) {
			$this->ci->mongo_db->where(array("userid"=>1))->increment("userprefs",array($menuitem=>1));
		}
	}
