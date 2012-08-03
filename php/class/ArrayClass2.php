<?php

/*
 * Extended ArrayClass with some SQL improvements
 */

class ArrayClass2 extends ArrayClass {
	protected $table = null;
	protected $id_field = null;
	protected $sql_set_fields = [];

	//protected $attributes = [];

	public function __construct($data = NULL) {
		if(is_array($data)) parent::set($data);
		elseif(preg_match('~^\-?\d+(\-.+)?$~', $data)) $this->getById($data);
		elseif($data !== null) {
			$data = i__i::hash(strtolower(urldecode($data)));
			$this->getById($data);
		}
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
				$where = $this->id_field."='".es($this->_acd[$this->id_field])."'";
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


	/*public function __call($fn, $args) {
		if(!preg_match('~^(add|removeAll|remove|get|count|allow)(.+?)(s)?$~', $fn, $out)) {
			throw new Exception('Method not found: '.$fn);
		}

		if($out[1] == 'allow') {
			$out[2]{0} = strtolower($out[2]{0});
			if(!preg_match('~^(add|removeAll|remove|get|count)(.+?)(s)?$~', $out[2], $out)) {
				throw new Exception('Method not found: '.$fn);
			}
			if(!isset($this->attributes[$out[2]])) {
				throw new Exception('Method not found: '.$fn);
			}
			return (isset($attr[$out[1]]) and !is_null($attr[$out[1]])) ? true : false;
		}

		if(!isset($this->attributes[$out[2]])) {
			throw new Exception('Method not found: '.$fn);
		}
		$attr =& $this->attributes[$out[2]];

		if(!isset($attr[$out[1]]) or is_null($attr[$out[1]])) {
			throw new Exception('Method not found or access denied: '.$fn);
		}

		function csa(&$args, &$default, $i, $fb = null) {
			return isset($args[$i]) ? $args[$i] : (isset($default[$i]) ? $default[$i] : $fb);
		}

		$default =& $attr[$out[1]];
		switch($out[1]) {
		case 'add':
			//add($attr, $update = []);
			return $this->addAttrs($attr['out'], $attr['table'], csa($args, $default, 0), csa($args, $default, 1, []));
		case 'remove':
			//remove($attr, $where = []);
			return $this->removeAttrs($attr['out'], $attr['table'], csa($args, $default, 0), csa($args, $default, 1, []));
		case 'removeAll':
			//removeAll($where = []);
			return $this->removeAllAttrs($attr['out'], $attr['table'], csa($args, $default, 0, []));
		case 'get':
			//get($order_by, $select = 'b.*');
			return $this->getAttrs($attr['out'], $attr['table'], $attr['content_table'], $attr['content_id_field'], $attr['content_class'], csa($args, $default, 0), csa($args, $default, 1, 'b.*'));
		case 'count':
			//get();
			return $this->countAttrs($attr['out'], $attr['table']);
		}
	}*/

	protected function logAttrAction($table, $attr, $action, $args) {
		$user_id = (IS_LOGGED_IN ? USER_ID : 0);
		if(isset($args['user_id']) and $args['user_id'] == $user_id) {
			unset($args['user_id']);
		}
		db()->query("
			INSERT INTO i_logs
			SET
				user_id='".$user_id."',
				t=".self::value_to_sql($table).",
				content_id=".self::value_to_sql($this->id).",
				attr_id=".self::value_to_sql($attr->id).",
				action=".self::value_to_sql($action).",
				args=".self::value_to_sql($args ? json_encode($args) : ''));
	}

	protected function addAttr(&$out, &$opts, $attr, $update = []) {
		$this->ArrayClass2_Assert();

		$log_args = $update;

		$update[$this->id_field] = $this->id;
		$update[$attr->id_field] = $attr->id;

		db()->query("INSERT IGNORE INTO ".$opts['table']." SET ".implode(", ", self::prepare_sql_arr($update)));

		if(db()->affected_rows) {
			if($out !== null) {
				$out[$attr->id] = new $opts['class'](db()->insert_id);
			}
			if(!empty($opts['log'])) {
				$this->logAttrAction($opts['table'], $attr, 'add', $log_args);
			}
		}
	}
	protected function removeAttr(&$out, &$opts, $attr, $where = []) {
		$this->ArrayClass2_Assert();

		$log_args = $where;

		$where[$this->id_field] = $this->id;
		$where[$attr->id_field] = $attr->id;

		db()->query("DELETE FROM ".$opts['table']." WHERE ".implode(" AND ", self::prepare_sql_arr($where)));

		if(!empty($opts['on_remove_delete_content'])) {
			db()->query("DELETE FROM ".$opts['content_table']." WHERE ".$attr->id_field."='".self::value_to_sql($attr->id)."'");
		}

		if(db()->affected_rows && !empty($opts['log'])) {
			$this->logAttrAction($opts['table'], $attr, 'remove', $log_args);
		}

		if($out !== null) {
			unset($out[$attr->id]);
		}
	}
	protected function removeAllAttrs(&$out, &$opts, $where = []) {
		$this->ArrayClass2_Assert();

		$where[$this->id_field] = $this->id;

		if(!empty($opts['log'])) {
			$aa = db()->query("
				SELECT ".$opts['content_id_field']."
				FROM ".$opts['table']." a
				WHERE ".implode(" AND ", self::prepare_sql_arr($where)));
			while($a = $aa->fetch_assoc()) {
				$this->removeAttr($out, $opts, new $opts['class']($a), $where);
			}
		}

		$out = null;
	}
	protected function getAttrs(&$out, &$opts, $order_by = null, $select = 'b.*') {
		if($out === null) {
			$this->ArrayClass2_Assert();

			$out = [];
			$aa = db()->query("
				SELECT $select
				FROM ".$opts['table']." a
				JOIN ".$opts['content_table']." b USING (".$opts['content_id_field'].")
				WHERE a.".$this->id_field."='".$this->id."'".($order_by ? "
				ORDER BY ".$order_by : ""));
			while($a = $aa->fetch_assoc())
				$out[$a[$opts['content_id_field']]] = new $opts['class']($a);
		}

		return $out;
	}
	protected function countAttrs(&$out, &$opts) {
		if($out === null)
			return db()->query("SELECT COUNT(*) num FROM ".$opts['table']." WHERE ".$this->id_field."='".es($this->id)."'")->fetch_assoc()['num'];
		else
			return count($out);
	}



	public static function prepare_sql_arr(&$arr) {
		$out = [];
		foreach($arr as $k=>$v)
			$out[] = $k.'='.self::value_to_sql($v);
		return $out;
	}
	public static function value_to_sql($v) {
		if($v === true) return 1;
		elseif($v === false) return 0;
		elseif($v === null) return 'NULL';
		elseif(in_array(trim(strtoupper($v)), ['CURRENT_TIMESTAMP'])) return $v;
		elseif(is_numeric($v)) return $v;
		elseif(!$v) return "''";
		elseif(strpos($v, "\x00") !== false) return '0x'.($v ? '0' : bin2hex($v));
		else return "'".es($v)."'";
	}

}

?>
