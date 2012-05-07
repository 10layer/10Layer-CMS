<?php
	/**
	 * API class
	 * 
	 * @extends Controller
	 */
	class API extends CI_Controller {
		
		//Return data array
		public $data;
		
		//Switch to false to not output
		protected $_render=true;
		
		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			//$this->output->enable_profiler(true);
			$this->load->library("tlsecurity");
			$this->tlsecurity->ignore_security();
			$this->data=array(
				"error"=>false,
				"timestamp"=>time(),
				"msg"=>"",
				"data"=>array()
			);
		}
		
		public function content($contenttype_urlid, $urlid) {
			$contenttype=$this->db->get_where("content_types",array("urlid"=>$contenttype_urlid))->row();
			if (empty($contenttype->id)) {
				$this->data["error"]=true;
				$this->data["msg"]="Content type $contenttype_urlid not found";
				$this->returndata();
				return true;
			}
			$this->load->model($contenttype->model, "content");
			$obj=$this->content->getByIdORM($urlid, $contenttype_urlid);
			$this->data["data"]=$obj->getFull();
			$this->data["data"]->content_type=$contenttype_urlid;
			$this->returndata();
		}
		
		public function content_cached($contenttype_urlid, $urlid) {
			$this->load->library("memcacher");
			$this->data["data"]=$this->memcacher->getById($contenttype_urlid, $urlid);
			$this->returndata();
		}
		
		public function section($section_urlid) {
			$this->load->model("model_site_sections");
			$section=$this->model_site_sections->getByIdORM($section_urlid);
			$sectiondata=$section->getData();
			if((!isset($sectiondata->zones)) || (!is_array($sectiondata->zones))) {
				$this->data["error"]=true;
				$this->data["msg"]="No zones found for section $section_urlid";
				$this->returndata();
				return true;
			}
			$data=array();
			$this->_render=false;
			foreach($sectiondata->zones as $zone) {
				$this->zone($zone);
				$data[$this->data["zone"]]=$this->data["data"];
			}
			$this->_render=true;
			$this->data["data"]=$data;
			$this->returndata();
		}
		
		public function zone($zone_urlid) {
			$this->load->model("model_zones");
			$zonedata=$this->model_zones->getByIdORM($zone_urlid)->getData();
			$result=$this->db->select("content_types.urlid AS content_type, content.urlid, content.title")->where("zone_urlid",$zonedata->urlid)->order_by("rank ASC")->where("live",true)->join("content","content.id=ranking.content_id")->join("content_types","content_types.id=content.content_type_id")->get("ranking");
			$this->data["data"]=$result->result();
			$this->data["zone"]=$zonedata->urlid;
			$this->returndata();
		}
		
		protected function returndata() {
			if ($this->_render) {
				$this->load->view("api/json",array("data"=>$this->data));
			}
		}
	}

/* End of file api.php */
/* Location: ./system/application/controllers/workers/ */