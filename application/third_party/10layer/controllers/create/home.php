<?php

class Home extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->output->enable_profiler($this->config->item("profile"));		
	}
	
	function index() {
		$contenttype=$this->session->userdata("contenttype");
		if (!empty($contenttype)) {
			redirect("create/".$contenttype);
		} else {
			redirect("create/article");
		}
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/edit/home.php */