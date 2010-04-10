<?php
	require_once "class.hmdSql.php";
	
	class hmdSqlQuery extends hmdSql {
		var $db;
		
		function hmdSqlQuery(&$db) {
			$this->db =& $db;
		}
	}
?>
