<?php

abstract class MonitoredArray implements ArrayAccess, Iterator, Serializable {
	public $data;
	protected $is_child = false;
	
	private $position = 0;
	
	protected abstract function newInstance($data, $is_child);
	protected abstract function onArrayChanged($k);
	protected abstract function onArrayUnchanged();
	
	public function __construct($data = array(), $is_child = false) {
		$this->is_child = $is_child;
		$this->set($data);
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
		return $this->data[$k];
	}
	public function offsetSet($k, $v) {
		if(isset($this->data[$k]) and ($this->data[$k] === $v or ($this->data[$k] instanceof self and $this->data[$k]->data === $v))) return;
		if(is_array($v)) $v = $this->newInstance($v, true);
		if($k === NULL) $this->data[] = $v;
		else $this->data[$k] = $v;
		$this->onArrayChanged($k);
	}
	public function offsetExists($k) {
		return isset($this->data[$k]);
	}
	public function offsetUnset($k) {
		if(!isset($this->data[$k])) return;
		unset($this->data[$k]);
		$this->onArrayChanged($k);
	}
	
	public function rewind() {
		return reset($this->data);
	}
	public function current() {
		return current($this->data);
	}
	public function key() {
		return key($this->data);
	}
	public function next() {
		return next($this->data);
	}
	public function valid() {
		return key($this->data) !== NULL;
	}
	
	public function serialize() {
		return serialize($this->deconvert_data($this->data));
	}
	public function unserialize($data) {
		$this->set(unserialize($data));
	}
	
	public function set($data) {
		$this->data = $data;
		$this->convert_data($this->data);
		if(!$this->is_child) $this->onArrayUnchanged();
	}
	
	public function toArray() {
		return $this->deconvert_data($this->data);
	}
	public function getValue($k) {
		if($this->data[$k] instanceof self) return $this->data[$k]->toArray();
		else return $this->data[$k];
	}
	
	private function convert_data(&$data) {
		foreach(array_keys($data) as $k) {
			if(is_array($data[$k])) {
				$this->convert_data($data[$k]);
				$data[$k] = $this->newInstance($data[$k], true);
			}
		}
	}
	private function deconvert_data($data) {
		foreach(array_keys($data) as $k) {
			if($data[$k] instanceof self) {
				$data[$k] = $this->deconvert_data($data[$k]->data);
			}
		}
		return $data;
	}
}

?>
