<?php

class m_contact extends imodule {
	use ilphp_trait;
	use im_way;
	
	public $message = NULL;
	public $error = NULL;
	public $invite_request = NULL;
	public $reason = 'admin';
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		$this->url = '/'.LANG.'/contact/';
		$this->way[] = [LS('Kontakt mit dem Team aufnehmen'), $this->url];
	}
	
	protected function POST_invite(&$args) {
		if(!$args['email']) $this->error = LS('Du musst eine E-Mail Addresse angeben.');
		else {
			$rv = db()->query("SELECT * FROM invite_requests WHERE email='".es($args['email'])."' LIMIT 1");
			if(!$rv->num_rows) $this->error = LS('Unter der angegebenen E-Mail Addresse wurde kein Invite-Code beantragt.');
			else $this->invite_request = $rv->fetch_assoc();
		}
		return IS_AJAX ? $this->RUN('MODULE') : NULL;
	}
	protected function POST_contact(&$args) {
		if(stripos($args['message'], '<a ') !== false) $this->error = LS('Deine Nachricht wurde als Spam eingestuft. Bitte achte darauf was du eingibst.');
		elseif(!$args['email'] or !preg_match('~[^ @\t\n\r]+@[^ \t\n\r@]+\.[^ \t\n\r@]+$~i', $args['email'])) $this->error = LS('Du musst eine g&uuml;ltige E-Mail Addresse angeben.');
		elseif(!$args['message']) $this->error = LS('Du musst eine Nachricht eingeben.');
		elseif($args['reason'] == 'invite') {
			$this->reason = 'invite';
			if(db()->query("SELECT 1 FROM users WHERE email='".es(trim($args['email']))."' LIMIT 1")->num_rows) $this->error = LS('Unter der angegebenen E-Mail Addresse existiert bereits ein Account.');
			elseif(db()->query("SELECT 1 FROM invite_requests WHERE email='".es(trim($args['email']))."' LIMIT 1")->num_rows) {
				$invite_request = db()->query("SELECT code, status FROM invite_requests WHERE email='".es($args['email'])."' LIMIT 1")->fetch_object();
				$this->error = "Du hast bereits einen Invite-Code beantragt.<br>Der aktuelle Status der Bearbeitung ist \"".($invite_request->status == "requested" ? "in der Warteschlange" : ($invite_request->status == "accepted" ? "angenommen; dein Code lautet <b>\"".$invite_request->code."\"</b>" : "abgelehnt"))."\".";
			}
			else {
				db()->query("
					INSERT INTO invite_requests
					SET
						email='".es(trim($args['email']))."',
						message='".es($args['message'])."'");
				$this->message = LS('Wir werden deinen Invite-Code Antrag in den n&auml;chsten Stunden bearbeiten.<br>Du kannst den Status deines Antrags jederzeit &uuml;ber das untere Kontaktformular abrufen.');
			}
		}
		else {
			$this->reason = 'admin';
			user(56340)->pn_system(
				'[b][u]Support: '.$args['email'].' ('.$_SERVER['REMOTE_ADDR'].")[/u][/b]\n".
				$args['message']);
			$this->message = LS('Deine Nachricht wurde abgeschickt.');
		}
		
		if(!$this->error) {
			if($args['email']) imail::mail(
				$args['email'],
				'Nachricht an iCom.to',
				"Hallo,\r\n".
				"\r\n".
				"vielen Dank für Deine Nachricht.\r\n".
				"Wir werden sie so bald wie möglich beantworten.\r\n".
				"\r\n".
				"Dein iCom.to-Team",
				"From: iCom.to <".NOREPLY_EMAIL.">\r\n");
		}
		return IS_AJAX ? $this->RUN('MODULE') : NULL;
	}
	
	protected function MODULE(&$args) {
		$this->im_way_title();
		if(@$args['action'] == 'invite') $this->reason = 'invite';
		return $this->ilphp_fetch('contact.php.ilp');
	}
}

?>
