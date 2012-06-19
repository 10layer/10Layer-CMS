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
		
		public function relations_right($urlid, $content_type, $content_link_type, $limit=false, $offset=false) {
			$query=$this->db->get_where("content_types", array("urlid"=>$content_type))->row();
			if (!isset($query->id)) {
				$this->data["error"]=true;
				$this->data["msg"]="Content type $content_type not found";
				$this->returndata();
				return true;
			}
			$type1=$query->id;
			$query=$this->db->get_where("content_types", array("urlid"=>$content_link_type))->row();
			if (!isset($query->id)) {
				$this->data["error"]=true;
				$this->data["msg"]="Content type $content_type not found";
				$this->returndata();
				return true;
			}
			$type2=$query->id;
			if ($limit!==false) {
				$this->db->limit($limit, $offset);
			}
			$result=$this->db->select('content2.*')->from('content')->join('content_content','content.id=content_content.content_id')->join('content AS content2', 'content_content.content_link_id=content2.id')->where('content.urlid', $urlid)->where('content2.content_type_id',$type2)->where('content.content_type_id',$type1)->get()->result();
			$this->data["count"]=$this->db->select('content2.*')->from('content')->join('content_content','content.id=content_content.content_id')->join('content AS content2', 'content_content.content_link_id=content2.id')->where('content.urlid', $urlid)->where('content2.content_type_id',$type2)->where('content.content_type_id',$type1)->get()->num_rows();
			$this->data["data"]=$result;
			$this->returndata();
			return true;
		}
		
		public function relations_left($urlid, $content_type, $content_link_type, $limit=false, $offset=false) {
			$query=$this->db->get_where("content_types", array("urlid"=>$content_type))->row();
			if (!isset($query->id)) {
				$this->data["error"]=true;
				$this->data["msg"]="Content type $content_type not found";
				$this->returndata();
				return true;
			}
			$type1=$query->id;
			$query=$this->db->get_where("content_types", array("urlid"=>$content_link_type))->row();
			if (!isset($query->id)) {
				$this->data["error"]=true;
				$this->data["msg"]="Content type $content_type not found";
				$this->returndata();
				return true;
			}
			$type2=$query->id;
			if ($limit!==false) {
				$this->db->limit($limit, $offset);
			}
			$result=$this->db->select('content.*')->from('content')->join('content_content','content.id=content_content.content_id')->join('content AS content2', 'content_content.content_link_id=content2.id')->where('content2.urlid', $urlid)->where('content2.content_type_id',$type1)->where('content.content_type_id',$type2)->get()->result();
			$this->data["count"]=$this->db->select('content.*')->from('content')->join('content_content','content.id=content_content.content_id')->join('content AS content2', 'content_content.content_link_id=content2.id')->where('content2.urlid', $urlid)->where('content2.content_type_id',$type1)->where('content.content_type_id',$type2)->get()->num_rows();
			$this->data["data"]=$result;
			
			$this->returndata();
			return true;
		}
		
		public function get_batch() {
			$urlids=$this->input->get_post('urlid');
			if (is_array($urlids)) {
				$this->db->where_in('content.urlid',$urlids);
			} elseif (is_array($ids)) {
				$this->db->where_in('content.id',$ids);
			}
			$rows=$this->db->limit(1000)->get('content')->result();
			$result=array();
			foreach($rows as $item) {
				$result[]=$this->model_content->getByIdORM($item->id)->getData();
			}
			$this->data=$result;
			$this->returndata();
			return true;
		}
		
		public function update($content_type, $urlid, $api_key) {
			$api_key=trim($api_key);
			$comp_api_key=$this->config->item('api_key');
			if (!empty($api_key) && ($comp_api_key != $api_key)) {
				header('HTTP/1.1 401 Access Denied');
				die();
			}
			require_once(APPPATH.'third_party/10layer/system/TL_Controller_Crud.php');
			$tlcontroller=new TL_Controller_Edit();
			$result=$tlcontroller->submit($content_type, $urlid);
			$this->data=$result;
			$this->returndata();
			return true;
		}
		
		protected function returndata() {
			if ($this->_render) {
				$this->load->view("json",array("data"=>$this->data));
			}
		}
	}

/* End of file api.php */
/* Location: ./system/application/controllers/workers/ */