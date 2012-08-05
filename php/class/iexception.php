<?php

class iexception extends Exception {
	public $msg;
	public $cls;
	
	public function __construct($msg, $cls = NULL) {
		$this->msg = $msg;
		$this->cls = $cls;
        $str = '['.$msg.'] '.$_SERVER['REQUEST_URI'];
        if($cls) $str .= ' MOD: '.$cls->imodule_name.' ARGS: '.print_r($cls->args, true).' IDLE: '.print_r($cls->idle, true);
		parent::__construct($str);
	}
}

?>
