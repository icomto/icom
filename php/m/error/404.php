<?php

require_once 'error.php';

class m_error_404 extends m_error {
	public function __construct() {
		parent::__construct();
		$this->code = '404 Not Found';
		$this->title = LS('404 - Seite nicht gefunden');
		$this->text = LS('Diese Seite existiert nicht');
	}
}

?>
