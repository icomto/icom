<?php

trait user_friends {
	public static $friend_cache = array();
	
	public function get_friendship_status($friend) {
		if(!IS_LOGGED_IN) return '';
		if(isset(self::$friend_cache[$this->i['user_id'].'-'.$friend->i['user_id']])) return self::$friend_cache[$this->i['user_id'].'-'.$friend->i['user_id']];
		$status = $this->_get_friendship_status($friend);
		self::$friend_cache[$this->i['user_id'].'-'.$friend->i['user_id']] = $status;
		self::$friend_cache[$friend->i['user_id'].'-'.$this->i['user_id']] = $status;
		return $status;
	}
	private function _get_friendship_status(&$friend) {
		if($this->is_equal($friend)) return 'not_possible';
		$status = db()->query("SELECT status FROM user_friends WHERE user_id='".$this->i['user_id']."' AND friend_id='".$friend->i['user_id']."' LIMIT 1");
		if($status->num_rows) return $status->fetch_object()->status;
		return '';
	}
	
	public function is_friend_with($friend) {
		return $this->get_friendship_status($friend) == 'accepted';
	}
	public function is_friend_with_ex($friend) {
		$status = $this->get_friendship_status($friend);
		return $status == 'accepted' or $status == 'not_possible';
	}
	
	
	//friendship functions returns true on error
	public function friendship_request($user_id) {
		$request = db()->query("SELECT id FROM user_friends WHERE user_id='".$this->i['user_id']."' AND friend_id='".$user_id."' LIMIT 1")->fetch_assoc();
		if($request) return true;
		db()->query("INSERT INTO user_friends SET user_id='".$this->i['user_id']."', friend_id='".$user_id."', status='request_sent'");
		db()->query("INSERT INTO user_friends SET user_id='".$user_id."', friend_id='".$this->i['user_id']."', status='request_received'");
		user($user_id)->pn_system(
			utf8_encode('Du hast eine Freundschaftsanfrage von [url=http://'.SITE_DOMAIN.'/users/'.$this->i['user_id'].'-'.urlenc($this->i['nick']).'/]'.$this->i['nick']."[/url] erhalten.\n".
			"\n".
			'Gehe auf das Profil des Benutzers um die Freundschaft anzunehmen oder abzulehnen.'."\n".
			"\n".
			'Du kannst die Freundschaft später jederzeit wieder kündigen, indem Du den Link, den Du im Profil deines Freundes/deiner Freundin findest, aufrufst.'));
		unset(user::$friend_cache[$this->i['user_id'].'-'.$user_id]);
		unset(user::$friend_cache[$user_id.'-'.$this->i['user_id']]);
	}
	
	public function friendship_cancel($user_id) {
		$request = db()->query("SELECT id FROM user_friends WHERE user_id='".$this->i['user_id']."' AND friend_id='".$user_id."' AND status='request_sent' LIMIT 1")->fetch_assoc();
		if(!$request) return true;
		db()->query("DELETE FROM user_friends WHERE user_id='".$this->i['user_id']."' AND friend_id='".$user_id."'");
		db()->query("DELETE FROM user_friends WHERE user_id='".$user_id."' AND friend_id='".$this->i['user_id']."'");
		user($user_id)->pn_system(
			utf8_encode('Die Freundschaftsanfrage von [url=http://'.SITE_DOMAIN.'/users/'.$this->i['user_id'].'-'.urlenc($this->i['nick']).'/]'.$this->i['nick'].'[/url] wurde von ihm/von ihr zuruckgenommen.'));
		unset(user::$friend_cache[$this->i['user_id'].'-'.$user_id]);
		unset(user::$friend_cache[$user_id.'-'.$this->i['user_id']]);
	}
	public function friendship_ignore($user_id) {
		$request = db()->query("SELECT id FROM user_friends WHERE user_id='".$this->i['user_id']."' AND friend_id='".$user_id."' AND status='request_received' LIMIT 1")->fetch_assoc();
		if(!$request) return true;
		db()->query("DELETE FROM user_friends WHERE user_id='".$this->i['user_id']."' AND friend_id='".$user_id."'");
		db()->query("DELETE FROM user_friends WHERE user_id='".$user_id."' AND friend_id='".$this->i['user_id']."'");
		#user($user_id)->pn_system(
		#	utf8_encode('Deine Freundschaftsanfrage an [url=http://'.SITE_DOMAIN.'/users/'.USER_ID.'-'.urlenc($this->i['nick']).'/]'.$this->i['nick'].'[/url] wurde von ihm/von ihr abgelehnt.'));
		unset(user::$friend_cache[$this->i['user_id'].'-'.$user_id]);
		unset(user::$friend_cache[$user_id.'-'.$this->i['user_id']]);
	}
	public function friendship_accept($user_id) {
		$request = db()->query("SELECT id FROM user_friends WHERE user_id='".$this->i['user_id']."' AND friend_id='".$user_id."' AND status='request_received' LIMIT 1")->fetch_assoc();
		if(!$request) return true;
		db()->query("UPDATE user_friends SET status='accepted' WHERE user_id='".$this->i['user_id']."' AND friend_id='".$user_id."' LIMIT 1");
		db()->query("UPDATE user_friends SET status='accepted' WHERE user_id='".$user_id."' AND friend_id='".$this->i['user_id']."' LIMIT 1");
		user($user_id)->pn_system(
			utf8_encode('Deine Freundschaftsanfrage an [url=http://'.SITE_DOMAIN.'/users/'.$this->i['user_id'].'-'.urlenc($this->i['nick']).'/]'.$this->i['nick'].'[/url] wurde von ihm/ihr angenommen.'));
		unset(user::$friend_cache[$this->i['user_id'].'-'.$user_id]);
		unset(user::$friend_cache[$user_id.'-'.$this->i['user_id']]);
	}
	public function friendship_end($user_id) {
		$request = db()->query("SELECT id FROM user_friends WHERE user_id='".$this->i['user_id']."' AND friend_id='".$user_id."' AND status='accepted' LIMIT 1")->fetch_assoc();
		if(!$request) return true;
		db()->query("DELETE FROM user_friends WHERE user_id='".$this->i['user_id']."' AND friend_id='".$user_id."'");
		db()->query("DELETE FROM user_friends WHERE user_id='".$user_id."' AND friend_id='".$this->i['user_id']."'");
		user($user_id)->pn_system(
			utf8_encode('Deine Freundschaft mit [url=http://'.SITE_DOMAIN.'/users/'.$this->i['user_id'].'-'.urlenc($this->i['nick']).'/]'.$this->i['nick'].'[/url] wurde von ihm/von ihr beendet.'));
		unset(user::$friend_cache[$this->i['user_id'].'-'.$user_id]);
		unset(user::$friend_cache[$user_id.'-'.$this->i['user_id']]);
	}
}

?>
