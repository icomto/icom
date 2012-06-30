<?php

class ArraySimple extends ArrayClass {
	public function offsetSet($k, $v) {
		return parent::offsetSet($k, $v);
	}
	public function offsetGet($k) {
		return @$this->_acd[$k];
	}
}

?>
