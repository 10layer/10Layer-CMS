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
			$query=$this->db->get();
			print json_encode($query->result());
		}
		
		public function queues() {
			$json=$this->input->post("model", true);
			if (!empty($json)) {
				$data=json_decode($json);
				$this->tluserprefs->set_queue_name($data->order, $data->name);
			}
			$queues=$this->tluserprefs->get_queues();
			print json_encode(array_values($queues));
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