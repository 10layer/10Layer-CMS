<?php
	/**
	 * Content class
	 * 
	 * @extends Controller
	 */
	class Content extends CI_Controller {

		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			$this->load->library("tluserprefs");
		}
		
		public function contentlist($queueid) {
			$this->db->select("content.*")->from("content")->order_by("last_modified DESC")->limit(100);
			$this->db->select("content_types.urlid AS content_type");
			$this->db->join("content_types", "content.content_type_id=content_types.id");
			$contentqueue=(array) $this->tluserprefs->get_queue($queueid);
			
			if(isset($contentqueue["personal"]) AND $contentqueue["personal"] == "personal"){
				//print_r($contentqueue["includes"]);
				
				$include_items=array(0);
				if (!empty($contentqueue["includes"])) {
					$this->db->where_in("content.id",$contentqueue["includes"]);					
				}else{
					$this->db->where_in("content.id",$include_items);
				}
						

			}else{
					if (!empty($contentqueue["contenttypes"])) {
						$exclude_contenttypes=array();
						foreach($contentqueue["contenttypes"] as $contenttype) {
							if (is_array($contenttype["checked"])) {
								$contenttype["checked"]=array_pop($contenttype["checked"]);
							}
							if (is_array($contenttype["id"])) {
								$contenttype["id"]=array_pop($contenttype["id"]);
							}
							if (empty($contenttype["checked"]) && isset($contenttype["id"])) {
								$exclude_contenttypes[]=$contenttype["id"];
							}
						}
						if (!empty($exclude_contenttypes)) {
							$this->db->where_not_in("content_type_id",$exclude_contenttypes);
						}
					}			
					if (!empty($contentqueue["workflow"])) {
						$exclude_workflow=array();
						foreach($contentqueue["workflow"] as $workflow) {
							if (is_array($workflow["checked"])) {
								$workflow["checked"]=array_pop($workflow["checked"]);
							}
							if (is_array($workflow["major_version"])) {
								$workflow["major_version"]=array_pop($workflow["major_version"]);
							}
							if (empty($workflow["checked"]) && isset($workflow["major_version"])) {
								$exclude_workflow[]=$workflow["id"];
							}
						}
						if (!empty($exclude_workflow)) {
							$this->db->where_not_in("major_version", $exclude_workflow);
						}
					}			
			}
						
			
			
			$query=$this->db->get();
			//echo $this->db->last_query();
						
			print json_encode($query->result());
		}
		
		public function queues($queueid=false) {
			
			//$json = file_get_contents('php://input');
			$json=$this->input->post("model", true);
			
						
			$method=$this->input->post("_method");
			if ($method=="DELETE") {
				$this->tluserprefs->delete_queue($queueid);
			}
			if (!empty($json)) {
				$data=json_decode($json);
				$this->tluserprefs->set_queue_name($data->id, $data->name, $data->order, $data->width, $data->height);
			}
			$queues=$this->tluserprefs->get_queues();
			$holder = array();
			foreach($queues as $q){
				if(!isset($q["order"])){
					$q["order"]=5;
				}
				if(!isset($q["height"])){
					$q["height"]=75;
				}
				if(!isset($q["width"])){
					$q["width"]=230;
				}
				if(!isset($q["personal"])){
					$q["personal"]="";
				}
				
				array_push($holder, $q);
			}
			$queues = $holder;
			
			usort($queues,array($this,"cmp"));		
			
			print json_encode(array_values($queues));
		}
		
		
		function personalise($id,$message){
			$this->tluserprefs->personalise_que($id, $message);
		}
		
		
		function load_recipients(){
			$string = ""; // "<h5>Send this item to...";
			foreach($this->tluserprefs->get_all_users() as $user){
				$string .= "</h5><div class='user_item' id='".$user->id."'>".$user->name."<span class='add_to'>remove</span> <span class='remove_from'>remove</span></div>";
			}
			echo $string;
		}
		
		function send_to($user_id, $item_id){
			$this->tluserprefs->send_to($user_id, $item_id);
		}
		
		function remove_from($user_id, $item_id){
			$this->tluserprefs->remove_from($user_id, $item_id);
		}
		
		
		function set_queue_order(){
			
			$sequence = $this->input->post("selecteds");
			for($i = 0; $i < sizeof($sequence); $i++){
				$this->tluserprefs->save_queue_order($sequence[$i], $i);
			}
			
			echo "Queues reordered successfully";
			
		}
		
		function set_queue_size(){
		
			$sequence = $this->input->post("selecteds");
			
			//print_r(json_decode($sequence));
			
			foreach($sequence as $item){
				$the_item = explode("|",$item);
				$id = $the_item[0];
				$height = $the_item[1];
				$width = $the_item[2];	
				$this->tluserprefs->save_queue_size($id, $height, $width);
			}
		
			echo "Queues resized successfully";
		}
		
		
		
		function cmp($a, $b)
		{
    		return strcmp($a["order"], $b["order"]);
		}
		
		public function contenttypes($queueid) {
			$json=$this->input->post("model", true);
			
			if (!empty($json)) {
				$data=json_decode($json);
				$this->tluserprefs->set_queue($data->queueid, array("contenttypes"=>array($data->urlid=>$data)));
			}
			$queue=json_decode(json_encode($this->tluserprefs->get_queue($queueid))); //Make everything an object
			
			if (isset($queue->contenttypes)) {
				$contentqueue=$queue->contenttypes;
			} else {
				$contentqueue=new stdClass;
			}
			$contenttypes=$this->model_content->get_content_types();
			$returndata=array();
			foreach($contenttypes as $contenttype) {
				$checked=true;
				$urlid=$contenttype->urlid;
				if (isset($contentqueue->$urlid->checked)) {
					$checked=$contentqueue->$urlid->checked;
				}
				$returndata[]=array("urlid"=>$urlid, "name"=>$contenttype->name, "checked"=>$checked, "id"=>$contenttype->id);
			}
			print json_encode($returndata);
		}
		
		public function workflows($queueid) {
			$json=$this->input->post("model", true);
			
			if (!empty($json)) {
				$data=json_decode($json);
				$this->tluserprefs->set_queue($data->queueid, array("workflow"=>array($data->urlid=>$data)));	
			}
			$queue=json_decode(json_encode($this->tluserprefs->get_queue($queueid))); //Make everything an object
			if (isset($queue->workflow)) {
				$contentqueue=$queue->workflow;
			} else {
				$contentqueue=new stdClass;
			}
			$workflows=$this->model_workflow->getAll();
			foreach($workflows as $workflow) {
				$checked=true;
				$urlid=$workflow->urlid;
				if (isset($contentqueue->$urlid->checked)) {
					$checked=$contentqueue->$urlid->checked;
				}
				$returndata[]=array("urlid"=>$urlid, "name"=>$workflow->name, "id"=>$workflow->id, "major_version"=>$workflow->major_version, "checked"=>$checked);
			}
			print json_encode($returndata);
		}
		
		public function update($queueid) {
			$contenttypes=$this->input->post("contenttypes");
			if (!empty($contenttypes)) {
				$cts=json_decode($contenttypes);
				foreach($cts as $ct) {
					$this->tluserprefs->set_queue($queueid, array("contenttypes"=>array($ct->urlid=>$ct)));
				}
			}
		}
		
		/*public function _remap() {
			$vals=$this->uri->uri_to_assoc();
			if (sizeof($vals)==0) {
				return $this->index();
			}
			print_r($vals);
		}*/
	}

/* End of file content.php */
/* Location: ./system/application/controllers/queues/ */