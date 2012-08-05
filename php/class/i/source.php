<?php

class i__source extends ArrayClass2 {
	protected $table = 'i_sources';
	protected $id_field = 'source_id';

	public function __construct($data = NULL) {
		$this->sql_set_fields = ['name'];
		parent::__construct($data);
	}

	public static function insert($url, $name) {
		$user_id = (IS_LOGGED_IN ? USER_ID : 0);
		$id = i__i::hash($user_id.$url.$name);
		db()->query("INSERT IGNORE INTO i_sources SET source_id='".$id."', url='".es($url)."', name='".es($name)."', user_id='$user_id'");
		return new self($id);
	}
}

?>
