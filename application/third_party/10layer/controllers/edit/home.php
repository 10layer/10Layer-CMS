<?php

class Home extends CI_Controller {

	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$contenttype=$this->session->userdata("contenttype");
		if (!empty($contenttype)) {
			redirect("edit/".$contenttype);
		} else {
			redirect("edit/article");
		}
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/edit/home.php */