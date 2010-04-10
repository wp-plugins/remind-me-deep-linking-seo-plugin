<?php
/**
 * Our mainframe class - sets up all our data to be used in the rest of the app/plugin
 *
 */
class jtMainFrame {
	// Store the single instance of this class
    protected static $_instance;
    public $pluginPath;
	public $db;
	public $config;
	public $tmpl;
	public $module;
	public $task;
	public $page;
	public $popup;
	public $ajax;
	public $perPage;
	public $pageStart;
	public $sortBy;
	public $sortOrder;
	public $json=null;
	public $version;
	public $_request;
	public $_sef;

	private function __construct($pluginPath=null){
		global $wpdb;
		/* determine which version we're running */
		$this->getVersion();
		
		/* connect to the database */
		$this->db = $wpdb;
		
		/* get all our dependencies */
		$this->__includes();
		
		//$this->session = flexiSession::getInstance();
		if ($pluginPath !== null){
			$this->pluginPath = $pluginPath;
		}
		
		/** initialise some common request directives */
		$this->task = jtUtility::getParam( $_REQUEST, 'jt_task');
		$this->page = jtUtility::getParam( $_REQUEST, 'jt_page');
		$this->module = jtUtility::getParam( $_REQUEST, 'jt_module');		
		$this->popup = jtUtility::getParam( $_REQUEST, 'jt_popup', 0);
		$this->ajax = jtUtility::getParam($_REQUEST, "jt_ajax", null);
		$this->json = jtUtility::getParam($_REQUEST, "jt_json", null);
		$this->perPage = intval(jtUtility::getParam($_REQUEST, "jt_perPage"));
		$this->pageStart = intval(jtUtility::getParam($_REQUEST, "jt_pageStart",1));
		$this->sortBy = jtUtility::getParam($_REQUEST, "jt_sb");
		$this->sortOrder = jtUtility::getParam($_REQUEST, "jt_ord");
		
	}
	
	/* 
	 * Implementation of the singleton design pattern
	 * See http://www.talkphp.com/advanced-php-programming/1304-how-use-singleton-design-pattern.html 
	 */	
	public function newInstance($pluginPath=null) {	
		self::$_instance = new self($pluginPath);
		return self::$_instance;
    }
    
/* 
	 * Implementation of the singleton design pattern
	 * See http://www.talkphp.com/advanced-php-programming/1304-how-use-singleton-design-pattern.html 
	 */	
	public static function getInstance($pluginPath=null) {		
        if (null === self::$_instance) {
            self::$_instance = new self($pluginPath);
        }

        return self::$_instance;
    }
	
	private function __includes(){	
				
		$this->loadInclude("jtFrame","class.utility.php");
		$this->loadInclude("jtFrame","class.model.php");
		$this->loadInclude("jtFrame","class.loader.php");
		$this->loadInclude("jtFrame","class.dispatcher.php");
		$this->loadInclude("jtFrame","class.view.php");
		$this->loadInclude("jtFrame", "class.template.php");
		$this->loadInclude("jtFrame/db", "class.hmdSqlSelect.php");
		$this->loadInclude("", "dispatcher.php");
				
	}
	
	public function getVersion() {
		if(!function_exists('get_plugin_data')) {
			if(file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) {
				require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}
			else {
				return "Error!";
			}
		}
		$data = get_plugin_data(__FILE__);	
		$this->version = $data['Version'];
	}
	
	public function loadInclude($path, $file, $vars=false, $view=null){
		if ($vars){
			extract($vars, EXTR_PREFIX_SAME, "tmpl");
		}
		$path_to_file = ($path) ? JT_PATH . trailingslashit($path) . $file : JT_PATH . $file;
		if (file_exists($path_to_file)){
			include_once($path_to_file);
		} else {
			trigger_error('jtMainFrame::loadInclude - Could not include ' . $path_to_file, E_USER_ERROR);
		}
	}
	
}
?>