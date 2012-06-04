<?php

require_once 'error.php';

class m_error_404_501 extends m_error {
	public function __construct() {
		parent::__construct();
		$this->code = '404 Not Found';
		$this->title = LS('404 oder 501 - Seite nicht gefunden oder interner Server fehler');
		$this->text = LS('Diese Seite existiert nicht oder bei uns ist was schiefgelaufen');
	}
}

?>
