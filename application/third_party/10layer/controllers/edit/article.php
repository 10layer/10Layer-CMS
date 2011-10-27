<?php

class Article extends TL_Controller_Edit {
	public function __construct() {
		parent::__construct();
		$this->output->enable_profiler(true);
	}
}
?>