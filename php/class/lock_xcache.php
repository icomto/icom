<?php

trait lock_xcache extends lock_base {
	public $lock_id = NULL;
	private static $locks = array();
	public function lock_set_id($data) {
		if($this->lock_id) $this->lock_release();
		$this->lock_id = 'lock_'.hash('adler32', $data);
	}
	public function lock_is_locked() {
		if(!$this->lock_id) return;
		return xcache_isset($this->lock_id);
	}
	public function lock_set() {
		if(xcache_isset($this->lock_id)) $this->lock_release();
		while(xcache_isset($this->lock_id)) usleep(10000);
		#echo "set..."; flush(); $ts = get_militime();
		xcache_set($this->lock_id, true, 120);
		lock_xcache::$locks[$this->lock_id] = true;
		#echo "done in ".sub_militime($ts, get_militime()),"..."; flush();
	}
	public function lock_release() {
		if(@lock_sem::$locks[$this->lock_id]) {
			#echo "release..."; flush();
			unset(lock_xcache::$locks[$this->lock_id]);
			xcache_unset($this->lock_id);
		}
	}
	public function __destruct() {
		$this->lock_release();
	}
	public static function lock_destroy_all() {
		foreach(lock_xcache::$locks as $k=>$v) {
			xcache_unset($k);
		}
	}
}

?>
