<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
	/**
	 * 10Layer MY_Email Class extends Email
	 *
	 * This class handles emailing with the 10Layer look
	 *
	 * @package		10Layer
	 * @subpackage	Libraries
	 * @category	Libraries
	 * @author		Jason Norwood-Young
	 * @link		http://10layer.com
	 */
	
	class MY_Email extends CI_Email {
	
		var	$mailtype		= "html";	// text/html  Defines email formatting
		
		/**
		 * Set Body
		 *
		 * @access	public
		 * @param	string
		 * @return	void
		 */
		function message($body) {
			$ci=& get_instance();
			$template=$ci->load->view("templates/email","",true);
			$body=str_replace("[CONTENT]",$body,$template);
			$body = stripslashes(rtrim(str_replace("\r", "", $body)));
			
			$this->_body=$body;
		}
		
		/**
		 * Set Mailtype
		 *
		 * @access	public
		 * @param	string
		 * @return	void
		 */
		function set_mailtype($type = 'html')
		{
			$this->mailtype = ($type == 'html') ? 'html' : 'text';
		}
	
	}