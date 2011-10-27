<?php

class Home extends CI_Controller {

	function __construct() {
		parent::__construct();
	}
	
	function index() {
		redirect("manage/users/my_account");
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/manage/home.php */