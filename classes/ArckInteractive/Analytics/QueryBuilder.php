<?php

namespace ArckInteractive\Analytics;

class QueryBuilder {

	private $from;
	private $select = [];
	private $join = [];
	private $and = [];
	private $group_by = [];
	private $order_by = [];
	private $having = [];
	private $limit;
	private $offset;

	public function from($table) {
		$prefix = elgg_get_config('dbprefix');
		$this->from = "{$prefix}{$table}";
		return $this;
	}

	public function select($select, $as = '') {
		$query = $select;
		if ($as) {
			$query .= " AS $as";
		}

		$this->select[] = $query;
		return $this;
	}

	public function join($table, $as = '', $on = '') {
		$prefix = elgg_get_config('dbprefix');
		$query = "JOIN {$prefix}{$table}";
		if ($as) {
			$query .= " AS $as";
		}
		if ($on) {
			$query .= " ON $on";
		}
		$this->join[] = $query;
		return $this;
	}

	public function where($name, $operand = '', $value = '') {

		$query = "$name";

		if ($operand && $value) {
			$query .= " $operand $value";
		}

		$this->and[] = $query;
		return $this;
	}

	public function group_by($column) {
		$this->group_by[] = $column;
		return $this;
	}

	public function having($name, $operand = '', $value = '') {

		$query = "$name";

		if ($operand && $value) {
			$query .= " $operand $value";
		}

		$this->having[] = $query;
		return $this;
	}

	public function order_by($column, $direction = 'ASC') {
		$this->order_by[] = "$column $direction";
		return $this;
	}

	public function limit($limit = 0) {
		$this->limit = (int) $limit;
		return $this;
	}

	public function offset($offset = 0) {
		$this->offset = (int) $offset;
		return $this;
	}

	public function getSql() {

		$vars = array_filter(get_object_vars($this));

		$composite = count($vars) > 1;

		foreach (['select', 'join', 'and', 'group_by', 'having'] as $key) {
			$this->$key = array_filter(array_unique($this->$key));
		}

		$query = '';
		if ($this->select) {
			$selects = implode(', ', $this->select);
			if ($composite) {
				$query .= "SELECT $selects";
			} else {
				return $selects;
			}
		}

		if ($this->from) {
			if ($composite) {
				$query .= " FROM $this->from";
			} else {
				return $this->from;
			}
		}

		if ($this->join) {
			$joins = implode(' ', $this->join);
			if ($composite) {
				$query .= " $joins";
			} else {
				return $joins;
			}
		}

		if ($this->and) {
			$wheres = implode(' AND ', $this->and);
			if ($composite) {
				$query .= " WHERE $wheres";
			} else {
				return $wheres;
			}
		}

		if ($this->group_by) {
			$columns = implode(',', $this->group_by);
			if ($composite) {
				$query .= " GROUP BY $columns";
			} else {
				return $columns;
			}
		}

		if ($this->having) {
			$having = implode(' AND ', $this->having);
			if ($composite) {
				$query .= " HAVING $having";
			} else {
				return $having;
			}
		}

		if ($this->order_by) {
			$columns = implode(',', $this->order_by);
			if ($composite) {
				$query .= " ORDER BY $columns";
			} else {
				return $columns;
			}
		}

		if ($this->limit || $this->offset) {
			$query .= " LIMIT $this->offset, $this->limit";
		}

		return $query;
	}

	public function __toString() {
		return $this->getSql();
	}

}
