<?php
/** ensure this file is being included by a parent file */

 class jtViewRemindMe extends jtView {

 	/**
 		Constructor function
 		@param object the database connection
 		@param object the template object
 		@param array config data
 	*/
 	public function __construct($admin=false){

 		parent::__construct($admin);
 		
 		$this->search = jtUtility::getParam($_REQUEST, "searchtxt");
 		$this->config = jtUtility::getConfig("jtModelRemindMe", "jt_remind_me");
 		
 		$this->model = new jtModelRemindMe($this->mainFrame->db);
 		
 		$this->params = array(
 			"highlight"=> jtUtility::getParam($_REQUEST, "jt_highlight"),
 			"categories"=> jtUtility::getParam($_REQUEST, "post_category"),
 			"tags" => jtUtility::getParam($_REQUEST, "jt_tags") 		
 		);
 		
 		$this->perPage = ($this->mainFrame->perPage) ? $this->mainFrame->perPage : $this->config["perPage"];
 		
 		// generate our query, since we already have the necessary search criteria
 		$this->_select = $this->model->generateSQL($this->params);
 		// set our default limit
 		//$this->_select->setLimit($this->mainFrame->perPage, $this->mainFrame->pageStart);

 	}
	
	public function adminConfig(){
	 	
	 	if (isset($_POST["items"])) {	 		
	 		$items = jtUtility::getParam($_POST, "items", array());	 		
	 		if (is_array($items)){
	 			update_option("jt_remind_me", $items);
	 			$this->config = $items;
	 		}
	 		
	 		$this->addTmplVar("msg", "Options updated successfully");
	 	}
	 	$this->addTmplVar("isvalidPage", 1);
	 	$this->addTmplVar("items", $this->config);
	 	$this->output = $this->getTemplate("config.php");
	}
	
	public function editPost(){
		$this->addTmplVar("rows", $this->getList(1));		
		$list = $this->getTemplate("list.php");
		
		
		$this->addScript("remind-me-metabox");
		$this->addTmplVar("list", $list);		
		echo $this->getTemplate("metaBox.php", true, true);
	}
	
 	/**
 		Perform a search for listings and return an object - i.e. short display
 		@param int mode (0 = no formatting, 1 = formatted)
 		@return object resulting rows
 	*/
 	public function getList($mode=0) {

		// first clear our limit, since we want to get the total number of rows.
		$this->_select->clearLimit();
		//echo $this->_select->toString();
		$this->_total_rows = $this->_select->getCount();
		
		$this->_select->setLimit($this->perPage, $this->mainFrame->pageStart);
		

		if ($this->_total_rows) { // our search has returned at least 1 result
			// reset our pagination to page 1 if we have less rows available than the number of listings allowed per page
			if ( $this->_total_rows <= $this->perPage ) {
				$this->mainFrame->pageStart = 0;
				$this->_select->setLimit($this->perPage, $this->mainFrame->pageStart);
			}

			// now set our orderBy clause and re-execute our query
			$this->_select->setOrderByTxt($this->config["defaultOrder"]);

			//echo $num_rows;
			//echo $this->_select->toString(true);
			
			$rows = $this->_select->getObjectTable();

			
			// get the results in the display mode required
			switch($mode) {
				case "1":
					// formatted / modified results
			 		$output = $this->getDisplayObject($rows, 0);
			 		break;
			 	default :
			 		// raw results
			 		$output = $rows;
			 		break;
			}

		} else 	{
			$output = '';
		}

		return $output;

 	}


 	
 	
 	/**
 		Return the rows formatted and ready for output
 		@param object the list of rows to loop through
 		@param bool return single or multiple rows
 		@return object formatted row
 	*/

 	public function getDisplayObject($rows, $single=1){

		$i = 0;
		
		foreach($rows as $row) {			
			$rows[$i]->title = $this->cleanTxt($row->post_title);
			$rows[$i]->date = $row->post_modified;
			$rows[$i]->count = $i;			
			$i++;
		}

		if ($single) {
			return $rows[0];
		} else {
			return $rows;
		}

	}	

	public function cleanTxt($txt, $br="<br />"){

		$txt = eregi_replace("\r",$br,$txt);
		$txt = eregi_replace("\n","",$txt);
		$txt = eregi_replace("'","`",$txt);
		//$txt = eregi_replace("\\r",$br,$txt);
		$txt = eregi_replace("&#039;","`",$txt);

		return $txt;
	}	
	
	
	public function viewList(){
	
		$rows = $this->getList(0);
		//print_r($this->_select->toString());
		if ($this->_total_rows > 0) {				
			$displayRows = $this->getDisplayObject($rows,0);				
							
			$this->addTmplVar('numrows', count($rows));				
			$this->addTmplVar('rows', $displayRows);
			$this->output = $this->getTemplate("list.php");

		} else 	{
			$this->output = $this->getTemplate("empty.php");
		}
	
	}
	
 }

?>