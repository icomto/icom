<?php

class m_users_friends extends im_tabs {
	use ilphp_trait;
	
	protected $im_tabs_var = 'upft';
	protected $im_tabs_template = 'sub';
	
	public $m_user = NULL;
	public $user = NULL;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		$this->m_user = $args['parent'];
		unset($this->args['parent']);
		
		$this->user =& $this->m_user->user;
		
		$this->url = $this->m_user->url.'upft/';
		$this->way[] = [LS('Freunde'), $this->url];
		
		$this->im_tabs_add('friends', LS('Freunde'), user()->has_priv($this->user['priv_friends'], user($this->user['user_id'])) ? TAB_SELF : false);
		$this->im_tabs_add('requests', LS('Anfragen'), $this->user['user_id'] == USER_ID ? TAB_SELF : false);
		
		parent::INIT($args);
	}
	
	protected function TAB_friends(&$args) {
		if(has_userrights() or $this->user['user_id'] == USER_ID) $this->ilphp_init('friends.php.friends.ilp');
		else  {
			$this->ilphp_init('friends.php.friends.ilp', 10, $this->user['user_id'].'-'.USER_ID);
			if(($data = $this->ilphp_cache_load()) !== false) return $data;;
		}
		$this->friends = db()->query("
			SELECT b.user_id, b.nick AS nick, b.avatar_img AS avatar, a.status AS status
			FROM user_friends a
			JOIN users b ON b.user_id=a.friend_id
			WHERE a.user_id='".$this->user['user_id']."' AND a.status='accepted'
			ORDER BY b.nick");
		return $this->ilphp_fetch();
	}
	
	protected function TAB_requests(&$args) {
		$this->requests_post($args);
		$this->ilphp_init('friends.php.requests.ilp');
		$this->requests = db()->query("
			SELECT b.user_id, b.nick AS nick, b.avatar_img AS avatar, a.status AS status
			FROM user_friends a
			JOIN users b ON b.user_id=a.friend_id
			WHERE a.user_id='".$this->user['user_id']."' AND a.status='request_received'
			ORDER BY b.nick");
		return $this->ilphp_fetch();
	}
	
	private function requests_post(&$args) {
		if(!@$args['friend_id'] or !in_array(@$args['status'], array('accept', 'reject'))) return;
		$friend_id = (int)$args['friend_id'];
		$request = db()->query("SELECT id FROM user_friends WHERE user_id='".USER_ID."' AND friend_id='".$friend_id."' AND status='request_received' LIMIT 1")->fetch_assoc();
		if(!$request) return LS('Freundschaftsanfrage nicht gefunden.');
		switch($args['status']) {
		case 'accept':
			db()->query("UPDATE user_friends SET status='accepted' WHERE user_id='".USER_ID."' AND friend_id='".$friend_id."' LIMIT 1");
			db()->query("UPDATE user_friends SET status='accepted' WHERE user_id='".$friend_id."' AND friend_id='".USER_ID."' LIMIT 1");
			user($friend_id)->pn_system(
				utf8_encode('Deine Freundschaftsanfrage an [url=http://'.SITE_DOMAIN.'/users/'.USER_ID.'-'.urlenc(user()->nick).'/]'.user()->nick.'[/url] wurde angenommen.'));
			return '<p class="success">'.LS('Freundschaftsanfrage angenommen.').'</p>';
		case 'reject':
			db()->query("DELETE FROM user_friends WHERE user_id='".USER_ID."' AND friend_id='".$friend_id."'");
			db()->query("DELETE FROM user_friends WHERE user_id='".$friend_id."' AND friend_id='".USER_ID."'");
			return '<p class="error">'.LS('Freundschaftsanfrage wird ignoriert.').'</p>';
		}
	}
}

?>
