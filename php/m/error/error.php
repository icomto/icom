<?php

class m_error extends imodule {
	use ilphp_trait;
	use im_way;
	
	public $code;
	public $title;
	public $text;
	
	public function __construct(&$user = NULL) {
		parent::__construct(__DIR__);
	}
	protected function MODULE(&$args) {
		header($_SERVER['SERVER_PROTOCOL'].' '.$this->code);
		$this->way[] = [$this->title ? $this->title : $this->code, ''];
		$this->im_way_title();
		return $this->ilphp_fetch('error.php.ilp');
	}
}

?>
