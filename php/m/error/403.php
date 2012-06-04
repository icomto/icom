<?php

require_once 'error.php';

class m_error_403 extends m_error {
	public function __construct() {
		parent::__construct();
		$this->code = '403 Forbidden';
		$this->title = LS('403 - Zugriff verweigert');
		$this->text = LS('Du hast nicht die n&ouml;tigen Rechte um diese Seite anzuzeigen');
	}
}

?>
