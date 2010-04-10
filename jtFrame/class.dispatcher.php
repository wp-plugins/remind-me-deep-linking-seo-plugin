<?php
 class jtDispatcher {
	public $isAdmin;
 	public $mainFrame;
 	public $sql;
 	public $rows;
 	public $output;
 	public $config;
 	public $view;
 	public $model;

 	/**
 		Constructor function
 		@param object the database connection
 		@param object the template object
 		@param array config data
 	*/
 	public function __construct($admin=false){
		$this->mainFrame = jtMainFrame::getInstance();
 		$this->view = "";
 		$this->isAdmin = $admin;
 	}
 	
 	public function execute(){
 		
 		if ($this->isAdmin){
 			$this->adminInit();
 			//$this->adminDispatch();
 			
 		} else {
 			$this->init();
 			$this->dispatch();
 		}
 	}
 	
 	public function adminInit(){
 		return;
	}
	
	public function adminDispatch(){
		return;
	}
	
	public function init(){
		return;
	}
	
	public function dispatch(){
		return;
	}	
	
 }
?>