<?php
	require_once(APPPATH.'third_party/10layer/system/Model_Crud.php');
	require_once(APPPATH.'third_party/10layer/system/TL_Content.php');
	
	/**
	 * Model_Content class.
	 * 
	 * Slimmed down Content Model that forms the base of custom and generic content types.
	 *
	 * @extends CI_Model
	 */
	class Model_Content extends CI_Model {
		/**
		 * content_type
		 * 
		 * Content type object (default value: false)
		 * 
		 * @var object
		 * @access protected
		 */
		protected $content_type=false;
		/**
		 * platform
		 * 
		 * Platform object (default value: false)
		 * 
		 * @var object
		 * @access protected
		 */
		protected $platform=false;
		
		/**
		 * order_by
		 * 
		 * Array to order lists by. Tail with "desc" to order descending. 
		 *
		 * @var mixed
		 * @access protected
		 */
		public $order_by=array();
		 
		public $_default_order_by=array(
			"content.start_date DESC",
			"content.last_modified DESC"
		);
		/**
		 * limit
		 * 
		 * Limit on lists (default value: 100)
		 * 
		 * @var int
		 * @access protected
		 */
		protected $limit=100;
		
		/**
		 * start
		 * 
		 * Start value on lists (default value: 0)
		 *
		 * @var int
		 * @access protected
		 */
		protected $start=0;
		
		/**
		 * error
		 * 
		 * Set to true if we encounter an error - primarily 
		 * for healthcheck, else we just throw the error
		 *
		 * @var boolean
		 * @access public
		 * @default false
		 */
		public $error=false;
		
		/**
		 * errormsg
		 * 
		 * Set to message if we encounter an error - primarily 
		 * for healthcheck, else we just throw the error
		 *
		 * @var string
		 * @access public
		 * @default false
		 */
		public $errormsg="";
		
		/**
		 * fields
		 * 
		 * An array of fields. See documentation for more details
		 *
		 * @var array
		 * @access public
		 */
		public $fields=array();
		
		public $_default_fields=array(
			array(
				"name"=>"id",
				"type"=>"hidden",
				"tablename"=>"content",
			),
			array(
				"name"=>"urlid",
				"tablename"=>"content",
				"hidden"=>true,
				"transformations"=>array(
					"copy"=>"title",
					"urlid"=>"content.urlid",
				),
			),
			array(
				"name"=>"title",
				"tablename"=>"content",
				"class"=>"bigger",
				"label_class"=>"bigger",
				"rules"=>array(
					"required",
				),
				"libraries"=>array(
					"semantic"=>true,
					"search"=>"like",
				),
				"type"=>"textarea"
			),
			array(
				"name"=>"last_modified",
				"tablename"=>"content",
				"hidden"=>true,
				"transformations"=>array(
					"date('c')",
				),
			),
			array(
				"name"=>"live",
				"tablename"=>"content",
				"type"=>"checkbox",
			),
			array(
				"name"=>"start_date",
				"tablename"=>"content",
				"type"=>"date",
				"value"=>'Today',
			),
			array(
				"name"=>"end_date",
				"tablename"=>"content",
				"type"=>"date",
				"value"=>"2100-01-01",
			),
		);
		
		
		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct($content_type=false) {
			parent::__construct();
			if (!empty($content_type)) {
				$this->setContentType($content_type);
			}
			$this->fields=array_merge($this->_default_fields, $this->fields);
			if (empty($this->order_by)) {
				$this->order_by=$this->_default_order_by;
			}
		}
		
		/**
		 * get function.
		 * 
		 * If you put in an id number or urlid it'll return a single item, else it'll return a list
		 *
		 * @access public
		 * @param int $id. (default: false)
		 * @return object
		 */
		public function get($id=false) {
			if (empty($id)) {
				return $this->getAll();
			} else {
				return $this->getById($id);
			}
		}
		
		/**
		 * setContentType function.
		 * 
		 * Sets this instance's content type. Takes ID or urlid.
		 *
		 * @access public
		 * @param mixed $content_type
		 * @return object this
		 */
		public function setContentType($content_type) {
			
			if (is_numeric($content_type)) {
				$query=$this->db->get_where("content_types",array("id"=>$content_type));
			} else {
				$query=$this->db->get_where("content_types",array("urlid"=>$content_type));
			}
			if ($query->num_rows()==0) {
				if ($this->uri->segment(2)!="healthchecks") { //If we're running the healthcheck, don't throw an error
					show_error("Could not find content type $content_type");
				} else {
					$this->error=true;
					$this->errormsg="Could not find content type $content_type";
				}
				return false;
			}
			$this->content_type=$query->row();
			return $this;
		}
		
		/**
		 * setPlatform function.
		 * 
		 * Sets this instance's platform. Takes ID or urlid.
		 *
		 * @access public
		 * @param mixed $platform
		 * @return object this
		 */
		public function setPlatform($platform) {
			if (is_numeric($platform)) {
				$query=$this->db->get_where("platforms",array("id"=>$platform));
			} else {
				$query=$this->db->get_where("platforms",array("urlid"=>$platform));
			}
			if ($query->num_rows()==0) {
				show_error("Could not find platform type $platform");
				return false;
			}
			$this->platform=$query->row();
			return $this;
		}
		
		/**
		 * getById function.
		 * 
		 * Gets an item by its id or urlid
		 *
		 * @access public
		 * @param mixed $id
		 * @return object
		 */
		public function getById($id) {
			return $this->getContent($id);
		}
		
		/**
		 * getByIdORM function.
		 * 
		 * Gets an item by id or urlid but returns the ORM representation
		 *
		 * @access public
		 * @param mixed $id
		 * @return TLContent
		 */
		public function getByIdORM($id, $contenttype_id=false) {
			$content=new TLContent($id, $contenttype_id);
			/*if (!empty($this->content_type)) {
				$content->setContentType($this->content_type->urlid);
			}*/
			return $content;
		}
		
		/**
		 * checkContentType function.
		 * 
		 * Returns this object's content_type by content id
		 *
		 * @access public
		 * @param mixed $id
		 * @return object
		 */
		public function checkContentType($id) {
			$this->db->select("content_types.*");
			$this->db->from("content_types");
			if (is_numeric($id)) {
				$this->db->where("content.id",$id);
			} else {
				$this->db->where("content.urlid",$id);
			}
			$this->db->join("content","content.content_type_id=content_types.id");
			$query=$this->db->get();
			$this->content_type=$query->row();
			return $this->content_type;
		}
		
		/**
		 * getAll function.
		 * 
		 * Returns a list of content
		 *
		 * @access public
		 * @param int $limit. (default: false)
		 * @param int $start. (default: false)
		 * @return object
		 */
		public function getAll($limit=false, $start=false) {
			
			$selecteds=$this->input->get("selected");		
			if(!empty($selecteds))
			{
				
				//$selecteds = $this->input->get("selected",TRUE);
				//print_r($selecteds);
				$this->db->where_not_in("content.id",$selecteds);
			}
			$this->limit($limit, $start);
			$this->_prepGetAllQuery();
			$this->db->group_by("content.urlid");
			$query=$this->db->get("content");
			
			return $query->result();
		}
		
		/**
		 * getContent function.
		 * 
		 * Gets content by ID or urlid
		 *
		 * @access public
		 * @param mixed $id
		 * @return object
		 */
		public function getContent($id) {
			if (is_numeric($id)) {
				$query=$this->db->get_where("content",array("id"=>$id));
			} else {
				$query=$this->db->get_where("content",array("urlid"=>$id));
			}
			return $query->row();
		}
		
		/**
		 * getContentType function.
		 * 
		 * Gets the content_type
		 *
		 * @access public
		 * @param mixed $id
		 * @return object
		 */
		public function getContentType($id) {
			if (empty($id)) {
				$query=$this->db->get_where("content_types",array("id"=>$this->getField("content_type_id")->value));
			} elseif (is_numeric($id)) {
				$query=$this->db->get_where("content_types",array("id"=>$id));
			} else {
				$query=$this->db->get_where("content_types",array("urlid"=>$id));
			}
			return $query->row();
		}
		
		/**
		 * count function.
		 * 
		 * Returns number of results we can expect from a list
		 *
		 * @access public
		 * @param bool $extensions. (default: false)
		 * @return int
		 */
		public function count($extensions=false) {
			$this->_prepQuery();
			if ($extensions) {
				$this->db->join($this->content_type->table_name, "content.id={$this->content_type->table_name}.content_id");
			}
			$this->db->select("COUNT(*) AS count",false);
			$query=$this->db->get("content");
			return $query->row()->count;
		}
		
		/**
		 * limit function.
		 * 
		 * Sets a limit on our queries
		 *
		 * @access public
		 * @param bool $limit. (default: false)
		 * @param bool $start. (default: false)
		 * @return object this
		 */
		public function limit($limit=false, $start=false) {
			if (!empty($limit)) {
				$this->limit=$limit;
			}
			if (!empty($start)) {
				$this->start=$start;
			}
			return $this;
		}
		
		/**
		 * addJoin function.
		 * 
		 * DEPRECATED
		 *
		 * @access public
		 * @param mixed $table
		 * @param string $field. (default: "id")
		 * @return object this
		 */
		public function addJoin($table,$field="id") {
			if (!in_array($table,$this->join_tables) && ($table!="content")) {
				$tmptables=array($table, $this->content_type->table_name);
				sort($tmptables);
				$pivot_table=implode("_",$tmptables);
				$this->join_tables[]=array("tablename"=>$table,"field"=>$field,"pivot"=>$pivot_table);
			}
			return $this;
		}
		
		/**
		 * clearJoins function.
		 * 
		 * DEPRECATED
		 *
		 * @access public
		 * @return void
		 */
		public function clearJoins() {
			$this->join_tables=array();
		}
				
		protected function _prepQuery() {
			if (!empty($this->content_type)) {
				$this->db->where("content.content_type_id",$this->content_type->id);
			}
			if (!empty($this->platform)) {
				$this->db->join("content_platforms","content.id=content_platforms.content_id");
				$this->db->where("content_platforms.platform_id",$this->platform->id);
			}
		}
		
		protected function _prepGetAllQuery() {
			$this->_prepQuery();
			$this->db->select("content.*");
			foreach($this->order_by as $ob) {
				$this->db->order_by($ob);
			}
			$this->db->limit($this->limit, $this->start);
		}
		
		/**
		 * searchCount function.
		 * 
		 * Returns the count of a search
		 *
		 * @access public
		 * @param mixed $content_type
		 * @param mixed $searchstr
		 * @return int
		 */
		public function searchCount($content_type, $searchstr) {
			$this->setContentType($content_type);
			$this->setPlatform($this->platforms->id());
			$tables=array("content");
			$contentobj=new TLContent();
			$contentobj->setContentType($content_type);
			$fields=$contentobj->getFields();
			$matches=array();
			$likes=array();
			$fields=$contentobj->getFields();
			foreach($fields as $field) {
				if (isset($field->libraries["search"])) {
					if ($field->libraries["search"]=="fulltext") {
						$matches[]=$field->tablename.".".$field->name;
					} elseif ($field->libraries["search"]=="like") {
						$likes[]=$field->tablename.".".$field->name;
					}
					if (!in_array($field->tablename,$tables)) {
						$tables[]=$field->tablename;
					}
				}
			}
			if (!empty($matches)) {
				$this->db->or_where("MATCH (".implode(",",$matches).") AGAINST (".$this->db->escape($searchstr).")",false, false);
			}
			foreach($likes as $like) {
				$this->db->or_where($like." LIKE '%".$searchstr."%'");
			}			
			foreach($tables as $table) {
				if ($table!="content") {
					$this->db->join($table, "content.id=$table.content_id");
				}
			}
			
			//$this->_prepQuery();
			
			return $this->count();
		}
		
		/**
		 * search function.
		 * 
		 * Returns the results of a search
		 *
		 * @access public
		 * @param string $content_type
		 * @param string $searchstr
		 * @param int $limit
		 * @param int $start. (default: 0)
		 * @return object
		 */
		public function search($content_type, $searchstr,$limit,$start=0) {
		
			
			
			$tables=array("content");
			$this->setContentType($content_type);
			$this->setPlatform($this->platforms->id());
			$contentobj=new TLContent();
			$contentobj->setContentType($content_type);
			$fields=$contentobj->getFields();
			$matches=array();
			$likes=array();
			$fields=$contentobj->getFields();
			$this->db->select("*, title AS value");
			foreach($fields as $field) {
				if (isset($field->libraries["search"])) {
					if ($field->libraries["search"]=="fulltext") {
						$matches[]=$field->tablename.".".$field->name;
					} elseif ($field->libraries["search"]=="like") {
						$likes[]=$field->tablename.".".$field->name;
					}
					if (!in_array($field->tablename,$tables)) {
						$tables[]=$field->tablename;
					}
				}
			}
			if (!empty($matches)) {
				$this->db->select("MATCH (".implode(",",$matches).") AGAINST (".$this->db->escape($searchstr).") AS score",false, false);
				$this->db->order_by("score","DESC");
				foreach($this->order_by as $ob) {
					$this->db->order_by($ob);
				}
				//$this->db->or_where("MATCH (".implode(",",$matches).") AGAINST (".$this->db->escape($searchstr).")",false, false);
			} 
			foreach($likes as $like) {
				$this->db->or_where($like." LIKE '%".$searchstr."%'");
			}
		
			foreach($tables as $table) {
				if ($table!="content") {
					$this->db->join($table, "content.id=$table.content_id");
				}
			}
			
			return $this->getAll($limit,$start);
		}
		
		/**
		 * suggest function.
		 * 
		 * Looks for suggestions based on title. Useful for autocomplete functionality.
		 *
		 * @access public
		 * @param mixed $content_type
		 * @param mixed $s
		 * @param mixed $limit
		 * @return void
		 */
		public function suggest($content_type, $s, $limit) {
			$this->setContentType($content_type);
			$this->db->select("id, urlid, title AS value");
			$this->db->from("content");
			$this->db->like("title",$s,"after");
			$this->db->limit($limit);
			$this->db->where("content_type_id",$this->content_type->id);
			$result=$this->db->get();
			return $result->result();
		}
		
		/**
		 * suggest_all function.
		 *
		 * Looks for suggestions regardless of content type
		 * 
		 * @access public
		 * @param mixed $s
		 * @param mixed $limit
		 * @return result
		 */
		public function suggest_all($s, $limit) {
			//$this->setContentType($content_type);
			$this->db->select("content.id, content.urlid");
			$this->db->select("CONCAT(content_types.name,': ',content.title) AS value",false);
			$this->db->from("content");
			$this->db->join("content_types","content_types.id=content.content_type_id");
			$this->db->like("content.title",$s,"after");
			$this->db->order_by("content.start_date DESC");
			$this->db->limit($limit);
			//$this->db->where("content_type_id",$this->content_type->id);
			$result=$this->db->get();
			return $result->result();
		}
		
		public function suggest_broad($types, $s, $limit) {
			$cids=array();
			foreach($types as $type) {
				$query=$this->db->get_where("content_types",array("urlid"=>$type));
				if ($query->num_rows()>0) {
					$cids[]="content_type_id=".$query->row()->id;
				}
			}
			$this->db->select("content.id, content.urlid");
			$this->db->select("CONCAT(content_types.name,': ',content.title) AS value",false);
			$this->db->from("content");
			$this->db->join("content_types","content_types.id=content.content_type_id");
			$this->db->like("content.title",$s,"after");
			$this->db->order_by("content.start_date DESC");
			$this->db->limit($limit);
			$this->db->where("(".implode(" OR ",$cids).")",false, false);
			$result=$this->db->get();
			return $result->result();
		}
		
		
		
		/**
		 * deep_suggest function.
		 * 
		 * Looks for suggestions based on title. Useful for autocomplete functionality, similar to the above function with the exception that it disregards the 		 * search ter as the starting word.
		 *
		 * @access public
		 * @param mixed $content_type
		 * @param mixed $s
		 * @param mixed $limit
		 * @return void
		 */
		public function deep_suggest($content_type, $s, $limit) {
			$this->setContentType($content_type);
			$this->db->select("id, urlid, title AS value");
			$this->db->from("content");
			$this->db->like("title",$s);
			$this->db->limit($limit);
			$this->db->where("content_type_id",$this->content_type->id);
			$result=$this->db->get();
			return $result->result();
		}
		
		/**
		 * deep_suggest_all function.
		 *
		 * Looks for suggestions regardless of content type, similar to the above function with the exception that it disregards the 		 * search ter as 		 * the starting word
		 * 
		 * @access public
		 * @param mixed $s
		 * @param mixed $limit
		 * @return result
		 */
		public function deep_suggest_all($s, $limit) {
			//$this->setContentType($content_type);
			$this->db->select("content.id, content.urlid");
			$this->db->select("CONCAT(content_types.name,': ',content.title) AS value",false);
			$this->db->from("content");
			$this->db->join("content_types","content_types.id=content.content_type_id");
			$this->db->like("content.title",$s);
			$this->db->order_by("content.start_date DESC");
			$this->db->limit($limit);
			//$this->db->where("content_type_id",$this->content_type->id);
			$result=$this->db->get();
			return $result->result();
		}
		
		public function deep_suggest_broad($types, $s, $limit) {
			$cids=array();
			foreach($types as $type) {
				$query=$this->db->get_where("content_types",array("urlid"=>$type));
				if ($query->num_rows()>0) {
					$cids[]="content_type_id=".$query->row()->id;
				}
			}
			$this->db->select("content.id, content.urlid");
			$this->db->select("CONCAT(content_types.name,': ',content.title) AS value",false);
			$this->db->from("content");
			$this->db->join("content_types","content_types.id=content.content_type_id");
			$this->db->like("content.title",$s);
			$this->db->order_by("content.start_date DESC");
			$this->db->limit($limit);
			$this->db->where("(".implode(" OR ",$cids).")",false, false);
			$result=$this->db->get();
			return $result->result();
		}
		
		
		/**
		 * smart_search function.
		 *
		 * Looks for suggestions regardless from articles.
		 * 
		 * @access public
		 * @param mixed $s
		 * @param mixed $limit
		 * @return result
		 */
		
		function smart_search($content_type, $s, $limit){
		
			
			$this->setContentType($content_type);
			//check if title matches the search term, if not use the fullbody text
			$query = "";
			if($this->input->get("selected", TRUE) != null)
			{
				$selecteds = $this->input->get("selected");
				$this->db->where_not_in("id",$this->db->escape($selecteds) );
				$query=$this->db->select("id, urlid, title AS value")->where("title", $s)->where("content_type_id",$this->content_type->id)->order_by("title ASC")->limit($limit)->get("content");
	
			}else{
				$query=$this->db->select("id, urlid, title AS value")->where("title", $s)->where("content_type_id",$this->content_type->id)->order_by("title ASC")->limit($limit)->get("content");
				
			}
			
			
			if($query->num_rows > 0) {
				return $query->result();

			} else {
				if(strlen($s) > 2) {
					$result=$this->search($content_type, $s, $limit);
				} else {
					$result=$this->suggest($content_type, $s, $limit);
				}
				return $result;
			}
			
			return $this->count();
			
			
		}
		
		
		
		/**
		 * smart_count function.
		 *
		 * counts hits for the search term regardless from articles.
		 * 
		 * @access public
		 * @param mixed $s
		 * @param mixed $limit
		 * @return result
		 */
		
		function smart_count($content_type, $s){
		
			
			$this->setContentType($content_type);
			//check if title matches the search term, if not use the fullbody text
			$query = "";
			if($this->input->get("selected", TRUE) != null)
			{
				$selecteds = $this->input->get("selected");
				$this->db->where_not_in("id",$this->db->escape($selecteds) );
				$query=$this->db->select("id, urlid, title AS value")->where("title", $s)->where("content_type_id",$this->content_type->id)->order_by("title ASC")->limit($limit)->get("content");
	
			}else{
				$query=$this->db->select("id, urlid, title AS value")->where("title", $s)->where("content_type_id",$this->content_type->id)->order_by("title ASC")->get("content");
				
			}
			
			if($query->num_rows > 0) {
				return $this->db->count_all_results();

			} else {
				if(strlen($s) > 2) {
					$result=$this->searchCount($content_type, $s);
				} else {
					$result=$this->searchCount($content_type, $s);
				}
				return $this->count();
			}
			
		  }

		
		
		/**
		 * get_content_types function.
		 * 
		 * Returns a list of possible content types
		 *
		 * @access public
		 * @param $public Show only public content types
		 * @return object
		 */
		public function get_content_types($public=true) {
		//Moved from model_content_deprecated
			if ($public) {
				$this->db->where("public",true);
			}
			$result=$this->db->get("content_types");
			return $result->result();
		}
		
		/**
		 * get_content_type function.
		 * 
		 * Returns a single content type
		 *
		 * @access public
		 * @param $id ID or urlid
		 * @return object
		 */
		public function get_content_type($id) {
			if (is_numeric($id)) {
				$this->db->where("id",$id);
			} else {
				$this->db->where("urlid",$id);
			}
			return $this->db->get("content_types")->row();
		}
		
		/**
		 * getParents function.
		 * 
		 * Looks for any objects that 'own' an object. You can limit it by certain
		 * types by setting the second variable. Will take numeric ID or urlid. Returns an
		 * array of content_ids
		 *
		 * @access public
		 * @param $id ID or urlid
		 * @return array
		 */
		public function getParents($id, $contenttype=false) {
			if (is_numeric($id)) {
				$item=$this->db->get_where("content",array("id"=>$id))->row();
			} else {
				$item=$this->db->get_where("content",array("urlid"=>$id))->row();
			}
			if (empty($item->urlid)) {
				print "Error finding $id";
				return false;
			}
			$content_id=$item->id;
			if (!empty($contenttype)) {
				$ct=$this->get_content_type($contenttype);
				$this->db->join("content","content.id=content_content.content_id");
				$this->db->where("content.content_type_id",$ct->id);
			}
			$this->db->where("content_link_id",$content_id);
			$query=$this->db->get("content_content");
			$result=array();
			foreach($query->result() as $parent) {
				$result[]=array("content_id"=>$parent->content_id, "urlid"=>$parent->urlid);
			}
			return $result;
		}
		
		/**
		 * get_sectionmap function.
		 * 
		 * Returns a named array of all the sections
		 *
		 * @access public
		 * @param int $content_type_id
		 * @return void
		 */
		public function get_sectionmap($content_type) {
		
			$this->setContentType($content_type);
			$content_type_id = $this->content_type->id;
			$result=array();
			$query=$this->db->select("content.urlid, content.title, content.id AS content_id")->from("content")->join("content_content","content_content.content_id=content.id")->join("content AS content2","content_content.content_link_id=content2.id")->where("content.content_type_id",$content_type_id)->where("content2.content_type_id",$content_type_id)->group_by("content.urlid")->order_by("content.title")->get();
						
			$parents=$query->result();
			foreach($parents as $parent) {
				$children=$this->get_subsections($parent->content_id, $content_type_id);
				$result[$parent->urlid]=$parent;
				foreach($children as $child) {
					$result[$parent->urlid]->children[]=$child;
				}
			}
			
			return $result;

		}
		
		/**
		 * get_subsections function.
		 * 
		 * Given a primary section, returns all data about its subsections
		 *
		 * @access public
		 * @param int $id
		 * @param int $content_type_id
		 * @return void
		 */
		public function get_subsections($id, $content_type_id) {
			$section=$this->db->get_where("content",array("id"=>$id))->row();
			$query=$this->db->select("content.title, content.urlid, content.id AS content_id")->from("content")->join("content_content","content.id=content_content.content_link_id")->where("content_content.content_id",$section->id)->where("content.content_type_id",$content_type_id)->get();
			return $query->result();
		}

	}


/* End of file Model_Content.php */
/* Location: ./system/application/models/ */