<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_Tag extends Model_Content {
	public $order_by=array(
		"title ASC",
		"start_date"
	);
	
	public $fields=array(
	    array(
	    	"name"=>"urlid",
	    	"tablename"=>"content",
	    	"type"=>"hidden",
	    	"transformations"=>array(
	    		"copy"=>"title",
	    		"urlid"=>array("content.urlid",false)
	    	)
	    ),
	    /*array(
	    	"name"=>"tagtype",
	    	"tablename"=>"tagtype",
	    	"label"=>"Type",
	    	"type"=>"select",
	    	"contenttype"=>"tagtype",
	    	"multiple"=>false,
	    ),*/	    
	    
	);

	public function __construct() {
		parent::__construct("tag");
	}
}

?>