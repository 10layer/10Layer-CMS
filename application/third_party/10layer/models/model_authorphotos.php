<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_AuthorPhotos extends Model_Content {
	public $order_by=array(
		"timestamp DESC",
		"start_date"
	);
	
	public $fields=array(
	    array(
	    	"name"=>"filename",
	    	"type"=>"file",
	    	"label"=>"File",
	    ),
	);

	public function __construct() {
		parent::__construct("authorphoto");
	}
}

?>