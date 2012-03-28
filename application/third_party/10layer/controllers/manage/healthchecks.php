<?php
	/**
	 * Healthchecks class
	 * 
	 * @extends Controller
	 */
	class Healthchecks extends CI_Controller {

		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
		}
		
		public function index() {
			$data["menu1_active"]="manage";
			$data["menu2_active"]="manage/healthchecks";
			$checks=array(
				array(
					"title"=>"Services",
					"result"=>$this->_services()
				),
				array(
					"title"=>"Directories",
					"result"=>$this->_directories()
				),
				array(
					"title"=>"Database",
					"result"=>$this->_database()
				),
				array(
					"title"=>"Content Types",
					"result"=>$this->_contenttypes()
				),
				/*array(
					"title"=>"Memcache",
					"result"=>$this->_memcache()
				),*/
			);
			
			$data["checks"]=$checks;
			$this->load->view('templates/header',$data);
			$this->load->view("manage/healthchecks/report");
			$this->load->view("templates/footer");
		}
		
		public function check($type) {
			
		}
		
		protected function _directories() {
			$required_dirs=array(
				"/resources/cache",
				"/resources/cache/pictures",
				"/resources/cache/pictures/cdn",
				"/resources/uploads",
				"/resources/uploads/files",
				"/resources/uploads/files/original",
			);
			$error_dirs=array();
			foreach($required_dirs as $dir) {
				if (!is_dir(".".$dir)) {
					$error_dirs["Directory Missing"][]=$dir;
				} elseif (!is_writable(".".$dir)) {
					$error_dirs["Directory Read Only"][]=$dir;
				}
			}
			return $error_dirs;
		}
		
		protected function _database() {
			//System Tables
			$required_tables=array(
				"content",
				"content_content",
				"content_platforms",
				"content_types",
				"content_workflows",
				"platforms",
				"ranking",
				"section_zones",
				"site_sections",
				"tl_permissions",
				"tl_permissions_urls",
				"tl_permissions_users_link",
				"tl_platform_types",
				"tl_roles",
				"tl_roles_users_link",
				"tl_roles_workflows_link",
				"tl_security_exclude_paths",
				"tl_users",
				"tl_user_queue",
				"tl_user_status",
				"tl_workflows",
			);
			
			$error_tables=array();
			foreach($required_tables as $table) {
				if (!$this->db->table_exists($table)) {
					$error_tables["Core Table Missing"][]=$table;
				}
			}
			
			//Content Type Tables
			$content_tables=array();
			if ($this->db->table_exists("content_types")) {
				$result=$this->db->get("content_types");
				foreach($result->result() as $row) {
					$content_tables[]=$row->table_name;
				}
			}
			foreach($content_tables as $table) {
				if (!$this->db->table_exists($table)) {
					$error_tables["Content Type Table Missing"][]=$table;
				} else {
					if (!$this->db->field_exists("content_id",$table)) {
						$error_tables["Field 'content_id' Missing"][]=$table;
					}
				}
			}
			return $error_tables;
		}
		
		protected function _contenttypes() {
			$error_cts=array();
			$result=$this->db->get("content_types");
			foreach($result->result() as $row) {
				if (!$this->exists->model($row->model)) {
					$error_cts["Missing Model"][]=$row->model;
				} else {
					$this->load->model($row->model);
					if ($this->{$row->model}->error) {
						$error_cts["Model Error: ".$this->{$row->model}->errormsg][]=$row->model;
					} else {
						foreach($this->{$row->model}->fields as $field) {
							$tablename=$row->table_name;
							if (!empty($field["tablename"])) {
								$tablename=$field["tablename"];
							}
							if (!isset($field["contenttype"])) {
								$field["contenttype"]="";
							}
							if ($tablename==$row->table_name && !isset($field["link"]) && ($field["contenttype"]!="mixed")) {
								if ($this->db->table_exists($tablename)) {
									if (!$this->db->field_exists($field["name"],$tablename)) {
										$error_cts["Field '".$field["name"]."' Missing from ".$tablename][]=$row->model;
									}
								}
							}
						}
					}
				}
			}
			return $error_cts;
		}
		
		protected function _services() {
			$error_services=array();
			if ($this->messaging->error) {
				$error_services["Orbited"][]=$this->messaging->errormsg;
			}
			$this->load->library("cdn");
			$this->cdn->init();
			if ($this->cdn->hasError()) {
				$error_services["CDN"][]=$this->cdn->lastError();
			}
			if ($this->mongo_db->error) {
				$error_services["MongoDB"][]=$this->mongo_db->errormsg;
			}
			return $error_services;
		}
		
		protected function _memcache() {
			$this->load->library("memcacher");
			$error_memcache=array();
			$online_servers=$this->memcacher->isOnline();
			foreach($online_servers as $server=>$online) {
				if (!$online) {
					$error_message[$server][]="Not online";
				}
			}
			return $error_memcache;
		}
		
	}

/* End of file healthchecks.php */
/* Location: ./system/application/controllers/manage/healthchecks */