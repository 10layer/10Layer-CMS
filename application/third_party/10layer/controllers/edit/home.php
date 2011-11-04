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
			$types=$this->model_content->get_content_types(true);
			redirect("create/".$types[0]->urlid);
		}
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/edit/home.php */