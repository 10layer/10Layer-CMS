<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * TL_Controller_Create class.
 *
 * Used to create new content.
 * 
 * @extends TL_Controller_CRUD
 */
class TL_Controller_Create extends TL_Controller_CRUD {
	/**
	 * _view
	 * 
	 * (default value: "content/default/create")
	 * 
	 * @var string
	 * @access protected
	 */
	protected $_view="content/default/create";
	
	/**
	 * __construct function.
	 *
	 * Calls the parent constructor.
	 * 
	 * @access public
	 * @param bool $contenttype. (default: false)
	 * @return void
	 */
	public function __construct($contenttype=false) {
		parent::__construct();	
	}
	
	/**
	 * submit function.
	 *
	 * Submits the data. This would usually not be called directly.
	 * 
	 * @access public
	 * @return void
	 */
	public function submit() {
		$returndata=array("error"=>false,"msg"=>"");
		$contentobj=new TLContent();
		$contentobj->setContentType($this->_contenttypeurlid);
		$do_action=$this->input->post("action");
		if (!empty($do_action)) {
			$this->checkCallback("onBeforeAction",$contentobj);
			foreach($contentobj->getFields() as $field) {
				if ($field->readonly) {
					//Do NOTHING
				} else {
					if ($field->type=="file") {
					//Process file uploads
						$dir="/resources/uploads/files/original/".date("Y")."/".date("m")."/".date("d")."/";
						if (!empty($field->directory)) {
							$dir=$field->directory;
							if ($dir[0]!="/") {
								$dir="/".$dir;
							}
							while (strpos($dir,"{")!==false) {
								$part=substr($dir, strpos($dir,"{")+1, strpos($dir,"}")-strpos($dir,"{")-1);
								$replace=eval("return $part;");
								$dir=str_replace("{".$part."}", $replace, $dir);
							}
							if (!is_dir(".".$dir)) {
								mkdir(".".$dir, 0755, true);
							}
							if (!is_dir(".".$dir)) {
								show_error("Unable to create directory $dir");
							}
						}
						$basedir=".".$dir;
						if (!file_exists($basedir)) {
							if (!mkdir($basedir, 0755, true)) {
								$returndata["error"]=true;
								$returndata["msg"]="Failed to create directory structure";
								$returndata["info"]="Tried to create $dir";
							}
						}
						//Handle drag-n-drop file uploads
						$filename=$this->input->post($field->tablename."_".$field->name."_dataurl");
						
						if (!empty($filename) && (!$returndata["error"])) {
							$data=$this->input->post($field->tablename."_".$field->name."_dataurl_data");
							$data=explode("base64,",$data);
							$databin=base64_decode($data[1]);
							file_put_contents($basedir.$filename, $databin);
							$contentobj->{$field->name}=$dir.$filename;
						} elseif (!$returndata["error"]) {
							if (!empty($_FILES[$field->tablename."_".$field->name]["name"])) {
								$config['upload_path'] = $basedir;
								$config['allowed_types'] = implode("|",$field->filetypes);
								$this->load->library("upload",$config);
								if (!$this->upload->do_upload($field->tablename."_".$field->name)) {
									$returndata["error"]=true;
									$returndata["info"]=$this->upload->display_errors();
									$returndata["msg"]="File Upload failed";
								} else {
									$uploaddata = $this->upload->data();
									$filename=$dir.$uploaddata["file_name"];
									$contentobj->{$field->name}=$filename;
									if ($field->cdn) {
									//Upload to CDN
										$this->load->library("cdn");
										$this->cdn->init();
										if ($this->cdn->hasError()) {
											$returndata["error"]=true;
											$returndata["info"]=$this->cdn->lastError();
											$returndata["msg"]="Error uploading to CDN";
										} else {
											$bucket=$this->config->item("cdn_bucket");
											$this->cdn->createBucket($bucket);
											$cdnurl=$this->cdn->uploadFile(".".$filename, $bucket,$filename);
											if ($this->cdn->hasError()) {
												$returndata["error"]=true;
												$returndata["info"]=$this->cdn->lastError();
												$returndata["msg"]="Error uploading to CDN";
											} else {
												if (!empty($field->cdn_link)) {
													$contentobj->{$field->cdn_link}=$cdnurl;
												}
											}
										}
									}
								}
							}
							
						}
						
					} else {
						$fieldval=$this->input->post($field->tablename."_".$field->name);
						if (empty($fieldval)) {
							$contentobj->{$field->name}="";
						} else {
							$contentobj->{$field->name}=$fieldval;
						}
					}
				}
			}
			
			$contentobj->transformFields();
			$validation=$contentobj->validateFields();
			if (!$validation["passed"]) {
				$returndata["error"]=true;
				$returndata["msg"]="Failed to create {$this->_contenttypeurlid}";
				$returndata["info"]=implode("<br />\n",$validation["failed_messages"]);
			}
			
			if (!$returndata["error"]) {
				$this->checkCallback("onBeforeSubmit",$contentobj);
				if (!$returndata["error"]) {
					$contentobj->insert();
				}
				$finalobj=$this->content->getByIdORM($contentobj->content_id, $this->_contenttype->id);
				$this->checkCallback("onAfterSubmit",$finalobj);
			}
			
			if (!$returndata["error"]) {
				$returndata["msg"]="Successfully created {$this->_contenttypeurlid}";
				$returndata["id"]=$finalobj->content_id;
				$returndata["data"]=$finalobj->getData();
				$this->checkCallback("onAfterAction",$finalobj);
			}
			
			if (!$returndata["error"]) { //Memcached submission
				//$this->cachesave($this->_contenttypeurlid,$contentobj->content_id);
			}
			$this->messaging->post_action("create",array($this->_contenttypeurlid,$finalobj->urlid));
			return $returndata;
		}
		return array("error"=>true,"msg"=>"No data submitted");
	}
	
	/**
	 * ajaxsubmit function.
	 *
	 * This will do your submit through Ajax, and will also set your document.domain and package it in a textarea for cross-domain safety
	 * 
	 * @access public
	 * @return void
	 */
	public function ajaxsubmit() {
		$result=$this->submit();
		print "<script>document.domain=document.domain;</script><textarea>";
		print json_encode($result);
		print "</textarea>";
	}
	
	/**
	 * view function.
	 *
	 * This draws our view for us.
	 * 
	 * @access public
	 * @return void
	 */
	public function view() {
		$contentobj=new TLContent();
		$contentobj->setContentType($this->_contenttypeurlid);
		$this->checkCallback("onBeforeView",$contentobj);
		$this->load->library("formcreator");
		$fields=$contentobj->getFields();
		$this->formcreator->setFields($fields);
		$data["file_fields"]=array();
		foreach($fields as $field) {
			if ($field->type=="file") {
				$data["file_fields"][]=$field;
			}
		}
		$data["menu1_active"]="create";
		$data["menu2_active"]="create/".$this->_contenttypeurlid;
		$data["contenttype"]=$this->_contenttypeurlid;
		$this->load->view($this->_view,$data);
		
		$this->checkCallback("onAfterView",$contentobj);
	}
	
	public function embed() {
		$this->_view="content/default/embed";
		$contentobj=new TLContent();
		$contentobj->setContentType($this->_contenttypeurlid);
		$this->checkCallback("onBeforeView",$contentobj);
		$this->load->library("formcreator");
		$fields=$contentobj->getFields();
		$tmp=array();
		foreach($fields as $key=>$field) {
			$fields->$key->name="embed_".$field->name;
		}
		$this->formcreator->setFields($fields);
		$data["file_fields"]=array();
		foreach($fields as $field) {
			if ($field->type=="file") {
				$data["file_fields"][]=$field;
			}
		}
		$data["menu1_active"]="create";
		$data["menu2_active"]="create/".$this->_contenttypeurlid;
		$data["contenttype"]=$this->_contenttypeurlid;
		$this->load->view($this->_view,$data);
		
		$this->checkCallback("onAfterView",$contentobj);
	}
		
}

/**
 * TL_Controller_Edit class.
 *
 * The controller for editing existing content.
 * 
 * @extends TL_Controller_CRUD
 */
class TL_Controller_Edit extends TL_Controller_CRUD {
	
	/**
	 * _pg_perpage
	 * 
	 * Number of rows to display per page (default value: 100)
	 * 
	 * @var int
	 * @access public
	 */
	public  $_pg_perpage=100;
	
	/**
	 * _pg_numlinks
	 * 
	 * Number of pagination links to display (default value: 15)
	 * 
	 * @var int
	 * @access public
	 */
	public $_pg_numlinks=15;
	
	/**
	 * _pg_offset
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_pg_offset=0;
	
	
	/**
	 * __construct function.
	 *
	 * Calls parent constructor.
	 * 
	 * @access public
	 * @param bool $contenttype. (default: false)
	 * @return void
	 */
	public function __construct($contenttype=false) {
		parent::__construct($contenttype);
		$this->load->library("memcacher");
	}
	
	/**
	 * submit function.
	 *
	 * Submits the edited data. This shouldn't be called directly.
	 * 
	 * @access public
	 * @param mixed $type
	 * @param mixed $urlid
	 * @return void
	 */
	public function submit($type,$urlid) {
		$contentobj=$this->content->getByIdORM($urlid, $this->_contenttype->id);
		$contentobj->clearData();
		if (empty($contentobj->content_id)) {
			//show_404("/edit/".$this->uri->segment(3)."/".$urlid);
			$returndata["error"]=true;
			$returndata["msg"]="Update failed";
			$returndata["info"]="Could not find content with urlid $urld";
			return $returndata;
		}
		
		$do_action=$this->input->post("action");
		
		if (!empty($do_action)) {
			$this->checkCallback("onBeforeAction",$contentobj);
			$dbdata=array();
			$returndata=array("error"=>false);
			$fields=$contentobj->getFields($this->_contenttypeurlid);
			//print_r($fields);
			foreach($fields as $field) {
				//print_r($field);
				if ($field->readonly || ($field->type=="drilldown")) {
					//Do Nothing!
				} else {
					if ($field->type=="file") {
					//Process file uploads
						if (!$returndata["error"] && (!empty($_FILES[$field->tablename."_".$field->name]["name"]))) {
							$dir="/resources/uploads/files/original/".date("Y")."/".date("m")."/".date("d")."/";
							$cachedir="/resources/uploads/pictures/cache/";
							if (!empty($field->directory)) {
								$dir=$field->directory;
								if ($dir[0]!="/") {
									$dir="/".$dir;
								}
								while (strpos($dir,"{")!==false) {
									$part=substr($dir, strpos($dir,"{")+1, strpos($dir,"}")-strpos($dir,"{")-1);
									$replace=eval("return $part;");
									$dir=str_replace("{".$part."}", $replace, $dir);
								}
								if (!is_dir(".".$dir)) {
									mkdir(".".$dir, 0755, true);
								}
								if (!is_dir(".".$dir)) {
									show_error("Unable to create directory $dir");
								}
							}
							$basedir=".".$dir;
							
							if (!file_exists($basedir)) {
								if (!mkdir($basedir, 0755, true)) {
									$returndata["error"]=true;
									$returndata["msg"]="Failed to create directory structure";
									$returndata["info"]="Tried to create $dir";
								}
							}
						}
						if (!$returndata["error"]) {
							if (!empty($_FILES[$field->tablename."_".$field->name]["name"])) {
								$config['upload_path'] = $basedir;
								$config['allowed_types'] = implode("|",$field->filetypes);
								$this->load->library("upload",$config);
								if (!$this->upload->do_upload($field->tablename."_".$field->name)) {
									$returndata["error"]=true;
									$returndata["info"]=$this->upload->display_errors();
									$returndata["msg"]="File Upload failed";
								} else {
									$uploaddata = $this->upload->data();
									$filename=$dir.$uploaddata["file_name"];
									$contentobj->{$field->name}=$filename;
									//Clear Cache
									exec("rm .".$cachedir.$urlid."*");
									if ($field->cdn) {
									//Upload to CDN
										$this->load->library("cdn");
										$this->cdn->init();
										if ($this->cdn->hasError()) {
											$returndata["error"]=true;
											$returndata["info"]=$this->cdn->lastError();
											$returndata["msg"]="Error uploading to CDN";
										} else {
											$bucket=$this->config->item("cdn_bucket");
											$this->cdn->createBucket($bucket);
											$cdnurl=$this->cdn->uploadFile(".".$filename, $bucket,$filename);
											if ($this->cdn->hasError()) {
												$returndata["error"]=true;
												$returndata["info"]=$this->cdn->lastError();
												$returndata["msg"]="Error uploading to CDN";
											} else {
												if (!empty($field->cdn_link)) {
													$contentobj->{$field->cdn_link}=$cdnurl;
												}
											}
										}
									}
								}
							}
							
						}
						
					} else {
						$contentobj->{$field->name}=$this->input->post($field->tablename."_".$field->name);
					}
				}
			}
			
			$contentobj->transformFields($this->_contenttypeurlid);
			$validation=$contentobj->validateFields($this->_contenttypeurlid);
			if (!$validation["passed"]) {
				$returndata["error"]=true;
				$returndata["msg"]="Failed to update {$this->_contenttypeurlid}";
				$returndata["info"]=implode("<br />\n",$validation["failed_messages"]);
			}
			if (!$returndata["error"]) {
				$this->checkCallback("onBeforeSubmit",$contentobj);
				$this->versions->bump_minor_version();
				//print_r($contentobj);
				$contentobj->update();
				$returndata["msg"]="Successfully updated {$this->_contenttypeurlid}";
				$this->checkCallback("onAfterSubmit",$contentobj);
			}
			
			if (!$returndata["error"]) { //Memcached submission
				$this->cachereset($this->_contenttypeurlid,$urlid);
				//$this->cachesave($this->_contenttypeurlid,$urlid);
			}
			
			//Tell the world
			$this->messaging->post_action("update_content",array($this->_contenttypeurlid,$urlid));
			$this->messaging->post_action("edit",array($this->_contenttypeurlid,$urlid));
			$this->checkCallback("onAfterAction",$contentobj);
			
			return $returndata;
		}
		return array("error"=>true,"msg"=>"No data submitted");
	}
	
	/**
	 * ajaxsubmit function.
	 * 
	 * Packaged our Ajax submit and returns the result in a cross-domain-safe package.
	 *
	 * @access public
	 * @param mixed $type
	 * @param mixed $urlid
	 * @return void
	 */
	public function ajaxsubmit($type,$urlid) {
		$result=$this->submit($type,$urlid);
		print "<script>document.domain=document.domain;</script><textarea>";
		print json_encode($result); 
		print "</textarea>";
	}
	
	/**
	 * view function.
	 * 
	 * Draws our edit view.
	 *
	 * @access public
	 * @param mixed $type
	 * @param bool $urlid. (default: false)
	 * @param bool $action. (default: false)
	 * @return void
	 */
	public function view($type,$urlid=false,$action=false) {
		//$this->output->enable_profiler(true);
		if (empty($urlid) || ($urlid=="pg")) {
			$this->index();
			return true;
		}
		if ($urlid=="search") {
			$this->search(rawurldecode($action));
			return true;
		}
		$contentobj=$this->content->getByIdORM($urlid, $this->_contenttype->id);
//		print_r($contentobj);
		if (empty($contentobj->content_id)) {
			show_404("/edit/".$this->uri->segment(3)."/".$urlid);
		}
		
		$this->checkout->lock();
		
		if (!empty($action)) {
			if (method_exists($this, $action)) {
				return call_user_method($action,$this);
			}
		}
		$data["urlid"]=$contentobj->urlid;
		$data["id"]=$contentobj->content_id;
		
		$data["contenttype_id"]=$contentobj->content_type->id;
		$data["contenttype"]=$this->_contenttypeurlid;
		
		$this->load->library("formcreator");
		$this->formcreator->setFields($contentobj->getFields());
		$data["menu1_active"]="edit";
		$data["menu2_active"]="edit/".$this->_contenttypeurlid;
		$this->load->view("content/default/edit",$data);
	}
	
	/**
	 * index function.
	 * 
	 * @access public
	 * @return void
	 */
	public function index() {
		$this->load->library("memcacher");
		//$this->content->setContentType($this->_contenttypeurlid);
		//$this->content->setPlatform($this->platforms->id());
		$this->load->library("tlpicture");
		$this->paginate();
		$data["content"]=$this->content->getAll($this->_pg_perpage, $this->_pg_offset);
		$data["contenttype"]="{$this->_contenttypeurlid}";
		if ($this->exists->view("content/{$this->_contenttypeurlid}/list")) {
			$this->load->view("content/{$this->_contenttypeurlid}/list",$data);
		} else {
			$this->load->view("content/default/list",$data);
		}
	}
	
	/**
	 * row function.
	 * 
	 * @access public
	 * @param mixed $type
	 * @param mixed $urlid
	 * @return void
	 */
	public function row($type, $urlid) {
		//$this->content->setContentType($this->_contenttypeurlid);
		//$this->content->setPlatform($this->platforms->id());
		$data["item"]=$this->content->get($urlid);
		$data["contenttype"]=$type;
		if ($this->exists->view("content/{$this->_contenttypeurlid}/row")) {
			$this->load->view("content/{$this->_contenttypeurlid}/row",$data);
		} else {
			$this->load->view("content/default/row",$data);
		}
	}
	
	/**
	 * paginate function.
	 * 
	 * @access public
	 * @return void
	 */
	public function paginate() {
		$this->_pg_offset=$this->uri->segment(5);
		$this->load->library('pagination');
		$config['full_tag_open']="<div class='pagination'>";
		$config['full_tag_close']="</div>";
		$config['uri_segment'] = 5;
		$config['num_links'] = $this->_pg_numlinks;
		$config['base_url'] = "/edit/".$this->uri->segment(2)."/".$this->uri->segment(3)."/pg/";
		//$config['total_rows'] = $this->{$this->_model}->count();
		$config['total_rows'] = $this->content->count();
		$config['per_page'] = $this->_pg_perpage;
		$this->pagination->initialize($config);
	}
	
	/**
	 * search function.
	 * 
	 * @access public
	 * @param mixed $s
	 * @return void
	 */
	public function search($s) {
		$s=rawurldecode($s);
		$this->load->library("search");
		$this->_pg_offset=$this->uri->segment(7);
		$config['uri_segment'] = 7;
		//$s=$this->uri->segment(4);
		if (($s=="pg") || ($s=="undefined")) {
			$s="";
			$this->_pg_offset=$this->uri->segment(6);
			$config['uri_segment'] = 6;
		}
		$result=$this->search->dosearch($this->_contenttypeurlid, $s, $this->_pg_perpage, $this->_pg_offset);
		$this->load->library('pagination');
		$config['full_tag_open']="<div class='pagination'>";
		$config['full_tag_close']="</div>";
		$config['num_links'] = $this->_pg_numlinks;
		$config['base_url'] = "/edit/".$this->uri->segment(2)."/".$this->uri->segment(3)."/".$this->uri->segment(4)."/".$s."/pg";
		$config['total_rows'] = $result["count"];
		$config['per_page'] = $this->_pg_perpage;
		$this->pagination->initialize($config);
		$data["pagination"]=$this->pagination->create_links();
		$data["content"]=$result["docs"];
		$data['total_rows'] = $result["count"];
		$data["contenttype"]="{$this->_contenttypeurlid}";
		$data["search"]=$s;
		if ($this->exists->view("content/{$this->_contenttypeurlid}/list")) {
			$this->load->view("content/{$this->_contenttypeurlid}/list",$data);
		} else {
			$this->load->view("content/default/list",$data);
		}
	}
	
	/**
	 * checkin function.
	 * 
	 * Saves a copy and does a major version bump
	 *
	 * @access public
	 * @return void
	 */
	public function checkin() {
		$this->versions->bump_major_version();
		print json_encode(array("error"=>false, "major_version"=>$this->versions->get_major_version()));
	}
	
}

/**
 * TL_Controller_Delete class.
 *
 * Deletes stuff. Also takes care of deleting joins. 
 * 
 * @extends TL_Controller_CRUD
 */
class TL_Controller_Delete extends TL_Controller_CRUD {
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * _remap function.
	 * 
	 * Make it dead. This is a remap function so you just pass it the urlid in the 3rd URI segment and it'll take care of the rest.
	 *
	 * @access public
	 * @return void
	 */
	public function _remap() {
		$urlid=$this->uri->segment(3);
		if (empty($urlid)) {
			show_404("/edit/".$this->uri->segment(2));
			return true;
		}
		$contentobj=new TLContent($urlid);
		$contentobj->setContentType($this->_contenttypeurlid);
		$this->checkCallback("onBeforeDelete",$contentobj);
		$contentobj->delete();
		$this->checkCallback("onAfterDelete",$contentobj);
		//if (!$returndata["error"]) { //Memcached submission
			$this->messaging->post_action("delete",array($this->_contenttypeurlid,$urlid));
			//$this->cachereset($this->_contenttypeurlid,$contentobj->urlid);
		//}
		
		redirect("edit/".$this->_contenttypeurlid);
	}
	
}

/**
 * TL_Controller_List class.
 *
 * Displays a list of content, usually pre-editing.
 * 
 * @extends TL_Controller_CRUD
 */
class TL_Controller_List extends TL_Controller_CRUD {
	/**
	 * _pg_perpage
	 * 
	 * Number of rows to display per page (default value: 100)
	 * 
	 * @var int
	 * @access public
	 */
	public  $_pg_perpage=30;
	
	/**
	 * _pg_numlinks
	 * 
	 * Number of pagination links to display (default value: 15)
	 * 
	 * @var int
	 * @access public
	 */
	public $_pg_numlinks=15;
	
	/**
	 * _pg_offset
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $_pg_offset=0;
	
	/**
	 * _remap function.
	 * 
	 * @access public
	 * @return void
	 */
	public function _remap() {
		//$this->content->setContentType($this->_contenttypeurlid);
		//$this->content->setPlatform($this->platforms->id());
		if($this->uri->segment(3)=="item") {
			$this->item();
			return true;
		} elseif($this->uri->segment(3)=="search") {
			$this->search();
			return true;
		} elseif($this->uri->segment(3)=="suggest") {
			$this->suggest();
			return true;
		} elseif($this->uri->segment(2)=="simple") { //A simple list
			$this->simple();
			return true;
		} elseif($this->uri->segment(3)=="deepsearch") { //A simple list
			$this->deepsearch();
			return true;
		}
		
		$this->paginate();
		$data["content"]=$this->content->getAll($this->_pg_perpage, $this->_pg_offset);
		$data["multiple"]=$this->uri->segment(3);
		$data["contenttype"]="{$this->_contenttypeurlid}";
		$data["menu1_active"]="edit";
		$data["menu2_active"]="edit/{$this->_contenttypeurlid}";
		if ($this->exists->view("content/{$this->_contenttypeurlid}/selectcontainer")) {
			$this->load->view("content/{$this->_contenttypeurlid}/selectcontainer",$data);
		} else {
			$this->load->view("content/default/selectcontainer",$data);
		}
	}
	
	/**
	 * search function.
	 * 
	 * @access public
	 * @return void
	 */
	public function search() {
		$this->load->library("search");
		$this->_pg_offset=$this->uri->segment(6);
		$config['uri_segment'] = 6;
		$s=$this->uri->segment(4);
		if (($s=="pg") || ($s=="undefined")) {
			$s="";
			$this->_pg_offset=$this->uri->segment(5);
			$config['uri_segment'] = 5;
		}
		$result=$this->search->dosearch($this->_contenttypeurlid,$s, $this->_pg_perpage, $this->_pg_offset);
		$this->load->library('pagination');
		
		$config['num_links'] = $this->_pg_numlinks;
		$config['base_url'] = "/list/".$this->uri->segment(2)."/".$this->uri->segment(3)."/".$s."/pg";
		$config['total_rows'] = $result["count"];
		$config['per_page'] = $this->_pg_perpage;
		$this->pagination->initialize($config);
		$result["pagination"]=$this->pagination->create_links();
		print json_encode($result);
	}
	
	public function suggest() {
		$this->load->library("search");
		$s=$this->input->get("term");
		$type=$this->uri->segment(2);
		$limit=20;
		if ($type=="all") {
			print json_encode($this->search->suggest($type,$s,$limit));
		} elseif($type=="mixed") {
			$segs=$this->uri->segment_array();
			$segs=array_slice($segs, 3);
			print json_encode($this->search->suggest($segs,$s,$limit));
		} else {
			print json_encode($this->search->suggest($this->_contenttypeurlid,$s,$limit));
		}
	}
	
	/**
	 * item function.
	 * 
	 * @access public
	 * @return void
	 */
	public function item() {
		$data["item"]=$this->content->getByIdORM($this->uri->segment(4),$this->_contenttype->id);
		if ($this->exists->view("content/{$this->_contenttypeurlid}/selectitem")) {
			$this->load->view("content/{$this->_contenttypeurlid}/selectitem",$data);
		} else {
			$this->load->view("content/default/selectitem",$data);
		}
	}
	
	/**
	 * paginate function.
	 * 
	 * @access public
	 * @return void
	 */
	public function paginate() {
		$this->_pg_offset=$this->uri->segment(5);
		$this->load->library('pagination');
		$config['uri_segment'] = 5;
		$config['num_links'] = $this->_pg_numlinks;
		$config['base_url'] = "/list/".$this->uri->segment(2)."/".$this->uri->segment(3)."/pg/";
		//$config['total_rows'] = $this->{$this->_model}->count();
		$config['total_rows'] = $this->content->count();
		$config['per_page'] = $this->_pg_perpage;
		$this->pagination->initialize($config);
	}
	
	public function simple() {
		$this->_pg_perpage=100;
		
		$data["search"]="";
		$segments=$this->uri->segment_array();
		$searchcheck=array_slice($segments,-2);
		if ($searchcheck[0]=="search") {
			$s=rawurldecode($searchcheck[1]);
			$this->load->library('pagination');
			$data["content"]=$this->content->search($this->_contenttypeurlid,$s,$this->_pg_perpage, $this->_pg_offset);
			$config['num_links'] = $this->_pg_numlinks;
			$config['base_url'] = "/list/".$this->uri->segment(2)."/".$this->uri->segment(3)."/".$searchcheck[1]."/pg";
			$config['total_rows'] = $this->content->searchCount($this->_contenttypeurlid,$s,$this->_pg_perpage, $this->_pg_offset);
			$config['per_page'] = $this->_pg_perpage;
			$data["search"]=$s;
			$this->pagination->initialize($config);
		} else {
			$this->paginate();
			$data["content"]=$this->content->getAll($this->_pg_perpage, $this->_pg_offset);
		}
		//$data["action"]=$this->uri->segment(3);
		$data["contenttype"]="{$this->_contenttypeurlid}";
		$this->load->view("content/default/simplelist",$data);
	}
	
	public function deepsearch() {
		$this->load->library("search");
		$s=$this->input->get("term");
		$type=$this->uri->segment(2);
		$limit=20;
		print json_encode($this->search->smart_search($type,$s,$limit));
	}

}

/**
 * TL_Controller_CRUD class.
 *
 * A base class that extends CodeIgniter's Controller to offer a quick implementation of all our CRUD functionality
 * 
 * @extends CI_Controller
 */
class TL_Controller_CRUD extends CI_Controller {
	
	/**
	 * _contenttype
	 * 
	 * (default value: false)
	 * 
	 * @var object
	 * @access protected
	 */
	protected $_contenttype=false;
	
	protected $_contenttypeurlid=false;
	
	/**
	 * __construct function.
	 * 
	 * You can set a content type as a parameter or let the controller try figure it out from the uri. 
	 * The constructor also tells everyone what you're doing through the Stomp server.
	 * It sets a userdata contenttype which is used when you switch between creating and editing to keep your state.
	 *
	 * @access public
	 * @param bool $contenttype. (default: false)
	 * @return void
	 */
	public function __construct($contenttype=false) {
		parent::__construct();
		$this->load->model("model_content");
		if (!empty($contenttype)) {
		//Try get the contenttype from our constructor
			$this->_contenttypeurlid=$contenttype;
		} else {
		//Try get contenttype from uri segment
			$segs=$this->uri->segment_array();
			$content_types=$this->model_content->get_content_types();
			foreach($content_types as $content_type) {
				if (in_array($content_type->urlid, $segs)) {
					$this->_contenttypeurlid=$content_type->urlid;
				}
			}
		}
		if (empty($this->_contenttypeurlid)) {
			//Only exception is "All" that we use for searching
			if (!in_array("all",$segs) && !in_array("mixed",$segs)) {
				show_error("Must set content type");
			}
		}
		$this->_contenttype=$this->db->get_where("content_types",array("urlid"=>$this->_contenttypeurlid))->row();
		$this->load->model($this->_contenttype->model, "content");
		
		$this->content->setContentType($this->_contenttypeurlid);
		$this->content->setPlatform($this->platforms->id());
		
		$this->session->set_userdata("contenttype",$this->_contenttypeurlid);
		//Send where we are thru Stomp
		$stompinfo=array("user"=>$this->model_user->get_by_id($this->session->userdata("id")), "url"=>$this->uri->segment_array());
		$this->messaging->post_message("all",json_encode($stompinfo));
	}
	
	public function cachereset($contenttype_urlid, $urlid) {
		$this->load->library("tlpicture");
		$this->load->library("memcacher");
		$this->memcacher->clearById($contenttype_urlid, $urlid);
		$this->memcacher->clearPic($contenttype_urlid, $urlid);
		$this->tlpicture->clearCache($urlid, $contenttype_urlid);
	}
	
	public function cachesave($contenttype_urlid, $urlid) {
		$this->load->library("memcacher");
		$this->memcacher->addById($contenttype_urlid, $urlid);
		return true;
	}
	
	/**
	 * checkCallback function.
	 * 
	 * @access protected
	 * @param mixed $callbackname
	 * @param mixed &$returndata
	 * @return void
	 */
	protected function checkCallback($callbackname,&$returndata) {
		if (method_exists($this,$callbackname)) {
			return call_user_func_array(array(&$this,$callbackname),array($returndata));
		}
	}
}
?>