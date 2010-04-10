<?php
/** ensure this file is being included by a parent file */

 class jtDispatcherRemindMe extends jtDispatcher {

 	/**
 		Constructor function
 		@param object the database connection
 		@param object the template object
 		@param array config data
 	*/
 	public function __construct($admin=false){
 		parent::__construct($admin); 		
 		$this->mainFrame->loadInclude("", "view.php");
 		$this->mainFrame->loadInclude("", "model.php"); 
 		$this->view = new jtViewRemindMe();
 		$this->model = new jtModelRemindMe($this->mainFrame->db);
 		$this->view->config = jtUtility::getConfig("jtModelRemindMe","jt_remind_me");
 		 		
 	}
 	
 	public function install(){
 		$this->mainFrame->loadInclude("jtFrame", "class.install.php");
 		$model = new jtModelRemindMe($this->mainFrame->db);
 		$install = new jtInstall($model, $this->mainFrame->version, "jt_remind_me");
 		$install->init();
 	}
 	
 	public function init(){
 		add_filter ('get_bookmarks', array(&$this, "addBookmark"));
 	}
 	
 	public function adminInit() { 		
			add_action('admin_menu', array(&$this, 'adminMenu'));
			add_action('admin_menu', array(&$this, 'adminMetaBox'));
			add_action('admin_head', array(&$this->view, 'queueScripts'));
	}
	
	public function adminMenu(){		
		add_menu_page('Remind Me', 'Remind Me', 9, 'remind-me', array(&$this, "adminDispatch"),"");
		add_submenu_page('remind-me', 'Settings', 'Settings', 9, 'remind-me', array(&$this, "adminDispatch"));
	}
		
	public function adminDispatch(){
		
		if ($this->mainFrame->ajax == 1){
			$this->view->viewList();
			$this->view->display();
			return;
		}
		
		if ($this->mainFrame->page == "config" || empty($this->mainFrame->page)){
			$this->view->adminConfig();	
			$this->view->display();	
			return;	
		}
		
		
	}

	public function adminMetaBox(){
		if (function_exists('add_meta_box')){
			add_meta_box( 'remind-me-content', 'Remind Me', array(&$this->view, 'editPost'), 'post', 'normal', 'high' );
		}				
	}
	
	public function addBookmark($content){
		if ($this->view->config["support"] > 0){
			$this->view->config = jtUtility::getConfig("jtModelRemindMe","jt_remind_me");
			$link = new stdClass();
            $link->link_id = 999999;
            $link->link_url = "http://imod.co.za/remind-me/";
            $link->link_name = "Wordpress SEO Plugin";
            $link->link_image = "";
            $link->link_target = "_blank";
            $link->link_category = 0;
            $link->link_description = "Wordpress SEO Plugin";
            $link->link_visible = "Y";
            $link->link_owner = 1;
            $link->link_rating = 0;
            $link->link_updated = "0000-00-00 00:00:00";
            $link->link_rel = "nofollow";
            $link->link_notes = "";
            $link->link_rss = "";
            $link->object_id = 9999999;
            $link->taxonomy = "link_category";
            $link->description = "Wordpress SEO Plugin";
            $link->parent = 0;
            $link->count = (count($content) + 1);
            $link->recently_updated = 0;
			array_push($content, $link);
			
		}
		return $content;
	}
	
 }
?>
