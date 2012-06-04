<?php

require_once 'error.php';

class m_error_403_404 extends m_error {
	public function __construct() {
		parent::__construct();
		$this->code = '403 Forbidden';
		$this->title = LS('403 oder 404 - Zugriff verweigert oder Seite nicht gefunden');
		$this->text = LS('Diese Seite existiert nicht oder Dir fehlen die n&ouml;tigen Rechte um sie anzuzeigen');
	}
}

?>
