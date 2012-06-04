<?php

class m_admin_email extends imodule {
	use ilphp_trait;
	
	public $sent = false;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		if(!has_privilege('email')) throw new iexception('ACCESS_DENIED', $this);
		$this->url = '/'.LANG.'/admin/email/';
	}
	
	protected function POST_send(&$args) {
		imail::mail($args['to'],
			$args['topic'],
			$args['message']."\n\n".LS('Um auf diese eMail zu Antworten benutze das Kontaktformular auf iCom.to')."\n",
			"From: iCom.to <".$args['from'].">\n");
		$this->sent = true;
		if(IS_AJAX) return LS('E-Mail verschickt');
	}
	
	protected function MODULE(&$args) {
		set_site_title(array(LS('Admin'), LS('E-Mail schreiben')));
		return $this->ilphp_fetch('email.php.ilp');
	}
}

?>
