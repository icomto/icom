<?php

trait lock_flock extends lock_base {
	public $lock_id = NULL;
	private static $locks = array();
	public function lock_set_id($data) {
		if($this->lock_id) $this->lock_release();
		$this->lock_id = '/tmp/lock_'.hash('adler32', $data);
	}
	public function lock_is_locked() {
		if(!$this->lock_id) return;
		if(@lock_flock::$locks[$this->lock_id]) {
			$this->lock_release();
			return false;
		}
		if(!file_exists($this->lock_id)) return false;
		lock_flock::$locks[$this->lock_id] = fopen($this->lock_id, 'w+');
		$wouldblock = false;
		if(flock(lock_flock::$locks[$this->lock_id], LOCK_EX, $wouldblock)) {
			flock(lock_flock::$locks[$this->lock_id], LOCK_UN);
			$locked = false;
		}
		else $locked = true;
		fclose(lock_flock::$locks[$this->lock_id]);
		unlink($this->lock_id);
		return $locked;
	}
	public function lock_set() {
		if(@lock_flock::$locks[$this->lock_id]) $this->lock_release();
		#echo "set..."; flush(); $ts = get_militime();
		lock_flock::$locks[$this->lock_id] = fopen($this->lock_id, 'w+');
		flock(lock_flock::$locks[$this->lock_id], LOCK_EX);
		#echo "done in ".sub_militime($ts, get_militime()),"..."; flush();
	}
	public function lock_release() {
		if(@lock_flock::$locks[$this->lock_id]) {
			#echo "release..."; flush();
			flock(lock_flock::$locks[$this->lock_id], LOCK_UN);
			fclose(lock_flock::$locks[$this->lock_id]);
			unset(lock_flock::$locks[$this->lock_id]);
			unlink($this->lock_id);
		}
	}
	public function __destruct() {
		$this->lock_release();
	}
	public static function lock_destroy_all() {
		foreach(lock_flock::$locks as $k=>$v) {
			flock($v, LOCK_UN);
			fclose($v);
			unlink($k);
		}
	}
}

?>
