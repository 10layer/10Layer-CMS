<?php
	
	/**
	 * Model_Section class.
	 * 
	 * @extends CI_Model
	 */
	class Model_Section extends CI_Model {
	
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
			$this->db->order_by("order","ASC");
			$result=$this->db->get("sections");
			return $result->result();
		}
		
		public function get($urlid) {
			if (is_numeric($urlid)) {
				$result=$this->db->get_where("sections",array("id"=>$urlid));
			} else {
				$result=$this->db->get_where("sections",array("urlid"=>$urlid));
			}
			return $result->row();
		}
		
		public function getSubSection($urlid) {
			if (is_numeric($urlid)) {
				$result=$this->db->get_where("subsections",array("id"=>$urlid));
			} else {
				$result=$this->db->get_where("subsections",array("urlid"=>$urlid));
			}
			return $result->row();
		}
		
		public function getSubSections($urlid) {
			$section=$this->get($urlid);
			$result=$this->db->get_where("subsections",array("section_id"=>$section->id));
			return $result->result();
		}
		
		public function getLayouts($urlid) {
			$section=$this->get($urlid);
			$result=$this->db->get_where("section_layout",array("section_id"=>$section->id));
			return $result->result();
		}
		
		public function getContent($urlid) {
			$this->db->select("ranking.urlid");
			$this->db->select("ranking.subsection_id");
			$this->db->select("content_types.urlid AS contenttype");
			$this->db->select("content.title AS title");
			$this->db->from("ranking");
			//$this->db->join("sections","ranking.section_id=sections.id");
			//$this->db->join("subsections","ranking.subsection_id=subsections.id");
			$this->db->join("content","content.urlid=ranking.urlid");
			$this->db->join("content_types","content_types.id=content.content_type_id");
			$this->db->order_by("rank ASC");
			$this->db->where("ranking.subsection_id",$urlid);
			$query=$this->db->get();
			
			return $query->result();
		}
		
		public function setContent($zone_id,$data) {
			$this->db->where("zone_urlid",$zone_id);
			$this->db->delete("ranking");
			foreach($data as $row) {
				$this->db->insert("ranking",$row);
			}
			return true;
		}
		
		public function getContentInQueue($urlid, $zone_id=false, $startdate=false, $enddate=false, $search=false, $limit=100, $start=0) {
			$this->load->model("model_zones","zones");
			$ctids=array();
			$zone=$this->zones->getByIdORM($zone_id)->getData();
			
			$published_list=$this->db->where("zone_urlid",$zone_id)->order_by("rank ASC")->get("ranking")->result();
			
			//$published_articles=$this->zones->content;
			$published_articles=array();
			$published_ids=array();
			foreach($published_list as $article) {
				//We need to make sure the ID exists
				if (isset($article->content_id)) {
					$query=$this->db->select("content.*, content_types.urlid AS content_type_urlid")->join("content_types","content_types.id=content.content_type_id")->where("content.id", $article->content_id)->get("content");
					if ($query->num_rows()==1) {
						$published_ids[]=$article->content_id;
						//print $article->content_id;
						try {
							$published_articles[]=$query->row();
						} catch(exception $e) {
							
						}
					}
				}
			}
			$contenttypes=explode(",",$zone->content_types);
			if (is_array($contenttypes)) {
				foreach($contenttypes as $ct) {
					$query=$this->db->get_where("content_types",array("urlid"=>$ct));
					if (!empty($query->row()->id)) {
						$ctids[]=$query->row()->id;
					}
				}
			}
			if (is_array($urlid)) {
				$major_versions=array();
				foreach($urlid as $val) {
					$workflow=$this->model_workflow->getByUrlId($val);
					$major_versions[]="content.major_version={$workflow->major_version}";
				}
				$this->db->where(array("content_platforms.platform_id"=>$this->platforms->id(), "content.live"=>true));
				
				$this->db->where("(".implode(" OR ",$major_versions).")");
			} else {
				$workflow=$this->model_workflow->getByUrlId($urlid);
				$major_version=(Int) $workflow->major_version;
				$this->db->where(array("content.major_version"=>$major_version, "content_platforms.platform_id"=>$this->platforms->id(), "content.live"=>true));
			}
			
			$this->db->limit($limit, $start);
			$this->db->select("content.*");
			$this->db->select("content_types.urlid AS contenttype");
			$this->db->from("content");
			$this->db->join("content_types","content_types.id=content.content_type_id");
			
			if (!empty($startdate)) {
				$this->db->where("content.start_date >=",date("Y-m-d",strtotime($startdate)));
			}
			if (!empty($enddate)) {
				$this->db->where("content.start_date <=",date("Y-m-d",strtotime($enddate)));
			}
			if (!empty($search)) {
				$this->db->like("title",$search);
			}

			$this->db->join("content_platforms","content_platforms.content_id=content.id");
			$cts=array();
			foreach($ctids as $ctid) {
				$cts[]="content.content_type_id = $ctid";
			}
			$this->db->where("(".implode(" OR ",$cts).")");
			if (!empty($published_ids)) {
				$this->db->where_not_in("content.id",$published_ids);
			}
			$this->db->order_by("content.last_modified","DESC");
			$query=$this->db->get();
			//print $this->db->last_query();
			$result=array();
			$result["published"]=$published_articles;
			$result["unpublished"]=$query->result();
			return $result;
			
		}
	}
	

/* End of file Model_Section.php */
/* Location: ./system/application/models/ */