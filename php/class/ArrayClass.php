<?php

class ArrayClass implements ArrayAccess, Iterator, Serializable {
	public $_acd;

	private $position = 0;

	public function __construct($_acd = array()) {
		$this->set($_acd);
	}

	protected function newChildInstance($_acd) {
		$temp = $this->_acd;
		$this->_acd = NULL;
		$new = clone $this;
		$new->set($_acd);
		$this->_acd =& $temp;
		return $new;
	}

	public function __get($k) {
		return $this->offsetGet($k);
	}
	public function __set($k, $v) {
		return $this->offsetSet($k, $v);
	}
	public function __isset($k) {
		return $this->offsetExists($k);
	}
	public function __unset($k) {
		return $this->offsetUnset($k);
	}

	public function offsetGet($k) {
		if(!isset($this->_acd[$k])) throw new Exception('OFFSET_NOT_FOUND:'.$k);
		return $this->_acd[$k];
	}
	public function offsetSet($k, $v) {
		if(isset($this->_acd[$k]) and ($this->_acd[$k] === $v or ($this->_acd[$k] instanceof self and $this->_acd[$k]->_acd === $v))) return;
		if(is_array($v)) $v = $this->newChildInstance($v);
		if($k === NULL) $this->_acd[] = $v;
		else $this->_acd[$k] = $v;
		return true;
	}
	public function offsetExists($k) {
		return isset($this->_acd[$k]);
	}
	public function offsetUnset($k) {
		if(!isset($this->_acd[$k])) return;
		unset($this->_acd[$k]);
		return true;
	}

	public function rewind() {
		return reset($this->_acd);
	}
	public function current() {
		return current($this->_acd);
	}
	public function key() {
		return key($this->_acd);
	}
	public function next() {
		return next($this->_acd);
	}
	public function prev() {
		return prev($this->_acd);
	}
	public function valid() {
		return key($this->_acd) !== NULL;
	}

	public function serialize() {
		return serialize($this->deconvert_data($this->_acd));
	}
	public function unserialize($_acd) {
		$this->set(unserialize($_acd));
	}

	public function set($_acd) {
		$this->convert_data($_acd);
		$this->_acd = $_acd;
	}

	public function toArray() {
		return $this->deconvert_data($this->_acd);
	}
	public function getValue($k) {
		if($this->_acd[$k] instanceof self) return $this->_acd[$k]->toArray();
		else return $this->_acd[$k];
	}

	private function convert_data(&$_acd) {
		if(!is_array($_acd)) throw new Exception('DATA_IS_NO_ARRAY');
		foreach(array_keys($_acd) as $k) {
			if(is_array($_acd[$k])) {
				$this->convert_data($_acd[$k]);
				$_acd[$k] = $this->newChildInstance($_acd[$k]);
			}
		}
	}
	private function deconvert_data($_acd) {
		foreach(array_keys($_acd) as $k) {
			if($_acd[$k] instanceof self) {
				$_acd[$k] = $this->deconvert_data($_acd[$k]->_acd);
			}
		}
		return $_acd;
	}
}

?>
