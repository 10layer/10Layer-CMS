<?php
	/**
	 * Collections class
	 * 
	 * Handles the configuration of sections
	 *
	 * @extends Controller
	 */
	class Collections extends CI_Controller {
		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			$this->load->model("model_collections");
			$this->load->model("model_zones","zones");
		}
		
		public function index() {
			$data["menu1_active"]="manage";
			$data["menu2_active"]="manage/collections";
			$data["collections"]=$this->model_collections->getAll();
			$this->load->view('templates/header',$data);
			$this->load->view("manage/sections/collections");
			$this->load->view("templates/footer");
		}
		
		public function collection($urlid) {
			$collection=$this->model_collections->get($urlid);
			$this->load->model($collection->model,"sections");
			$data["sections"]=$this->sections->getAll();
			$data["collectionurlid"]=$collection->urlid;
			$data["menu1_active"]="manage";
			$data["menu2_active"]="manage/collections";
			$this->load->view('templates/header',$data);
			$this->load->view("manage/sections/section_chooser");
			$this->load->view("templates/footer");
		}
		
		public function section($collectionurlid,$urlid) {
			$collection=$this->model_collections->get($collectionurlid);
			$this->load->model($collection->model,"sections");
			$data["menu1_active"]="manage";
			$data["menu2_active"]="manage/collections";
			$section=$this->sections->getByIdORM($urlid);
			$data["section"]=$section;
			$data["content_types"]=$this->model_content->get_content_types();
			$zones=array();
			if (!empty($section->getData()->zones)) {
				foreach($section->getData()->zones as $zone) {
					$zones[]=$this->zones->getByIdORM($zone)->getData();
				}
			}
			$data["zones"]=$zones;
			$this->load->view('templates/header',$data);
			$this->load->view("manage/sections/section_config");
			$this->load->view("templates/footer");
		}
	}
?>