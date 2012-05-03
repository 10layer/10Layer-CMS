<?php
	/**
	 * Content class
	 * 
	 * @extends Controller
	 */
	class Content extends CI_Controller {
		public $content_filters=array();
		
		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			$this->load->library("tluserprefs");
			$workflows=new TLContentFilter(
				"Workflows", 
				"tl_workflows",
				"name",
				array(
					"join"=>array("tl_workflows", "content.major_version=tl_workflows.major_version"),
					"where_in"=>array("tl_workflows.id","{values}"),
				)
			);
			$content_types=new TLContentFilter(
				"Content Types",
				"content_types",
				"name",
				array(
					"where_in"=>array("content.content_type_id","{values}"),
				)
			);
			$this->content_filters=array($workflows, $content_types);
		}
		
		public function contentfilters($queueid=0) {
			$model=$this->input->post("model");
			if (!empty($model)) { //We've recieved an update
				try {
					$filter=json_decode($model);
					$this->tluserprefs->set_queue($queueid, array("filters"=>array($filter->table=>$filter->options)));
				} catch(Exception $e) {
					//Some error handling here maybe
				}
			}
			for($x=0; $x<sizeof($this->content_filters); $x++) {
				$this->content_filters[$x]->queueid=$queueid;
			}
			$queue=$this->tluserprefs->get_queue($queueid);
			if (!isset($queue["filters"])) {
				print json_encode($this->content_filters);
				return true;
			}
			$filters=$queue["filters"];

			for($x=0; $x<sizeof($this->content_filters); $x++) {
				foreach($filters as $tablename=>$filter) {
					if ($this->content_filters[$x]->table==$tablename) {
						$this->content_filters[$x]->options=$filter;
					}
				}
			}
			print json_encode($this->content_filters);
		}
		
		public function contentlist($queueid) {
			$this->db->select("content.*")->from("content")->order_by("last_modified DESC")->limit(100);
			$this->db->join("content_types","content_types.id=content.content_type_id");
			$this->db->select("content_types.urlid AS content_type");
			$contentqueue=(array) $this->tluserprefs->get_queue($queueid);
			if(isset($contentqueue["personal"]) AND $contentqueue["personal"] == "personal") {
				$include_items=array(0);
				if (!empty($contentqueue["includes"])) {
					$this->db->where_in("content.id",$contentqueue["includes"]);					
				} else {
					$this->db->where_in("content.id",$include_items);
				}
			} else {
				if (isset($contentqueue["filters"]) && !empty($contentqueue["filters"])) {
					$filters=$contentqueue["filters"];
					foreach($filters as $tablename=>$filter) {
						foreach($this->content_filters as $content_filter) {
							if ($tablename==$content_filter->table) {
							//We have a winner!
								$values=array();
								foreach($filter as $f) {
									if (!empty($f["checked"])) {
										$values[]=$f["id"];
									}
								}
								if (!$content_filter->prep_db($values)) {
									//Seems there's an empty value. Let's return nothing.
									print "[]";
									return false;
								}
							}
						}
					}
				}
			}
			$query=$this->db->get();
			//print $this->db->last_query();
			print json_encode($query->result());
		}
		
		public function queues($queueid=false) {
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
				$string .= "<div class='user_item' id='".$user->id."'>".$user->name."<span class='add_to'>send to...</span> <span class='remove_from'>remove from...</span></div>";
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
			foreach($sequence as $item){
				$the_item = explode("|",$item);
				$id = $the_item[0];
				$height = $the_item[1];
				$width = $the_item[2];	
				$this->tluserprefs->save_queue_size($id, $height, $width);
			}
			echo "Queues resized successfully";
		}
		
		function cmp($a, $b) {
    		return strcmp($a["order"], $b["order"]);
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
	}
	
	class TLContentFilter {
		public $label;
		public $table;
		public $dbinfo;
		public $options;
		
		public function __construct($label, $table, $field, $dbinfo) {
			$this->label=$label;
			$this->table=$table;
			$this->field=$field;
			$this->dbinfo=$dbinfo;
			$this->populate_options();
		}
		
		public function populate_options() {
			$ci=&get_instance();
			$result=$ci->db->get($this->table)->result();
			foreach($result as $item) {
				$option=new stdClass();
				$option->id=$item->id;
				$option->urlid=$item->urlid;
				$option->value=$item->{$this->field};
				$option->checked=true;
				$this->options[]=$option;
			}
			return $this;
		}
		
		public function get_options() {
			return $this->options;
		}
		
		public function prep_db($values) {
			if (empty($values)) {
			//This probably means that we won't get any results. Return false so that our caller function knows this.
				return false;
			}
			$ci=&get_instance();
			if (is_array($this->dbinfo)) {
				foreach($this->dbinfo as $key=>$dbitem) {
					if (is_array($dbitem)) {
						for($x=0; $x<sizeof($dbitem); $x++) {
							if ($dbitem[$x]=="{values}") {
								$dbitem[$x]=$values;
							}
						}
					}
					call_user_func_array(array($ci->db, $key), $dbitem);
				}
			}
			return true;
		}
	}

/* End of file content.php */
/* Location: ./system/application/controllers/queues/ */