<?php

class m_report_page extends imodule {
	use ilphp_trait;
	
	public $error = [];
	public $success = false;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function ENGINE(&$args) {
		theme::init($this);
		$this->LANG_TIME =& G::$LANG_TIME;
		
		$this->SITE_TITLE = LS('Seite oder Inhalt melden');
		$this->META_KEYWORDS =& G::$META_KEYWORDS;
		$this->META_DESCRIPTION =& G::$META_DESCRIPTION;
		
		if(iengine::$post) iengine::$post->RUN('POST');
		
		$this->ilphp_display('report_page.php.ilp');
	}
	
	protected function POST_save(&$args) {
		if(!IS_LOGGED_IN) {
			if(!@$args['name']) $this->error['name'] = true;
			if(!@$args['email'] or !filter_var($args['email'], FILTER_VALIDATE_EMAIL)) $this->error['email'] = true;
		}
		if(!@$args['class'] or !in_array($args['class'], array('content', 'abuse', 'privacy', 'other'))) $this->error['class'] = true;
		if(!@$args['message']) $this->error['message'] = true;
		if(!@$args['captcha'] or !isset(session::$s['captcha_report_page']) or strtolower($args['captcha']) != session::$s['captcha_report_page']) $this->error['captcha'] = true;
		unset(session::$s['captcha_report_page']);
		if($this->error) return;
		
		$update = array();
		if(IS_LOGGED_IN) $update['user_id'] = USER_ID;
		else {
			$update['name'] = es($args['name']);
			$update['email'] = es($args['email']);
		}
		$this->password = random_string(10);
		$update['password'] = $this->password;
		$update['url'] = es($args['url']);
		$update['class'] = es($args['class']);
		$update['message'] = es($args['message']);
		$update = hash_to_sql($update);
		if(!IS_LOGGED_IN) $update[] = "ip=0x".bin2hex(inet6_pton($_SERVER['REMOTE_ADDR']));
		db()->query("INSERT INTO report_page SET ".implode(',', $update));
		$this->report_id = db()->insert_id;
		
		imail::mail(IS_LOGGED_IN ? user()->email : $args['email'],
			"iCom Ticket [{$this->report_id}] - Seite gemeldet",
			"Hallo,\r\n".
			"\r\n".
			"Vielen Dank fuer die Meldung. Wir werden uns so bald wie moeglich darum kuemmern.\r\n".
			"\r\n".
			"Du kannst den Status des Tickets unter folgendem Link einsehen:\r\n".
			"http://".SITE_DOMAIN."/settings/tickets/ticket_id/{$this->report_id}/".(IS_LOGGED_IN ? "" : "pw/{$this->password}/")."\r\n".
			"\r\n".
			"Dein iCom.to-Team",
			"From: iCom.to <".NOREPLY_EMAIL.">\n");
		$this->success = true;
	}
}

?>
