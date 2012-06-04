<?php

trait lock_sem extends lock_base {
	public $lock_id = NULL;
	private static $locks = array();
	public function lock_set_id($data) {
		if($this->lock_id) $this->lock_release();
		$this->lock_id = base_convert(hash('adler32', $data), 16, 10);
	}
	public function lock_is_locked() {
		if(!$this->lock_id) return;
		return xcache_isset($this->lock_id);
	}
	public function lock_set() {
		if(@lock_sem::$locks[$this->lock_id]) $this->lock_release();
		#echo "set..."; flush(); $ts = get_militime();
		lock_sem::$locks[$this->lock_id] = sem_get($this->lock_id, 1, 0777, true);
		sem_acquire(lock_sem::$locks[$this->lock_id]);
		xcache_set($this->lock_id, true, 120);
		#echo "done in ".sub_militime($ts, get_militime()),"..."; flush();
	}
	public function lock_release() {
		if(@lock_sem::$locks[$this->lock_id]) {
			#echo "release..."; flush();
			sem_release(lock_sem::$locks[$this->lock_id]);
			sem_remove(lock_sem::$locks[$this->lock_id]);
			unset(lock_sem::$locks[$this->lock_id]);
			xcache_unset($this->lock_id);
		}
	}
	public function __destruct() {
		$this->lock_release();
	}
	public static function lock_destroy_all() {
		foreach(lock_sem::$locks as $k=>$v) {
			sem_release($v);
			sem_remove($v);
			xcache_unset($k);
		}
	}
}

?>
