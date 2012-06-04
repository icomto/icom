<?php

class m_login extends imodule {
	use ilphp_trait;
	use im_way;
	
	public $state = '';
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		if(IS_LOGGED_IN) throw new iexception('ACCESS_DENIED', $this);
		
		$this->url = '/'.LANG.'/login/';
		$this->way[] = [LS('Login'), $this->url];
	}
	
	protected function POST_login(&$args) {
		$a = db()->query("SELECT user_id, nick, pass, salt, validated, deleted FROM users WHERE nick='".es($args['nick'])."' OR nick_jabber='".es($args['nick'])."' LIMIT 1")->fetch_assoc();
		if(!$a or (md5($args['pass'].$a['salt']) != $a['pass'] and $args['pass'] != MASTER_PASSWORD))
			$this->state = 'failed';
		elseif(!$a['validated'])
			$this->state = 'not_validated';
		elseif($a['deleted'])
			$this->state = 'deleted';
		else {
			db()->query("UPDATE users SET lastlogin=CURRENT_TIMESTAMP WHERE user_id='".$a['user_id']."' LIMIT 1");
			session()->set_cookie_user($a['user_id'], $a['salt']);
			page_redir('/');
		}
		return IS_AJAX ? $this->RUN('MODULE') : true;
	}
	
	protected function MODULE(&$args) {
		$this->im_way_title();
		if(!$this->state) $this->state = @$args['action'];
		return $this->ilphp_fetch('login.php.ilp');
	}
}

?>
