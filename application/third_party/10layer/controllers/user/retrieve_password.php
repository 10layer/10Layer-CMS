<?php
	/**
	 * Retrieve_Password class
	 * 
	 * @extends CI_Controller
	 */
	class Retrieve_Password extends CI_Controller {

		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
		}
		
		public function index() {
			$data["error"]=false;
			$data["email_sent"]=false;
			$email=$this->input->post("email");
			if (!empty($email)) {
				$password=$this->model_user->get_password_by_email($email);
				if (!empty($email)) {
					$this->email_password($email, $password);
					$data["email_sent"]=true;
				}
				
			}
			$this->load->view("user/retrieve_password",$data);
		}
		
		/**
		 * email_password function.
		 * 
		 * @access protected
		 * @param String $password
		 * @return void
		 */
		protected function email_password($email, $password) {
			$this->load->library("email");
			$this->email->from('admin@10layer.com', '10Layer CMS');
			$this->email->to($email); 
			
			$this->email->subject('10Layer CMS password');
			$this->email->message("Your 10Layer password is <strong>$password</strong");	
			
			$this->email->send();
		}
	}

/* End of file retrieve_password.php */
/* Location: ./system/application/controllers/user/ */