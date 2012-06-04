<?php

class m_admin_menu extends imodule {
	use ilphp_trait;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	protected function MENU(&$args) {
		return $this->ilphp_fetch('admin_menu.php.ilp');
	}
}

?>
