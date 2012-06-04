<?php

class m_register extends imodule {
	use ilphp_trait;
	use im_way;
	
	public $error = '';
	public $step = '';
	public $user = NULL;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		if(IS_LOGGED_IN) throw new iexception('ACCESS_DENIED', $this);
		$this->url = '/'.LANG.'/register/';
		$this->way[] = [LS('Registrierung'), $this->url];
	}
	
	protected function POST_register(&$args) {
		try {
			$a = db()->query("SELECT 1 FROM banned_ips WHERE ip=0x".bin2hex(inet6_pton($_SERVER['REMOTE_ADDR']))." LIMIT 1")->fetch_assoc();
			if($a) throw new Exception('REGISTER_DENIED');
			
			if(mb_strlen($args['nick']) < 3 or mb_strlen($args['nick']) > 13) throw new Exception('INVALID_USERNAME');
			$a = db()->query("SELECT user_id FROM users WHERE nick='".es($args['nick'])."' OR nick_jabber='".es($args['nick'])."' LIMIT 1")->fetch_assoc();
			if($a) throw new Exception('USERNAME_ALREADY_ESISTS');
			
			if(!filter_var($args['email'], FILTER_VALIDATE_EMAIL)) throw new Exception('INVALID_EMAIL');
			if(preg_match('~@('.implode('|', array_map('preg_quote', get_trash_mails())).')$~i', $args['email'])) throw new Exception('EMAIL_HOST_FORBIDDEN');
			
			if(mb_strlen($args['pass']) < 6) throw new Exception('PASSWORD_TOO_SHORT');
			if($args['pass'] != $args['pass2']) throw new Exception('PASSWORDS_NOT_MATCH');
			
			if(REGISTER_NEED_INVITE_CODE) {
				$rv = db()->query("SELECT id FROM invite_codes WHERE code='".es($args['invite'])."' AND NOT used LIMIT 1");
				if($rv->num_rows > 0) $invite_id = $rv->fetch_object()->id;
				else throw new Exception('INVALID_INVITE_CODE');
			}
			
			if(empty($args['rules_accepted'])) throw new Exception('RULES_NOT_ACCEPTED');
			
			if(!isset(session::$s['captcha_register']) or strtolower($args['captcha']) != strtolower(session::$s['captcha_register'])) throw new Exception('INVALID_CAPTCHA');
			
			
			if(REGISTER_NEED_INVITE_CODE) db()->query("UPDATE invite_codes SET used=1 WHERE id='".$invite_id."' LIMIT 1");
			srand(time());
			$salt = $args['nick'].$args['email'].$args['pass'];
			for($i = 0; $i < 10; $i++) $salt .= rand();
			$salt = md5($salt);
			$pass = md5($args['pass'].$salt);
			db()->query("
				INSERT INTO users
				SET
					nick='".es($args['nick'])."',
					email='".es($args['email'])."',
					pass='$pass',
					salt='$salt',
					validated='".(REGISTER_OPT_IN ? 0 : 1)."',
					languages='".LANG."'");
			$user_id = db()->insert_id;
			create_jabber_id($user_id, $args['nick']);
			user($user_id)->pn_system(LS('Herzlich willkommen auf %1%!

Wir freuen uns dich hier als Mitglied begr&uuml;&szlig;en zu d&uuml;rfen.

Wie &uuml;berall gibt es auch hier ein paar Verhaltensregeln, an die Du dich halten solltest.
[url=http://icom.to/de/thread/48074-Neu-hier--LESEN-/]So gut wie alle Informationen dazu erh&auml;lst Du in [u][b]diesem[/b][/u] Thread.[/url]

Wir w&uuml;nschen Dir einen angenehmen aufenthalt bei uns.

Dein %1%-Team', SITE_NAME, SITE_NAME));
			if(REGISTER_OPT_IN) {
				imail::mail(
					$args['email'],
					LS('Registrierung bei %1%', SITE_NAME),
					LS('Vielen Dank f&uuml;r Deine Registrierung.

Wir w&uuml;nschen Dir viel Spa&szlig; auf %1%!

Klicke auf den folgenden Link um sie abzuschlie&szlig;en:
%2%

Dein iCom.to Team', SITE_NAME, sprintf('http://icom.to/register/validate/user_id/%s/hash/%s/', $user_id, $salt)),
					'From: '.SITE_NAME.' <'.NOREPLY_EMAIL.">\n");
			}
			$this->step = 'success';
		}
		catch(Exception $e) {
			$this->error = $e->getMessage();
		}
		return IS_AJAX ? $this->RUN('MODULE') : true;
	}
	
	protected function MODULE(&$args) {
		$this->im_way_title();
		
		if(isset($args['uid'])) $args['user_id'] = $_GET['uid'];
		if(isset($args['code'])) $args['hash'] = $_GET['code'];
		
		if(REGISTER_CLOSED)
			$step = 'closed';
		elseif(!empty($_COOKIE['gDraFdwfrG4']) and db()->query("SELECT 1 FROM users WHERE salt='".es($_COOKIE['gDraFdwfrG4'])."' AND FIND_IN_SET(".BANNED_GROUPID.", groups) LIMIT 1")->num_rows)
			$this->step = 'disallow';
		elseif(isset($_GET['user_id']) and isset($_GET['hash'])) {
			$this->step = 'validate';
			$this->user = db()->query("SELECT user_id, nick, validated FROM users WHERE user_id='".es($_GET['user_id'])."' AND salt='".es($_GET['hash'])."' LIMIT 1")->fetch_assoc();
			if($this->user and !$this->user['validated']) db()->query("UPDATE users SET validated=1 WHERE user_id='".$this->user['user_id']."' LIMIT 1");
		}
		
		return $this->ilphp_fetch('register.php.ilp');
	}
}

?>
