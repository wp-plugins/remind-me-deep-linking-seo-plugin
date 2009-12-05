<?php
	require_once "class.hmdSql.php";

	class hmdSqlWhere extends hmdSql {
		var $items;
		var $type;

		function hmdSqlWhere($type="AND") {
			$this->items = array();
			$this->setType($type);
		}

		function add($item) {
			if (empty($item)){
				return false;
			}
			
			if (!in_array($item, $this->items)) {
				$this->items[] = $item;
				return true;
			}
			return false;
		}

		function addWhere($where) {
			$this->add($where->toString());
		}

		function addEquals($field, $value, $table=null) {
			$field = $this->escape($field);
			$value = $this->escape($value);
			if ($table) $table = $this->escape($table);

			$this->add(($table ? "`$table`." : "") . "`$field` = '$value'");
		}

		function addNotEquals($field, $value, $table=null) {
			$field = $this->escape($field);
			$value = $this->escape($value);
			if ($table) $table = $this->escape($table);

			$this->add(($table ? "`$table`." : "") . "`$field` <> '$value'");
		}

		function addIsNull($field, $table=null) {
			$field = $this->escape($field);
			if ($table) $table = $this->escape($table);

			$this->add(($table ? "`$table`." : "") . "`$field` IS NULL");
		}

		function addIsNotNull($field, $table=null) {
			$field = $this->escape($field);
			if ($table) $table = $this->escape($table);

			$this->add(($table ? "`$table`." : "") . "`$field` IS NOT NULL");
		}

		function addLike($field, $value, $table=null, $matchall=false) {
			$field = $this->escape($field);
			$value = $this->escape($value);
			if ($table) $table = $this->escape($table);

			$this->add(($table ? "`$table`." : "") . "`$field` LIKE '$value'");

			if ($matchall){
				$this->add(($table ? "`$table`." : "") . "`$field` LIKE '%$value'");
				$this->add(($table ? "`$table`." : "") . "`$field` LIKE '$value%'");
				$this->add(($table ? "`$table`." : "") . "`$field` LIKE '%$value%'");
			}
		}

		function addIn($field, $value, $table=null) {
			$field = $this->escape($field);
			$value = $this->escape($value);
			if ($table) $table = $this->escape($table);

			$this->add(($table ? "`$table`." : "") . "`$field` IN ($value)");
		}


		function getType() {
			return $this->type;
		}

		function setType($type) {
			if (strcasecmp($type, "AND") == 0 || strcasecmp($type, "OR") == 0) {
				$this->type = $type;
				return true;
			}
			return false;
		}

		function toString() {
			$sql = "";
			if (!is_array($this->items) || count($this->items) <= 0){
				return "";
			}
			foreach ($this->items as $item) {
				$sql .= ($sql ? " $this->type " : "") . "($item)";
			}
			return $sql;
		}
	}
?>
