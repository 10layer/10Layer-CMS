<?php
class MY_Config extends CI_Config {
		/**
	 * Load Config File
	 *
	 * @access	public
	 * @param	string	the config file name
	 * @param   boolean  if configuration values should be loaded into their own section
	 * @param   boolean  true if errors should just return false, false if an error message should be displayed
	 * @return	boolean	if the file was loaded correctly
	 */
	function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$file = ($file == '') ? 'config' : str_replace(EXT, '', $file);
		$found = FALSE;
		$loaded = FALSE;
		$this->_config_paths=array_reverse($this->_config_paths); //Check override configs before third party
		foreach ($this->_config_paths as $path)
		{
			$check_locations = defined('ENVIRONMENT')
				? array(ENVIRONMENT.'/'.$file, $file)
				: array($file);

			foreach ($check_locations as $location)
			{
				$file_path = $path.'config/'.$location.EXT;

				if (in_array($file_path, $this->is_loaded, TRUE))
				{
					$loaded = TRUE;
					continue 2;
				}

				if (file_exists($file_path))
				{
					$found = TRUE;
					break;
				}
			}

			if ($found === FALSE)
			{
				continue;
			}
			if (file_exists($file_path)) {
				include($file_path);
			
				if ( ! isset($config) OR ! is_array($config))
				{
					if ($fail_gracefully === TRUE)
					{
						return FALSE;
					}
					show_error('Your '.$file_path.' file does not appear to contain a valid configuration array.');
				}
				
				if ($use_sections === TRUE)
				{
					if (isset($this->config[$file]))
					{
						$this->config[$file] = array_merge($this->config[$file], $config);
					}
					else
					{
						$this->config[$file] = $config;
					}
				}
				else
				{
					$this->config = array_merge($this->config, $config);
				}
				
				$this->is_loaded[] = $file_path;
				unset($config);
				
				$loaded = TRUE;
				log_message('debug', 'Config file loaded: '.$file_path);
			}
		}

		if ($loaded === FALSE)
		{
			if ($fail_gracefully === TRUE)
			{
				return FALSE;
			}
			show_error('The configuration file '.$file.EXT.' does not exist.');
		}

		return TRUE;
	}
}
?>