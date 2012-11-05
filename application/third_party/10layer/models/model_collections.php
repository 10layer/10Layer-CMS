<?php
	
	/**
	 * Model_Collections class.
	 * 
	 * @extends Model
	 * @package 10Layer
	 * @subpackage Models
	 */
	class Model_Collections extends CI_Model {
	
		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
		}
		
		public function getAll() {
			return $this->db->get_where("content_types",array("collection"=>true))->result();
		}
		
		public function get($id) {
			if (is_numeric($id)) {
				return $this->db->get_where("content_types",array("collection"=>true, "id"=>$id))->row();
			} else {
				return $this->db->get_where("content_types",array("collection"=>true, "urlid"=>$id))->row();
			}
		}



		/**
		 * get_content_map function.
		 * 
		 * Returns a named array of all the content based on the content_type
		 *
		 * @access public
		 * @param int $content_type_id
		 * @return void
		 */
		public function get_content_map($content_type_id) {
		
			$result=array();
			$query=$this->db->select("content.urlid, content.title, content.id AS content_id")->from("content")->where("content_type_id", $content_type_id)->order_by("content.title", "asc")->get();
		
			$ancestors = array();
			
			foreach($query->result() as $item){
				if($this->is_ancestor($item->content_id)){
					array_push($ancestors,$item);
				}
			}
			
			foreach($ancestors as $parent){
				$children = $this->get_children($parent->content_id);
				foreach($children as $child){
					$parent->children[] = $child;
				}
			}
			return $ancestors;
		}


		/**
		 * is_ancestor function.
		 * 
		 * @access public
		 * @param mixed $content_id
		 * @return void
		 */
		public function is_ancestor($content_id){
		
			$parents = $this->db->query("select content_id, title, content_link_id from content_content join content on content.id = content_content.content_id where content.content_type_id = 11 and content_link_id = $content_id")->result();
			
			if(sizeof($parents) > 0){
				return false;
			}else{
				return true;
			}
		
		}

		/**
		 * get_children function.
		 * 
		 * @access public
		 * @param mixed $parent_id
		 * @return void
		 */
		public function get_children($parent_id){
			$sql = "select title,urlid,content_id,content_link_id from content_content join content on content.id = content_content.content_link_id where content_content.content_id = {$parent_id} and content.content_type_id = 11 order by title asc;";
			$children = $this->db->query($sql)->result();
			return $children; 
		}


		public function get_content_items($content_type_id, $start, $limit){
			$query=$this->db->select("content.urlid, content.title, content.id AS content_id")->from("content")->where("content_type_id", $content_type_id)->order_by("content.title", "asc")->limit($limit, $start)->get();
			return $query->result();
		}

		public function count_items($content_type_id){
			$sql = 'select count(*) as number from content where content_type_id = '.$content_type_id;
			return $this->db->query($sql)->row()->number;
		}

		function get_collection_item($urlid){
			$sql = 'select * from content where urlid = "'.$urlid.'"';
			return $this->db->query($sql)->row();
		}

		function save_zone($data, $zone_id=''){
			if($zone_id != ''){
				$this->db->where('content_id', $zone_id);
				$this->db->update('section_zones', $data);
			}else{
				$this->db->insert('section_zones',$data);
				return $this->db->insert_id();
			}
		}

		function get_parent_collection($item_id){
			$sql = 'select * from content where id = '.$item_id;
			return $this->db->query($sql)->row();
		}

		function save_new_zone($data){
			$this->db->insert('content',$data);
			return $this->db->insert_id();
		}

		function associate($parent, $child){
			$sql = 'insert into content_content set content_id = '.$parent.', content_link_id = '.$child;
			$this->db->query($sql);
		}






	}

/* End of file model_collections.php */
/* Location: ./system/application/third_party/10layer/models/model_collections.php */