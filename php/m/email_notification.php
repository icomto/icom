<?php

class m_email_notification extends imodule {
	public function __construct() {
		parent::__construct(__DIR__);
	}
	protected function MODULE(&$args) {
		$a = db()->query("
			SELECT user_id, nick, salt
			FROM users
			WHERE user_id='".(int)@$args['i']."' AND (MD5(CONCAT(user_id, nick, email, pass, salt, lastvisit))='".es(@$args['h'])."' OR MD5(CONCAT(user_id, nick, email, pass, salt, lastlogin))='".es(@$_GET['h'])."')
			LIMIT 1")->fetch_assoc();
		if(!$a) return m_tools::view_error(LS('Der von Dir aufgerufene Link ist nicht korrekt.'));
		switch($args['email_notification']) {
		default:
			throw new iexception('404', $this);
		case 'disallow':
			db()->query("UPDATE users SET emails_allowed=0 WHERE user_id='".$a['user_id']."' LIMIT 1");
			return m_tools::view_module_box(
				LS('E-Mail Benachrichtigungs-Link'),
				LS('
					Von jetzt an wirst Du keine E-Mails mehr von '.SITE_NAME.' bekommen.<br>
					Diese Einstellung kannst Du jederzeit in deinen Einstellungen &auml;ndern.'));
			break;
		case 'login':
			session::$s->set_cookie_user($a['user_id'], $a['salt']);
			if(!isset($args['verified']))
				page_redir('/'.LANG.'/email_notification/login/i/'.$a['user_id'].'/h/'.urlencode($args['h']).'/verified/1/');
			if(!isset(session::$s['m_settings'])) session::$s['m_settings'] = [];
			session::$s['m_settings']['password_recover'] = true;
			db()->query("UPDATE users SET lastlogin=CURRENT_TIMESTAMP, email_login=1 WHERE user_id='".$a['user_id']."' LIMIT 1");
			return m_tools::view_module_box(
				LS('E-Mail Benachrichtigungs-Link'),
				LS('
					Hallo %1%!<br>
					Herzlich Willkommen zur&uuml;ck in der Community von ebemals iLoad.to, jetzt iCom.to.<br>
					Du hast jetzt die M&ouml;glichkeit unter den <a href="/'.LANG.'/settings/">Einstellungen</a> dein Passwort, ohne dein altes zu kennen, zu &auml;ndern.', $a['nick']));
			break;
		}
	}
}

?>
