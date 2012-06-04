<?php

class imodule {
	public $url;
	
	//input arguments
	public $args = [];
	public $idle = [];
	
	public $imodule_name;
	public $imodule_args = []; //output idle arguments
	
	private $imodule_run_once = [];
	
	
	public function __construct($dir = 'templates') {
		$this->ilphp_template_dir = $dir.'/';
	}
	
	//DEFAULT REQUEST:
	//1. INIT
	//2. POST
	//3. MODULE ...
	
	//IDLE REQUEST:
	//1. IDLE
	
	
	//INIT
	protected function INIT(&$args) { }
	protected function POST(&$args) {
		$fn = 'POST_'.$args['action'];
		if(!method_exists($this, $fn)) throw new imodule_exception('NOT_IMPLEMENTED '.$this->imodule_name.' => '.$fn);
		return $this->$fn($args);
	}
	protected function MODULE(&$args) { throw new imodule_exception('NOT_IMPLEMENTED '.$this->imodule_name.' => MODULE'); }
	protected function MENU(&$args) { throw new imodule_exception('NOT_IMPLEMENTED '.$this->imodule_name.' => MENU'); }
	protected function ITEM(&$args) { throw new imodule_exception('NOT_IMPLEMENTED '.$this->imodule_name.' => ITEM'); }
	protected function IDLE(&$args) { throw new imodule_exception('NOT_IMPLEMENTED '.$this->imodule_name.' => IDLE'); }
	
	//ENGINE: by default this is the only handler for a page. call inside your main code
	//POST
	//IDLE
	
	
	public function RUN($fn) {
		if(!method_exists($this, $fn)) throw new imodule_exception('NOT_IMPLEMENTED '.$this->imodule_name.' => '.$fn);
		return $this->$fn($this->args);
	}
	public function RUN_ONCE($fn) {
		if(isset($this->imodule_run_once[$fn])) return;
		$this->imodule_run_once[$fn] = true;
		return $this->RUN($fn);
	}
	public function RUN_IDLE() {
		return $this->IDLE($this->idle);
	}
	
	public function IMODULE_POST_VAR() {
		$args = func_get_args();
		if(count($args) == 1 and is_array($args[0])) $args = $args[0];
		return 'imodules/'.$this->imodule_name.'/'.implode('/', $args);
	}
	
	public function _DEBUG_PRINT($id, $var, $once = true) {
		if($once and isset($this->imodule_run_once['_DEBUG_PRINT'])) return;
		$this->imodule_run_once['_DEBUG_PRINT'] = true;
		echo '<p style="text-align:left;">', $id ? $id.': ' : '', $this->imodule_name, ' => ', print_r($this->$var, true), '</p>';
	}
}

?>
