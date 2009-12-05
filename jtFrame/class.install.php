<?php
class jtInstall {

	public $model;
	public $version;
	public $configStr;

	public function __construct($model, $version, $configStr="jumptag_") {
		
		$this->model = $model;
		$this->version = $version;
		$this->configStr = (!empty($configStr)) ? $configStr : "jumptag_"; 
		
	}
	
	public function init() {
		
		// If model table exists then try upgrade, otherwise create it and add defaults
//		if (checkTable()) {
//			$this->model->upgrade();
//		}
//		else {
			$this->model->install();
			$this->loadDefaults();
//		}
		
	}
	
	public function loadDefaults(){
		$defaults = $this->model->getDefaults();
		
		if (is_array($defaults)){
				add_option($this->configStr, $defaults, null, "yes");
		}
	}
	
}
?>