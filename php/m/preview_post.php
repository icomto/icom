<?php

class m_preview_post extends imodule {
	public function __construct(&$user = NULL) {
		parent::__construct(__DIR__);
	}
	protected function AJAX($args) {
		return ubbcode::add_smileys(ubbcode::compile($_POST['content']));
	}
}

?>
