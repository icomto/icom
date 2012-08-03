<?php

/*
 * ArrayClass that knows when values are changed
 */

abstract class ArrayMonitored extends ArrayClass {
	protected $is_child = false;

	private $position = 0;

	protected abstract function onArrayChanged($k);
	protected abstract function onArrayUnchanged();

	public function __construct($_acd = array(), $is_child = false) {
		$this->is_child = $is_child;
		parent::set($_acd);
	}

	public function offsetSet($k, $v) {
		if(parent::offsetSet($k, $v)) $this->onArrayChanged($k);
	}
	public function offsetUnset($k) {
		if(parent::offsetUnset($k)) $this->onArrayChanged($k);
	}

	public function set($_acd) {
		parent::set($_acd);
		if(!$this->is_child) $this->onArrayUnchanged();
	}
}

?>
