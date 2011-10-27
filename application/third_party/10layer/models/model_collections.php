<?php
	
	/**
	 * Model_Collections class.
	 * 
	 * @extends Model
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
	}

/* End of file model_collections.php */
/* Location: ./system/application/third_party/10layer/models/model_collections.php */