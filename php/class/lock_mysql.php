<?php

trait lock_mysql {
	public $lock_id = NULL;
	private $lock_data;
	private static $locks = array();
	public function lock_set_id($lock_data) {
		if($this->lock_id) $this->lock_release();
		$this->lock_data = $lock_data;
		$this->lock_id = hash('adler32', $lock_data);
	}
	public function lock_is_locked() {
		if(!$this->lock_id) return;
		if(isset(self::$locks[$this->lock_id])) return false;
		return db()->query("SELECT IS_USED_LOCK('".$this->lock_id."') AS used")->fetch_object()->used;
	}
	public function lock_set() {
		if(!$this->lock_id) return;
		if(isset(self::$locks[$this->lock_id])) $this->lock_release();
		#echo "set..."; flush(); $ts = get_militime();
		db()->query("/*".db()->escape_string(str_replace('LANG', '', str_replace('../templates_cache/', '', $this->lock_data)))."*/ SELECT GET_LOCK('".$this->lock_id."', 120)");
		self::$locks[$this->lock_id] = $this->lock_id;
		#echo "done in ".sub_militime($ts, get_militime()),"..."; flush();
	}
	public function lock_release() {
		if(!$this->lock_id) return;
		if(!isset(self::$locks[$this->lock_id])) return;
		#echo "release..."; flush();
		db()->query("SELECT RELEASE_LOCK('".$this->lock_id."')");
		unset(self::$locks[$this->lock_id]);
	}
	public function __destruct() {
		$this->lock_release();
	}
	public static function lock_destroy_all() {
		foreach(self::$locks as $k=>$v) {
			#db()->query("INSERT IGNORE INTO _errors SET id='lock_".$k."_".time()."', err='lock ".es($v)." was not released'");
			db()->query("SELECT RELEASE_LOCK('".$k."')");
		}
	}
}

?>
