<?php

class m_logout extends imodule {
	use ilphp_trait;
	public function __construct() {
		parent::__construct(__DIR__);
	}
	protected function POST_logout(&$args) {
		session::$s->del_cookie_user();
		page_redir('/');
	}
	protected function MODULE(&$args) {
		return $this->ilphp_fetch('logout.php.ilp');
	}
}

?>
