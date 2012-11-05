<?php
	/**
	 * Collections class
	 * 
	 * Handles the configuration of sections
	 *
	 * @extends Controller
	 * @package 10Layer
	 * @subpackage Controllers
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
		
		public function collection($urlid, $start = 0) {
			$collection=$this->model_collections->get($urlid);
			$data["menu1_active"]="manage";
			$data["menu2_active"]="manage/collections";
			if($collection->nested == 1){
				$collection_map = $this->model_collections->get_content_map($collection->id);
				$data['collections'] = $collection_map;
				$data['collection'] = $collection;
				$this->load->view('templates/header',$data);
				$this->load->view("manage/collections/nested_item_selector");
				$this->load->view("templates/footer");
		
			}else{
				$this->load->library('pagination');
				$config['base_url'] = '/manage/collections/collection/'.$urlid.'/';
				$config['uri_segment'] = 5;
				$config['total_rows'] = $this->model_collections->count_items($collection->id);;
				$config['per_page'] = 100; 

				$this->pagination->initialize($config); 

				$data['pagination'] = $this->pagination->create_links();
				$collection_items = $this->model_collections->get_content_items($collection->id, $start, $config['per_page']);
				$data['collections'] = $collection_items;
				$data['collection'] = $collection;
				$this->load->view('templates/header',$data);
				$this->load->view("manage/collections/item_selector");
				$this->load->view("templates/footer");
			}

		}



		public function manage_item($urlid){
		
			$data["menu1_active"]="manage";
			$data["menu2_active"]="manage/collections";
			$item=$this->model_collections->get_collection_item($urlid);

			$zones=array();
			//pull zones directly
			$the_zones = $this->db->query("select c.id, c.title, c.urlid from content c join content_content cc on c.id = cc.content_link_id where  c.content_type_id = 21 and cc.content_id = ". $item->id)->result();
			//echo $this->db->last_query();
			if(sizeof($the_zones) > 0){
				foreach($the_zones as $zone){
					$zones[]=$this->zones->getByIdORM($zone->id)->getData();
				}
			}
				
			$data['item'] = $item;
			$data["zones"]=$zones;
			$this->load->view('templates/header',$data);
			$this->load->view("manage/collections/collection_config");
			$this->load->view("templates/footer");

		}

		function configure_zone($urlid=""){
			$data['content_types'] = $this->db->query("select * from content_types")->result();
			if($urlid != ''){
				$sql = "select * from content join section_zones on section_zones.content_id = content.id where content.urlid = '".$urlid."'";
				$data['zone'] = $this->db->query($sql)->row();
				$this->load->view('manage/collections/zone_config', $data);
			}else{
				$this->load->view('manage/collections/zone_config',$data);
			}
		}

		function save_zone($parent_item_id){

			$zone_id = $this->input->post('id');

			if($zone_id == ''){
				//create a new zone
				//find the parent item
				$parent_item = $this->model_collections->get_parent_collection($parent_item_id);

			
				$content['urlid'] = $parent_item->urlid.'-'.url_title($this->input->post('zone_title'));
				$content['title'] = $this->input->post('zone_title');
				$content['live'] = 1;
				$content['major_version'] = 4;
				$content['minor_version'] = 1;
				$content['content_type_id'] = 21;
				$new_zone_id = $this->model_collections->save_new_zone($content);
				//add zone to the content
				$this->model_collections->associate($parent_item_id, $new_zone_id);
				$zone['content_id'] = $new_zone_id;
				$zone['max_count'] = $this->input->post('max_items');
				$zone['min_count'] = $this->input->post('min_items');
				$zone['auto'] = $this->input->post('zone_auto_selector');
				$content_types = implode(',', $this->input->post('content_types'));
				$zone['content_types'] = $content_types;
				$this->model_collections->save_zone($zone);

				$results['title'] = $content['title'];
				$results['urlid'] = $content['urlid'];
				$results['message'] = "Zone saved...";
				echo json_encode($results);
				
				
			}else{
				$data['max_count'] = $this->input->post('max_items');
				$data['min_count'] = $this->input->post('min_items');
				$content_types = implode(',', $this->input->post('content_types'));
				$data['content_types'] = $content_types;
				$this->model_collections->save_zone($data,$zone_id);
				$results['message'] = "Zone saved...";
				echo json_encode($results);
			}







			// $this->ci->load->model("model_content", "column1");
			// $this->ci->column1->setContentType("zones");
			// $data["content_title"]="$urlid Left Column";
			// $data["section_zones_content_types"]="article";
			// $result[]=$this->ci->column1->create($data)->getData()->content_id;
			
			
			// $this->ci->load->model("model_content", "column2");
			// $this->ci->column2->setContentType("zones");
			// $data["content_title"]="$urlid Middle Column";
			// $data["section_zones_content_types"]="picture,specialreport,promo,podcast,slideshow,video";
			// $result[]=$this->ci->column2->create($data)->getData()->content_id;
			
			// $query=$this->ci->db->get_where("content", array("urlid"=>$urlid));
			// $content_id=$query->row()->id;
			// foreach($result as $content_link_id) {
			// 	$this->ci->db->insert("content_content", array("content_id"=>$content_id, "content_link_id"=>$content_link_id));
			// }

			//print_r($this->input->post());
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
			//pull zones directly
			$the_zones = $this->db->query("select c.id, c.title, c.urlid from content c join content_content cc on c.id = cc.content_link_id where  c.content_type_id = 21 and cc.content_id = ". $section->content_id)->result();
			
			//echo $this->db->last_query();
			
			if(sizeof($the_zones) > 0){
				foreach($the_zones as $zone){
					$zones[]=$this->zones->getByIdORM($zone->id)->getData();
				}
			}
				
			$data["zones"]=$zones;
			
			//print_r($zones); die();
			
			$this->load->view('templates/header',$data);
			$this->load->view("manage/sections/section_config");
			$this->load->view("templates/footer");
		}
		
		public function dosave($urlid) {
			$returndata=array("error"=>false,"msg"=>"");
			$section=$this->sections->getByIdORM($urlid);
			$data=$section->getData();
			//Find and delete existing Zones
			if (is_array($data->zones)) {
				foreach($data->zones as $zone) {
					$this->db->where("content_link_id",$zone)->delete("content_content");
					$this->db->where("content_id",$zone)->delete("content_content");
					$this->db->where("content_id",$zone)->delete("section_zones");
					$this->db->where("id",$zone)->delete("content");
				}
			}
			//Add new zones
			$titles=$this->input->post("content_title");
			$max=sizeof($titles);
			$content_ids=array();
			$contentobj=new TLContent();
			
			$contentobj->setContentType("zones");
			
			for($x=0;$x<$max;$x++) {
				$contentobj->clearData();
				foreach($contentobj->getFields() as $field) {
					$fieldval=$this->input->post($field->tablename."_".$field->name);
					if (empty($fieldval)) {
						$contentobj->{$field->name}="";
					} else {
						$contentobj->{$field->name}=$fieldval[$x];
					}
				}
				$contentobj->transformFields();
				$validation=$contentobj->validateFields();
				if (!$validation["passed"]) {
					$returndata["error"]=true;
					$returndata["msg"]="Failed to create {$this->_contenttypeurlid}";
					$returndata["info"]=implode("<br />\n",$validation["failed_messages"]);
				} else {
					
					$contentobj->insert();
					$content_ids[]=$contentobj->getData()->content_id;
				}
			}
			//Link them
			if (!$returndata["error"]) {
				foreach($content_ids as $content_id) {
					$this->db->insert("content_content", array("content_id"=>$data->content_id, "content_link_id"=>$content_id));
				}
				print "<script>document.domain=document.domain;</script><textarea>";
				print json_encode($returndata);
				print "</textarea>";
			}
		}

	}
?>