<?php

class ArrayClass2 extends ArrayClass {
	protected $table = null;
	protected $id_field = null;
	protected $sql_set_fields = [];

	public function __construct($data = NULL) {
		if(is_array($data)) parent::set($data);
		elseif($data !== null) $this->getById($data);
	}


	protected function ArrayClass2_Assert() {
		if(!$this->table) {
			throw new Exception('table not set');
		}

		if($this->id_field === null) {
			throw new Exception('where and id_field is null');
		}
	}


	public function offsetGet($k) {
		if($k == 'id' and $this->id_field) $k = $this->id_field;
		return parent::offsetGet($k);
	}

	public function offsetSet($k, $v, $where = null) {
		if($k == 'id' and $this->id_field) $k = $this->id_field;

		if(in_array($k, $this->sql_set_fields)) {
			if(!$this->table) {
				throw new Exception('table not set');
			}

			if($where === null) {
				if($this->id_field === null) {
					throw new Exception('where and id_field is null');
				}
				$where = $this->id_field."='".es($this->_acd[self::$id_field])."'";
			}

			$vv = self::value_to_sql($v);

			db()->query("UPDATE ".$this->table." SET $k=$vv WHERE $where");
		}

		return parent::offsetSet($k, $v);
	}


	protected function getById($id) {
		$this->ArrayClass2_Assert();

		$a = db()->query("SELECT * FROM ".$this->table." WHERE ".$this->id_field."='".es($id)."' LIMIT 1")->fetch_assoc();
		if(!$a) throw new Exception($this->table.'('.$id.'): not found');
		parent::set($a);
	}


	protected function addAttr(&$out, $table, $attr, $update = []) {
		$this->ArrayClass2_Assert();

		$update[$this->id_field] = $this->id;
		$update[$attr->id_field] = $attr->id;

		db()->query("INSERT IGNORE INTO $table SET ".implode(", ", self::prepare_sql_arr($update)));

		if(db()->affected_rows and $out !== null) {
			$out[$attr->id] = $attr;
		}
	}
	protected function removeAttr(&$out, $table, $attr, $where = []) {
		$this->ArrayClass2_Assert();

		$update[$this->id_field] = $this->id;
		$update[$attr->id_field] = $attr->id;

		db()->query("DELETE FROM $table WHERE ".implode(" AND ", self::prepare_sql_arr($update)));

		if($out !== null) {
			unset($out[$attr->id]);
		}
	}
	protected function getAttrs(&$out, $table, $id_field, $class, $order_by = null) {
		if($out === null) {
			$this->ArrayClass2_Assert();

			$out = [];
			$aa = db()->query("
				SELECT b.*
				FROM $table a
				JOIN ".$this->table." b USING (".$this->id_field.")
				WHERE a.".$this->id_field."='".$this->id."'".($order_by ? "
				ORDER BY ".$order_by : ""));
			while($a = $aa->fetch_assoc())
				$out[$a[$id_field]] = new $class($a);
		}

		return $out;
	}



	public static function value_to_sql($v) {
		if($v === true) return 1;
		elseif($v === false) return 0;
		elseif($v === null) return 'NULL';
		elseif(in_array(trim(strtoupper($v)), ['CURRENT_TIMESTAMP'])) return $v;
		else return "'".es($v)."'";
	}

	public static function prepare_sql_arr(&$arr) {
		$out = [];
		foreach($arr as $k=>$v) {
			$out[] = $k.'='.self::value_to_sql($v);
		return $out;
	}
}

?>
