<?php
 class jtView {
	public $isAdmin;
 	public $result;
 	public $mainFrame;
 	public $tmpl;
 	public $tmplUrl;
 	public $searchUrl;
 	public $tmplVars;
 	public $templatePath;
 	public $totalRows;
 	public $sql;
 	public $rows;
 	public $output;
 	public $styles;
 	public $scripts;
 	public $config;

 	/**
 		Constructor function
 		@param object the database connection
 		@param object the template object
 		@param array config data
 	*/
 	public function __construct($admin=false){
		$this->mainFrame = jtMainFrame::getInstance();
 		
 		$this->output = "";
 		$this->rows = "";
 		$this->styles = array();
 		$this->scripts = array();
 		$this->options = array();
 		$this->isAdmin = $admin;
 		$this->tmplUrl = get_option('siteurl') . "/wp-content/plugins/" . $this->mainFrame->pluginPath . "/template/"; 
 		
 		$this->tmplVars = array();

		$this->searchUrl = "module=" . $this->mainFrame->module . "&page=" . $this->mainFrame->page . "&task=" . $this->mainFrame->task . "&perpage=" . $this->mainFrame->perPage . "&pagestart=" . $this->mainFrame->pageStart;


 	}
 	
 	public function addCss($style, $file=null){
 		if (is_array($style)){
 			foreach($style as $name){
 				if (!is_array($name)){
 					$this->addCss($name);
 				} else {
 					$this->addCss($name[0], $name[1]);
 				}
 			}
 		} elseif (!empty($style)) { 
 			$file = ($file !== null) ? $file : $style . ".css";			
 			array_push($this->styles, array("name"=>$style, "file"=>$file));
 		}
 	}
 	
 	public function addScript($script, $file=null){
 		if (is_array($script)){
 			foreach($script as $name){
 				if (!is_array($name)){
 					$this->addScript($name);
 				} else {
 					$this->addScript($name[0], $name[1]);
 				}
 			}
 		} elseif (!empty($script)) { 	
 			$file = ($file !== null) ? $file : $script . ".js";		
 			array_push($this->scripts, array("name"=>$script, "file"=>$file));
 		}
 	}
 	
 	public function queueScripts(){ 		
 		if (is_array($this->styles) && count($this->styles) > 0){ 	
 			foreach($this->styles as $style){ 	
 				$url = 	$this->tmplUrl . "css/". $style["file"];		 				
 				echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
 			}
 		}
 		
 		if (is_array($this->scripts) && count($this->scripts) > 0){
 			foreach($this->scripts as $script){
 				$url = 	$this->tmplUrl . "js/". $script["file"];		 				
 				echo "<script type='text/javascript' src='$url'></script>\n";
 			}
 		}
 	}
 	
 	public function addTmplVar($key, $value){
 		if (!is_array($this->tmplVars)){
 			$this->tmplVars = array($key=>$value); 			
 		} else {
 			$this->tmplVars[$key] = $value;
 		}
 	}
 	
 	public function getTemplate($tmpl, $clearVars=true, $queueScripts=false){
 		$globals  = $this->config;
 		$globals["tmplUrl"] = $this->tmplUrl;
 		//$globals["popup"] = $this->mainFrame->popup;		
		$content = new jtTemplate(null, $this);		
		$content->set('globals', $globals);
		$content->set($this->tmplVars);		
		if ($clearVars)
			$this->tmplVars = "";
			
		if ($queueScripts){
			$this->queueScripts();
		}
		
		return $content->fetch($tmpl);
	}

	public function cleanTxt($txt, $br="<br />"){

		$txt = eregi_replace("\r","",$txt);
		$txt = eregi_replace("\n",$br,$txt);
		$txt = eregi_replace("'","`",$txt);
		$txt = eregi_replace("&#039;","`",$txt);

		return $txt;
	}
	
	public function display(){
		echo $this->output;
	}
	
 	public function returnEditError($msg){
		if ($this->mainFrame->ajax !== null){
			$this->output = $msg;
		} else {
			$this->addTmplVar("error", $msg);
			$this->edit();
		}
	}
	
	public function getConfig($model, $configString){
		
		$config = get_option($configString);
		
		if (!is_array($config)){
			if (class_exists($model)){
				$model = new $model($this->mainFrame->db);
				$config = $model->getDefaults();	
			} else {
				$config = array();
			}
		}
		return $config;
	}
 }
?>