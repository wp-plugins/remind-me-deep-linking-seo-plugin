<?php
/**
* @package jtRemindMe
* @copyright (C) 2009 Christopher Mills, iMod.co.za
*/

class jtModelRemindMe extends jtModel {

	/**
	* @param database A database connector object
	*/
	public function __construct( $db ) {
		$this->mainFrame = jtMainFrame::getInstance();
		$this->_db = $db;
	}

	public function store(){
		return true;
	}

	public function quickstore(){
		return true;
	}

	public function check() {
		return true;
	}
	
	public function formatForView(){
		return;
	}
	
	public function install(){
		return true;
	}

	public function getDefaults() {
		$default = array();
		$default["perPage"] = 10;
		$default["support"] = 0;
		$default["defaultOrder"] = "post_date DESC, post_title ASC";
		$default["blogroll_url"] = "http://imod.co.za";
		$default["blogroll_title"] = "Remind Me SEO Plugin";
		$default["blogroll_rel"] = "nofollow";
		$default["blogroll_target"] = "_blank";
		$default["blogroll_description"] = "Deep Linking SEO Plugin";
		
		return $default;
		
	}
	
/**
 		Return the sql necessary to perform a search on the db
 		@param array $param
 		@return object $select
 	*/
	public function generateSQL($params=array()){			

		$select = new hmdSqlSelect($this->_db, $this->_db->prefix . "posts", "p", true);
		$select->addField("*","p");
		$select->where->addEquals("post_status", "publish", "p");
		$select->where->addEquals("post_type", "post", "p");
		
		
		$searchWhere = new hmdSqlWhere("OR");
		
		if($params["categories"] && is_array($params["categories"])){
			$select->addLeftJoin($this->mainFrame->db->prefix . "term_relationships", "tr", "object_id", "p", "ID");
			$select->addLeftJoin($this->mainFrame->db->prefix . "term_taxonomy","ttc", "term_taxonomy_id", "tr", "term_taxonomy_id");
			$select->where->addEquals("taxonomy", "category", "ttc");			
		
			$cats = implode(",", $params["categories"]);
			$searchWhere->addIn("term_id", $cats, "ttc");
		}
		
		if($params["tags"]){
			$select->addLeftJoin($this->mainFrame->db->prefix . "term_relationships", "tr_tag", "object_id", "p", "ID");
			$select->addLeftJoin($this->mainFrame->db->prefix . "term_taxonomy","tt_tag", "term_taxonomy_id", "tr_tag", "term_taxonomy_id");
			$select->addLeftJoin($this->mainFrame->db->prefix . "terms","t", "term_id", "tt_tag", "term_id");
			$select->where->addEquals("taxonomy", "post_tag", "tt_tag");

			$params["tags"] = trim(str_replace(",", '","', $params["tags"]));
			$params["tags"] = '"' . $params["tags"] . '"';		
			$searchWhere->add("`t`.`name` IN ($params[tags])");			
		}				

		// add the search text
		if (!empty($params["highlight"])){
			$params["highlight"] = jtUtility::cleanTxt($params["highlight"], "");
			$searchWhere->addLike("post_title", $params["highlight"],"p", true);
			$searchWhere->addLike("post_excerpt", $params["highlight"],"p", true);
			$searchWhere->addLike("post_content", $params["highlight"],"p", true);
			$searchWhere->addLike("post_name", $params["highlight"],"p", true);
		}

		$select->where->addWhere($searchWhere);
		//echo $select->toString();		
		return $select;
	}
}
?>