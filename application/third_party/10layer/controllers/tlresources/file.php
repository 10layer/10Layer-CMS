<?php
	/**
	 * File class
	 * 
	 * Loads files in application/third_party/resources
	 *
	 * @extends Controller
	 */
	class File extends CI_Controller {

		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			$this->load->library("tlsecurity");
			$this->tlsecurity->ignore_security();
			$this->load->helper("file");
		}
		
		/**
		 * _remap function.
		 * 
		 * Shows a specific file with header
		 *
		 * @access public
		 * @return void
		 */
		public function _remap() {
			$segments=$this->uri->segment_array();
			array_shift($segments);
			array_shift($segments);
			$filename=APPPATH."third_party/10layer/resources/".implode("/",$segments);
			if (file_exists($filename)) {
				header("content-type: ".get_mime_by_extension($filename));
				print file_get_contents($filename);
			} else {
				show_404($segments[sizeof($segments)-1]);
			}
		}
	}

/* End of file tlresources.php */
/* Location: ./system/application/controllers/ */