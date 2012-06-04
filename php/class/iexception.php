<?php

class iexception extends Exception {
	public $msg;
	public $cls;
	
	public function __construct($msg, $cls) {
		$this->msg = $msg;
		$this->cls = $cls;
		parent::__construct('['.$msg.'] '.$_SERVER['REQUEST_URI'].' MOD: '.$cls->imodule_name.' ARGS: '.print_r($cls->args, true).' IDLE: '.print_r($cls->idle, true));
	}
}

?>
