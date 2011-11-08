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
		protected $userid;
		protected $data;
		
		public function __construct() {
			$this->ci=&get_instance();
			$this->ci->load->library("mongo_db");
			$this->userid=$this->ci->session->userdata("id");
			$this->get_data();
		}
		
		public function user_setup() {
			if (empty($this->data->userid)) {
				$this->ci->mongo_db->insert("userprefs",array("userid"=>$this->userid, "last_login"=>time(), "login_count"=>1));
			} else {
				$this->ci->mongo_db->where(array("userid"=>$this->userid))->update("userprefs",array("last_login"=>time()));
				$this->ci->mongo_db->where(array("userid"=>$this->userid))->increment("userprefs",array("login_count"=>1));
			}
		}
		
		public function click_menu($menuitem) {
			if (empty($this->data->menus)) {
				$types=$this->ci->model_content->get_content_types();
				$data=array();
				foreach($types as $type) {
					$data[$type->urlid]["click_count"]=0;
				}
				$this->ci->mongo_db->where(array("userid"=>$this->userid))->update("userprefs",array("menus"=>$data));
				$this->get_data();
			}
			$data=$this->data->menus;
			if (!empty($data[$menuitem]["click_count"])) {
				$data[$menuitem]["click_count"]++;
				$data[$menuitem]["last_click"]=time();
			} else {
				$data[$menuitem]["click_count"]=1;
				$data[$menuitem]["last_click"]=time();
			}
			$this->ci->mongo_db->where(array("userid"=>$this->userid))->update("userprefs",array("menus"=>$data));
			$this->get_data();
			//$this->ci->mongo_db->where(array("userid"=>$this->userid))->increment("userprefs",array($menuitem=>1));
		}
		
		public function get_menus() {
			if (!isset($this->data->menus)) {
				return false;
			}
			return $this->data->menus;
		}
		
		public function get_last_menu() {
			$menus=$this->get_menus();
			if (empty($menus)) {
				return false;
			}
			$latest=0;
			$item=false;
			foreach($menus as $key=>$menu) {
				if (isset($menu["last_click"]) && $menu["last_click"]>$latest) {
					$item=$key;
					$latest=$menu["last_click"];
				}
			}
			return $item;
		}
		
		public function get_menus_order() {
			$menus=$this->get_menus();
			$sortnames=array();
			$sortvals=array();
			foreach($menus as $key=>$menu) {
				$sortnames[]=$key;
				$sortvals[]=$menu;
			}
			array_multisort($sortvals, SORT_DESC, $sortnames);
			return $sortnames;
		}
		
		protected function get_data() {
			$data=$this->ci->mongo_db->where(array("userid"=>$this->userid))->get("userprefs");
			if (empty($data)) {
				$this->data=false;
				return false;
			}
			$this->data=$data[0];
		}
	}
