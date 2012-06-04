<?php

class m_admin_invites extends im_tabs {
	use ilphp_trait;
	
	public $sent = false;
	public $create = '';
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		if(!has_privilege('inviter')) throw new iexception('ACCESS_DENIED', $this);
		
		$this->url = '/'.LANG.'/admin/invites/';
		$this->way[] = [LS('Admin'), ''];
		$this->way[] = [LS('Invites'), $this->url];
		
		$this->im_tabs_add('requests', LS('Anfragen'), TAB_SELF);
		$this->im_tabs_add('send', LS('Versenden'), TAB_SELF);
		$this->im_tabs_add('create', LS('Erstellen'), TAB_SELF);
		
		parent::INIT($args);
	}
	
	protected function TAB_requests($args) {
		$this->invite_requests = db()->query("SELECT * FROM invite_requests WHERE status='requested' ORDER BY requesttime DESC");
		return $this->ilphp_fetch('invites.php.requests.ilp');
	}
	private function TAB_requests_POST_request(&$args) {
		$id = (int)@$args['id'];
		$status = ($args['status'] == 'accepted' ? 'accepted' : 'rejected');
		bigbrother('invite_request', array($id, $status));
		$rv = db()->query("SELECT * FROM invite_requests WHERE id='$id' LIMIT 1")->fetch_assoc();
		if(!$rv or $rv['status'] != 'requested') return;
		
		db()->query("UPDATE invite_requests SET editingtime=CURRENT_TIMESTAMP, status='$status' WHERE id='$id' LIMIT 1");
		if($status == 'accepted') {
			$code = create_invite_code();
			db()->query("UPDATE invite_requests SET editingtime=CURRENT_TIMESTAMP, status='$status', code='$code' WHERE id='$id' LIMIT 1");
			imail::mail($rv['email'],
				"Dein Invite-Code für iCom.to",
				"Hallo,\r\n".
				"\r\n".
				"Du hast eine Anfrage für einen Invite-Code auf iCom.to gestellt.\r\n".
				"\r\n".
				"Hier ist Dein Code: $code\r\n".
				"\r\n".
				((isset($args['message']) and $args['message']) ? "Anmerkung des Supermoderators/Administrators:\r\n--------------------------------------------\r\n".utf8_encode($args['message'])."\r\n--------------------------------------------\r\n\r\n" : "").
				"Hier gehts zur Registrierung: http://icom.to/register/\r\n".
				"\r\n".
				"Wir wünschen Dir viel Spaß!\r\n".
				"\r\n".
				"Dein iCom.to-Team",
				"From: iCom.to <".NOREPLY_EMAIL.">\n");
			if(IS_AJAX) return LS('Angenommen');
		}
		else {
			db()->query("UPDATE invite_requests SET editingtime=CURRENT_TIMESTAMP, status='$status' WHERE id='$id' LIMIT 1");
			imail::mail($rv['email'],
				"Dein Invite-Code Antrag für iCom.to wurde abgelehnt",
				"Hallo,\r\n".
				"\r\n".
				"Dein Invite-Code Antrag für wurde abgelehnt\r\n".
				"\r\n".
				((isset($args['message']) and $args['message']) ? "Anmerkung des Supermoderators/Administrators:\r\n--------------------------------------------\r\n".utf8_encode($args['message'])."\r\n--------------------------------------------\r\n\r\n" : "").
				"Dein iCom.to-Team",
				"From: iCom.to <".NOREPLY_EMAIL.">\n");
			if(IS_AJAX) LS('Abgelehnt');
		}
	}
	
	protected function TAB_send($args) {
		return $this->ilphp_fetch('invites.php.send.ilp');
	}
	protected function TAB_send_POST_send($args) {
		if(empty($args['email'])) return;
		imail::mail($args['email'],
			"Dein Invite-Code für iCom.to",
			"Hallo,

Du hast eine Anfrage für einen Invite-Code auf iCom.to gestellt.

Hier ist Dein Code: ".create_invite_code()."

Hier gehts zur Registrierung: http://icom.to/register/

Wir wünschen Dir viel Spaß!

Dein iCom.to-Team",
			'From: '.SITE_NAME.' <'.NOREPLY_EMAIL.">\n");
		$this->sent = $args['email'];
		return IS_AJAX ? $this->tab_send($args) : true;
	}
	
	protected function TAB_create($args) {
		return $this->ilphp_fetch('invites.php.create.ilp');
	}
	protected function TAB_create_POST_create($args) {
		$this->create = create_invite_code();
		return IS_AJAX ? $this->tab_create($args) : true;
	}
}

?>
