<?php

class m_password_lost extends imodule {
	use ilphp_trait;
	use im_way;
	
	public $posted = false;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	protected function INIT(&$args) {
		if(IS_LOGGED_IN) throw new iexception('ACCESS_DENIED', $this);
		$this->url = '/'.LANG.'/password_lost/';
		$this->way[] = [LS('Passwort vergessen'), $this->url];
	}
	protected function MODULE(&$args) {
		$this->im_way_title();
		return $this->ilphp_fetch('password_lost.php.ilp');
	}
	protected function POST_request(&$args) {
		if(empty($args['nick']) and empty($args['email'])) return;
		$this->posted = true;
		$this->user = db()->query("SELECT user_id, nick, email, salt, validated, MD5(CONCAT(user_id, nick, email, pass, salt, lastlogin)) hash FROM users WHERE nick='".es($args['nick'])."' AND email='".es($args['email'])."' LIMIT 1")->fetch_assoc();
		if($this->user and $this->user['validated']) {
			imail::mail(
				$this->user['email'],
				LS('Passwort bei %1% vergessen', SITE_NAME),
				LS('Wenn Du den folgenden Link besuchst wirst Du automatisch bei %1% eingeloggt. Dieser Link ist bis zu Deinem 1. Login g&uuml;ltig.

http://%2%/email_notification/login/i/%3%/h/%4%/

&Auml;ndere Dein Passwort am besten sofort nachdem Du Dich eingeloggt hast.

Dein %5%-Team', SITE_NAME, SITE_DOMAIN, $this->user['user_id'], $this->user['hash'], SITE_NAME),
				'From: '.SITE_NAME.' <'.NOREPLY_EMAIL.">\n");
		}
		return IS_AJAX ? $this->RUN('MODULE') : true;
	}
}

?>
