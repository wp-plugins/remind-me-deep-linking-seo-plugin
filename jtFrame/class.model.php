<?php
/**
* jtModel Database Abstract Class.
* Heavily Based on mosDBTable Class supplied with Mambo 4.5.1 by Andrew Eddie <eddieajau@users.sourceforge.net>
* Thanks to the Mambo/Joomla devs for a brilliant package.
*
* Parent classes to all database derived objects.  Customisation will generally
* not involve tampering with this object.
* @package wp-jumptagFramework
* @author Barry Roodt (barry@jumptag.co.za)
*/
class jtModel {
	/** @var string Name of the table in the db schema relating to child class */
	public $_tbl = '';
	/** @var string Name of the primary key field in the table */
	public $_tbl_key = '';
	/** @var string Error message */
	public $_error = '';
	/** @var hmdDatabase Database connector */
	public $_db = null;
	public $mainFrame=null;

	/**
	*	Object constructor to set table and key field
	*
	*	Can be overloaded/supplemented by the child class
	*	@param string $table name of the table in the db schema relating to child class
	*	@param string $key name of the primary key field in the table
	*/
	public function __construct( $table, $key, &$db ) {
		$this->_tbl = $table;
		$this->_tbl_key = $key;
		$this->_db =& $db;
		$this->mainFrame = jtMainFrame::getInstance();
	}
	/**
	*	@return string Returns the error message
	*/
	public function getError() {
		return $this->_error;
	}
	/**
	* Gets the value of the class variable
	* @param string The name of the class variable
	* @return mixed The value of the class var (or null if no var of that name exists)
	*/
	public function get( $_property ) {
		if(isset( $this->$_property )) {
			return $this->$_property;
		} else {
			return null;
		}
	}
	/**
	* Set the value of the class variable
	* @param string The name of the class variable
	* @param mixed The value to assign to the variable
	*/
	public function set( $_property, $_value ) {
		$this->$_property = $_value;
	}
	/**
	*	binds a named array/hash to this object
	*
	*	can be overloaded/supplemented by the child class
	*	@param array $hash named array
	*	@return null|string	null is operation was satisfactory, otherwise returns an error
	*/
	public function bind( $array, $ignore="" ) {
		if (!is_array( $array )) {
			$this->_error = strtolower(get_class( $this ))."::bind failed.";
			return false;
		} else {
			return flexiUtility::bindArrayToObject( $array, $this, $ignore );
		}
	}

	/**
	*	binds an array/hash to this object
	*	@param int $oid optional argument, if not specifed then the value of current key is used
	*	@return any result from the database operation
	*/
	public function load( $oid=null ) {
		$k = $this->_tbl_key;
		if ($oid !== null) {
			$this->$k = $oid;
		}
		$oid = $this->$k;
		if ($oid === null) {
			return false;
		}

		$result = $this->_db->query("SELECT * FROM " . $this->_tbl . " WHERE " . $this->_tbl_key . "='$oid'");
		$row = $this->_db->get_row();
		return $this->bind( $row );
	}

	/**
	*	binds an array/hash to this object
	*	@param int $where mandatory argument
	*	@return any result from the database operation
	*/
	public function loadCustom( $where=null ) {
		$sql = "SELECT * FROM " . $this->_tbl;

		if($where){
			$sql .= " WHERE " . $where;
		}
		
		$result = $this->_db->query($sql);
		if ($result > 0){
			$row = $this->_db->get_row();
			return $this->bind( $row );
		}

		return false;
	}

	/**
	*	generic check method
	*
	*	can be overloaded/supplemented by the child class
	*	@return boolean True if the object is ok
	*/
	public function check() {
		return true;
	}
	/**
	 * generic clearing method
	 * can be overloaded/supplemented by the child class
	 *
	 */
	public function clear(){
		$this->_db->clearObject($this);
	}

	/**
	* Inserts a new row if id is zero or updates an existing row in the database table
	*
	* Can be overloaded/supplemented by the child class
	* @param boolean If false, null object variables are not updated
	* @return null|string null if successful otherwise returns and error message
	*/
	public function store( $updateNulls=false ) {
		$key = $this->_tbl_key;
		$fields = array();
		foreach (get_object_vars( $this ) as $k => $v) {
			if (is_array($v) || is_object($v) || ($v === NULL && !$updateNulls)) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[$k] = $v;
		}
		
		if( $this->$key) {
			$ret = $this->_db->update( $this->_tbl, $fields, array($key => $fields[$key]));
		} else {
			$ret = $this->_db->insert( $this->_tbl, $fields );
		}
		if( !$ret ) {
			$this->_error = strtolower(get_class( $this ))."::store failed <br />"; // . $this->_db->getErrorMsg();
			return false;
		} else {
			return true;
		}
	}

	/**
	*	Default delete method
	*
	*	can be overloaded/supplemented by the child class
	*	@return true if successful otherwise returns an error message
	*/
	public function delete( $oid=null ) {
		
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}

		if ($this->_db->query("DELETE FROM $this->_tbl WHERE $this->_tbl_key = '".$this->$k."' LIMIT 1")) {
			return true;
		} else {
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}
	}

	/**
	* Generic save function
	* @param array Source array for binding to class vars
	* @param string Filter for the order updating
	* @returns TRUE if completely successful, FALSE if partially or not succesful.
	*/
	public function save( $source ) {
		if (!$this->bind( $source )) {
			return false;
		}
		if (!$this->check()) {
			return false;
		}
		if (!$this->store()) {
			return false;
		}

		$this->_error = '';
		return true;
	}

	/**
	 * Generic status field update function, for things such as "published", "enabled" etc.
	 *
	 * @param int $id
	 * @param string $field
	 */
	public function updateStatus($id, $field){
		if (empty($id) || empty($field)){
			return false;
		}

		if ($this->load($id)) {

			($this->$field == 1) ? $this->$field = 0 : $this->$field = 1;
			$this->store();

		} else {
			return false;
		}

		return true;
	}
	
	/**
	 * Place holder - to be extended by child class
	 *
	 * @return mixed
	 */
	public function getDefaults(){
		return false;
	}
	
	public function getEscaped( $text ) {
		return mysql_escape_string( $text );
	}


	/**
	* Export item list to xml
	* @param boolean Map foreign keys to text values
	*/
	public function toXML( $mapKeysToText=false ) {
		$xml = '<record table="' . $this->_tbl . '"';
		if ($mapKeysToText) {
			$xml .= ' mapkeystotext="true"';
		}
		$xml .= '>';
		foreach (get_object_vars( $this ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
		}
		$xml .= '</record>';

		return $xml;
	}

	/**
	 * Clean Value - strips all unwanted characters from provided value
	 *
	 * @param unknown_type $_value
	 * @return unknown
	 */
	public function cleanValue($_value, $path=0) {

		$_value = strtolower($_value);
	    $_value = stripslashes(strip_tags($_value));
	    $_value = str_replace(" ", "-", $_value);
	    if ($path){
	    	$_value = preg_replace("/[^a-z0-9\/\-]/i","", $_value);
	    } else {
	    	$_value = preg_replace("/[^a-z0-9\-]/i","", $_value);
	    }

	    $_value = str_replace(array('delete',
	                                'DELETE',
	                                'rm -',
	                                '!',
	                                '|',
	                                '?',
	                                '&',
	                                '=',
	                                '_',
	                                '`',
	                                "'",
	                                '"',
	                                '\\\\',
	                                '\\',
	                                '//',
	                                ',',
	                                ';',
	                                ':',
	                                '*',
	                                ']',
	                                '[',
	                                '>',
	                                '<',
	                                '#',
	                                '@',
	                                '$',
	                                '%',
	                                '^'
	                               ), '', $_value);
		$_value = htmlspecialchars($_value);
	    return trim($_value);
	}

	/**
	 * Convert this object to an array.
	 * @return array An array-based representation of this object.
	 */
	public function toArray() {
		$array = array();

		foreach($this as $key => $value) {
			if(substr($key, 0, 1) != "_") {
				$array[$key] = $value;
			}
		}

		return $array;
	}

	/**
	 * Return how many rows in the current table
	 */
	public function numRows(){
		$result = $this->_db->query("SELECT COUNT(" . $this->_tbl_key . ") AS num_rows FROM " . $this->_tbl);
		if($result->size() > 0){
			$num_rows = $result->fetch();
			return $num_rows["num_rows"];
		} else {
			return 0;
		}
	}
}
?>