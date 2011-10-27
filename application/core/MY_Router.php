<?php
class MY_Router extends CI_Router {
	
	/**
	 * Validates the supplied segments.  Attempts to determine the path to
	 * the controller. Looks in third_party if it doesn't find it initially.
	 *
	 * @access	private
	 * @param	array
	 * @return	array
	 */
	function _validate_request($segments) {
		if (count($segments) == 0) {
			return $segments;
		}
		
		$dirs=array(APPPATH);
		
		if (defined('ENVIRONMENT') AND file_exists(APPPATH.'config/'.ENVIRONMENT.'/autoload.php')) {
			include(APPPATH.'config/'.ENVIRONMENT.'/autoload.php');
		} else {
			include(APPPATH.'config/autoload.php');
		}

		if (isset($autoload['packages'])) {
			foreach ($autoload['packages'] as $package_path) {
				$dirs[]=$package_path."/";
			}
		}
		
		$found=false;
		foreach($dirs as $dir) {
			$result=$this->_find_controller($segments, $dir);
			if (!empty($result)) {
				return $result["segments"];
			}
		}
		
		
		
		// If we've gotten this far it means that the URI does not correlate to a valid
		// controller class.  We will now see if there is an override
		if ( ! empty($this->routes['404_override']))
		{
			$x = explode('/', $this->routes['404_override']);

			$this->set_class($x[0]);
			$this->set_method(isset($x[1]) ? $x[1] : 'index');

			return $x;
		}


		// Nothing else to do at this point but show a 404
		show_404($segments[0]);
	}
	
	protected function _find_controller($segments, $dir=false) {
		if (substr($dir,-1)!="/") { //Check trailing slash
			$dir.="/";
		}
		
		// Does the requested controller exist in the root folder?
		if (file_exists($dir.'controllers/'.$segments[0].'.php')) {
			if ($dir!=APPPATH) {
				$this->directory="../../".$dir.'controllers/'; //Here we cheat a bit
				//print $this->directory;
			}
			return array("dir"=>$dir,"segments"=>$segments);
		}
		
		// Is the controller in a sub-folder?
		if (is_dir($dir.'controllers/'.$segments[0])) {
			
			// Set the directory and remove it from the segment array
			$this->set_directory($segments[0]); //Here we cheat a bit
			if ($dir!=APPPATH) {
				$this->directory="../../".$dir.'controllers/'.$segments[0]."/"; //Here we cheat a bit
			}
			$subdir=$segments[0];
			$segments = array_slice($segments, 1);
			if (count($segments) > 0) {
				
				// Does the requested controller exist in the sub-folder?
				if (!file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].'.php')) {
					//show_404($this->fetch_directory().$segments[0]);
					return false;
				}
			} else {
				
				// Is the method being specified in the route?
				if (strpos($this->default_controller, '/') !== FALSE) {
					$x = explode('/', $this->default_controller);
					$this->set_class($x[0]);
					$this->set_method($x[1]);
				} else {
					$this->set_class($this->default_controller);
					$this->set_method('index');
					if ($dir!=APPPATH) {
						$this->directory="../../".$dir.'controllers/'.$subdir."/"; //Here we cheat a bit
					}
					$segments[0]=$this->default_controller;
					if (!file_exists($dir.'controllers/'.$segments[0].'.php')) {
						return false;
					}
					return array("dir"=>$dir,"segments"=>$segments);
				}

				// Does the default controller exist in the sub-folder?
				if ( ! file_exists($dir.'controllers/'.$this->fetch_directory().$this->default_controller.'.php')) {
					return array("dir"=>$dir,"segments"=>array());
				}
			}
			return array("dir"=>$dir,"segments"=>$segments);
		}
		
		// Let's try look in our third_party spot for packages
		// CI_Controller isn't loaded yet, which means we can't get access to the CI instance,
		// so we need to check the autoload config for packages.
		// This code comes from Loader.php - ci_autoloader
		if (defined('ENVIRONMENT') AND file_exists(APPPATH.'config/'.ENVIRONMENT.'/autoload.php'))
		{
			include_once(APPPATH.'config/'.ENVIRONMENT.'/autoload.php');
		}
		else
		{
			include_once(APPPATH.'config/autoload.php');
		}

		// Autoload packages
		if (isset($autoload['packages']))
		{
			foreach ($autoload['packages'] as $package_path)
			{
				//Search third_party package for controller
				$result=$this->_find_controller($segments, $package_path);
				if (!empty($result)) {
					return $result;
				}
			}
		}
		
		//No luck
		return false;
	}
}
?>