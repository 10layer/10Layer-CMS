<?php
	/**
	 * Worker class
	 *
	 * Does all the heavy lifting for the Publish feature
	 * 
	 * @extends CI_Controller
	 */
	class Worker extends CI_Controller {

		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			$this->load->model("model_section");
			$this->load->model("model_content");
			$this->load->model("model_site_sections","sections");
			$this->load->model("model_zones","zones");
		}
		
		public function rank_section() {
			$content=$this->input->post("content");
			$zone_id=$this->input->post("zone_id");
			$zone_name=$this->input->post("zone_name");
			$dbdata=array();
			$x=1;
			if (is_array($content)) {
				foreach($content as $content_id) {
					$dbdata[]=array("content_id"=>$content_id,"rank"=>$x,"zone_urlid"=>$zone_id);
					$x++;
				}
			}
			$this->model_section->setContent($zone_id,$dbdata);
			//$this->checkCallback("onAfterUpdate", $zone_id);
			$this->messaging->post_action("publish",$zone_id);
			//$subsection=$this->model_section->getSubSection($subsection_id);
			print "Updated ".$zone_name;
		}
		
		public function subsection($section_urlid, $zone_urlid, $startdate=false, $enddate=false, $searchstr="") {
			if ($zone_urlid=="undefined") {
				print "This zone is undefined.";
				return true;
			}
			$section=$this->sections->getByIdORM($section_urlid);
			
			$data["zone"]=$this->zones->getByIdORM($zone_urlid)->getData();
			$articles=$this->model_section->getContentInQueue(array("queued_for_publishing", "published"),$zone_urlid,$startdate,$enddate, $searchstr);
			$data["content"]=$articles["unpublished"];
			$data["published_articles"]=$articles["published"];
			$data["section_id"]=$section->content_id;
			$this->load->view("publish/subsection",$data);
		}
		
	}

/* End of file worker.php */
/* Location: ./system/application/controllers/publish/ */