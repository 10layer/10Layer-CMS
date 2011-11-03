<?php
	/**
	 * Rackfiles class
	 * 
	 * @extends Controller
	 */
	class Rackfiles extends CI_Controller {

		/**
		 * __construct function.
		 * 
		 * @access public
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			$this->load->library("cdn");
		}
		
		public function index() {
			print "Hello";
		}
		
		public function upload_test() {
			$this->cdn->init();
			//$this->cdn->connectBucket("test");
			$path="/var/www/virtual/test";
			$original_dir=$path;
			$bucket="test";
			if (is_dir($path)) {
				print "Checking $path\n";
				$dirs = array($path);
				while (sizeof($dirs)) {
					$dir=array_pop($dirs);
					if ($dh = opendir($dir)) {
						while ($file = readdir($dh)) {
							if ($file[0]=='.') {
								continue;
							}
							$path = $dir.'/'.$file;
							if (is_dir($path)) {
								$dirs[] = $path;
							} else {
								$file = $path;
								$object_name = ltrim(str_replace($original_dir, '', $file), '/');
								print "Uploading $object_name ($file) \n";
								$this->cdn->uploadFile($file, $bucket, $object_name);
							}
						}
					}
				}
			} else {
				print "$path isn't a directory - dying";
			}
          
		}
		
		public function upload($date=false) {
			$this->cdn->init();
			if (empty($date)) {
				$date=date("Ymd");
			}
			$rootpath="./resources/uploads/issues/";
			$path=$rootpath.$date;
			$bucket="test";
			if (is_dir($path)) {
				print "Checking $path\n";
				$dirs = array($path);
				while (sizeof($dirs) > 0) {
					$dir=array_pop($dirs);
					if ($dh = opendir($dir)) {
						while ($file = readdir($dh)) {
							if ($file[0]=='.') {
								continue;
							}
							$path = $dir.'/'.$file;
							if (is_dir($path)) {
								$dirs[] = $path;
							} else {
								$file = $path;
								$object_name = ltrim(str_replace($rootpath, '', $file), '/');
								//print "Object name: $object_name\n";
								//$object_name = "issues/$date/".$object_name;
								print "Uploading $object_name ($file) \n";
								$this->cdn->uploadFile($file, $bucket, $object_name);
							}
						}
					}
				}
			} else {
				print "$path isn't a directory - dying";
			}
		}
	}

/* End of file rackfiles.php */
/* Location: ./system/application/controllers/workers/ */