<?php
	require_once "class.hmdSqlQuery.php";
	require_once "class.hmdSqlWhere.php";

	class hmdSqlSelect extends hmdSqlQuery {
		var $fields;
		var $table;
		var $joins;
		var $where;
		var $orderBy;
		var $groupBy;
		var $limit;
		var $distinct;

		function hmdSqlSelect(&$db, $table, $alias=null, $distinct=null) {
			parent::hmdSqlQuery($db);

			$this->fields = array();
			$this->customfields = array();
			$this->joins  = array();
			$this->where  = new hmdSqlWhere();

			$table = $this->escape($table);
			$alias = $this->escape($alias);

			$this->table = "$table";
			if ($alias) {
				$this->table .= " AS $alias";
			}

			if($distinct){
				$this->distinct = " DISTINCT ";
			}
		}

		function addField($field, $table=null, $alias=null) {
			$found = false;

			foreach ($this->fields as $i) {
				if ($i["field"] == $field && $i["table"] == $table && $i["alias"] == $alias) {
					$found = true;
					break;
				}
			}

			if (!$found) {
				$this->fields[] = array("field" => $field, "table" => $table, "alias" => $alias);
			}
		}

		function addCustomField($field, $alias=null) {
			$found = false;

			foreach ($this->customfields as $i) {
				if ($i["field"] == $field && $i["alias"] == $alias) {
					$found = true;
					break;
				}
			}

			if (!$found) {
				$this->customfields[] = array("field" => $field, "alias" => $alias);
			}
		}

		function addFields($fields, $table=null) {
			foreach ($fields as $field) {
				$this->addField($field, $table);
			}
		}

		function addLeftJoin($table, $alias, $field, $table2, $field2) {
			$table  = $this->escape($table);
			$alias  = $this->escape($alias);
			$field  = $this->escape($field);
			$table2 = $this->escape($table2);
			$field2 = $this->escape($field2);

			$join = "LEFT JOIN $table AS $alias ON $alias.$field = $table2.$field2";

			if (!in_array($join, $this->joins)) {
				$this->joins[] = $join;
			}
		}

		function setOrderBy($field, $table=null, $direction="ASC") {
			$field = $this->escape($field);
			if ($table) {
				$table = $this->escape($table);
			}

			if (strcasecmp($direction, "ASC") == 0 || strcasecmp($direction, "DESC") == 0) {
				$this->orderBy = "ORDER BY " . ($table ? "$table." : "") . "$field $direction";
			}
		}

		function setOrderByRandom() {
			$this->orderBy = "ORDER BY RAND()";
		}

		function setOrderByTxt($txt) {
			$this->orderBy = "ORDER BY $txt";
		}

		function setGroupBy($field, $table=null) {
			$field = $this->escape($field);
			if ($table) {
				$table = $this->escape($table);
			}

			$this->groupBy = "GROUP BY " . ($table ? "$table." : "") . "$field";
		}

		function setLimit($limit, $start=null) {
			if (is_numeric($limit) && ($start != null || is_numeric($start))) {
				$this->limit = "LIMIT " . ($start != null ? "$start, " : "") . $limit;
			}
		}

		function clearLimit(){
			$this->limit = null;
		}

		function toString($count=false) {
			$fieldSql = "";

			if ($count) {
				$fieldSql = "COUNT(*)";
			} else {
				foreach ($this->fields as $field) {
					if ($fieldSql) {
						$fieldSql .= ", ";
					}
					if ($field["table"]) {
						$fieldSql .= "" . $this->escape($field["table"]) . ".";
					}
					$fieldSql .= "" . $this->escape($field["field"]) . "";
					if ($field["alias"]) {
						$fieldSql .= " AS " . $this->escape($field["alias"]) . "";
					}
				}

				if ($fieldSql == "") {
					$fieldSql = "*";
				}

				foreach ($this->customfields as $customfield) {
					if ($fieldSql) {
						$fieldSql .= ", ";
					}
					$fieldSql .= $customfield["field"];
					if ($customfield["alias"]) {
						$fieldSql .= " AS " . $this->escape($customfield["alias"]) . "";
					}
				}
			}

			$sql = "SELECT $this->distinct $fieldSql FROM $this->table";

			$where = $this->where->toString();

			foreach ($this->joins as $join) {
				$sql .= " " . $join;
			}

			if ($where)         $sql .= " WHERE " . $where;
			if ($this->groupBy) $sql .= " $this->groupBy";
			if ($this->orderBy) $sql .= " $this->orderBy";
			if (!$count && $this->limit) $sql .= " $this->limit";

			return $sql;
		}

		function getOptions($selected=null, $valueField="value", $titleField="title") {
			$table  = $this->getAssocTable();
			$markup = "";

			foreach ($table as $row) {
				$value = htmlspecialchars($row[$valueField]);
				$title = htmlspecialchars($row[$titleField]);
				$selectedMarkup = $selected != null && $selected == $row[$valueField] ? " selected=\"selected\"" : "";
				$markup .= "<option value=\"$value\"$selectedMarkup>$title</option>";
			}

			return $markup;
		}

		function getValue() {
			return $this->db->get_var($this->toString());
		}

		function getColumn() {
			return $this->db->get_col($this->toString());
		}

		function getObjectRow() {
			return $this->db->get_row($this->toString());
		}

		function getAssocRow() {
			return $this->db->get_row($this->toString(), ARRAY_A);
		}

		function getNumericRow() {
			return $this->db->get_row($this->toString(), ARRAY_N);
		}

		function getObjectTable() {
			return $this->db->get_results($this->toString());
		}

		function getAssocTable() {
			return $this->db->get_results($this->toString(), ARRAY_A);
		}

		function getNumericTable() {
			return $this->db->get_results($this->toString(), ARRAY_N);
		}

		function getCount() {			
			return $this->db->get_var($this->toString(true));
		}
	}
?>