<?php

class session extends session_engine {
	public static function start() {
		self::$s = new self;
		self::$s->init();
		return self::$s;
	}
	
	protected function newInstance($data = array(), $is_child = false) {
		return new self($data, $is_child);
	}
	
	protected function init() {
		parent::init();
		
		if(!isset($this->data['release_version'])) $this['release_version'] = RELEASE_VERSION;
		elseif($this->data['release_version'] != RELEASE_VERSION) {
			$this['release_version'] = RELEASE_VERSION;
			page_redir(!empty($_POST['current_location']) ? $_POST['current_location'] : rebuild_location());
		}
	}
	
	protected function init_basics() {
		parent::init_basics();
		
		if(isset($_GET['_cutted']) and $_GET['_cutted']) {
			$temp = explode('/', $_GET['_cutted']);
			for($i = 0, $num = count($temp); $i < $num; $i += 2)
				$_GET[$temp[$i]] = (isset($temp[$i + 1]) ? $temp[$i + 1] : '');
		}
		if(isset($_GET['_cutted'])) unset($_GET['_cutted']);
		
		define('IS_AJAX', isset($_GET['_ajax']) ? true : false);
	}
	
	public function init_defaults() {
		if(!isset($this->data['menu_radio_tab'])) $this['menu_radio_tab'] = RADIO_DEFAULT_CHANNEL;
		
		if(!isset($this->data['layout'])) $this['layout'] = DEFAULT_LAYOUT;
		if(!isset($this->data['theme_ini'])) $this['theme_ini'] = DEFAULT_THEME_INI;
		if(!isset($this->data['theme_preset'])) $this['theme_preset'] = DEFAULT_THEME_PRESET;
	}
	
	public function get_cookie_user() {
		if(!empty($_COOKIE['stayon_u']) and !empty($_COOKIE['stayon_s']))
			return array($_COOKIE['stayon_u'], $_COOKIE['stayon_s']);
	}
	public function set_cookie_user($user_id, $hash) {
		setcookie('stayon_u', $user_id, time() + 60*60*24*365*5, '/', '.'.BASE_DOMAIN, false, true);
		setcookie('stayon_s', $hash, time() + 60*60*24*365*5, '/', '.'.BASE_DOMAIN, false, true);
		setcookie('stayon_u', $user_id, time() + 60*60*24*365*5, '/', '', false, true);
		setcookie('stayon_s', $hash, time() + 60*60*24*365*5, '/', '', false, true);
	}
	public function del_cookie_user() {
		setcookie('stayon_u', 'deleted', time() - 3600, '/', '.'.BASE_DOMAIN, false, true);
		setcookie('stayon_s', 'deleted', time() - 3600, '/', '.'.BASE_DOMAIN, false, true);
		setcookie('stayon_u', 'deleted', time() - 3600, '/', '', false, true);
		setcookie('stayon_s', 'deleted', time() - 3600, '/', '', false, true);
	}
	
	public function get_cookie_guest() {
		return empty($_COOKIE['suilid']) ? '' : $_COOKIE['suilid'];
	}
	public function set_cookie_guest($hash) {
		setcookie('suilid', $hash, time() + 60*60*24*365*5, '/', '.'.BASE_DOMAIN, false, true);
		setcookie('suilid', $hash, time() + 60*60*24*365*5, '/', '', false, true);
	}
	public function del_cookie_guest() {
		setcookie('suilid', 'deleted', time() - 3600, '/', '.'.BASE_DOMAIN, false, true);
		setcookie('suilid', 'deleted', time() - 3600, '/', '', false, true);
	}
	
	protected function cookie_spammer_check() {
		if(isset($_POST['captcha']) and isset($_POST['captcha_text'])) {
			$text = @captcha_decrypt($_POST['captcha_text']);
			$status = cache_L2::get('cookie_cookie_spammer_img_'.$text);
			cache_L2::del('cookie_cookie_spammer_img_'.$text);
			if(strtolower($_POST['captcha']) == strtolower($text) and $status !== false) {
				db()->query("UPDATE guest_sessions SET validated=1 WHERE ip=0x".bin2hex(inet6_pton($_SERVER['REMOTE_ADDR'])));
				return true;
			}
		}
	}
	protected function cookie_spammer_drop() {
		if(IS_AJAX) {
			G::$json_data['s'][] = "document.location='".htmlspecialchars(rebuild_location())."';";
			die(json_encode(G::$json_data));
		}
		db()->query("
			INSERT INTO guest_sessions_blocked
			SET
			ip=0x".bin2hex(inet6_pton($_SERVER['REMOTE_ADDR'])).",
			referer='".es(@$_SERVER['HTTP_REFERER'])."',
			user_agent='".es(@$_SERVER['HTTP_USER_AGENT'])."'");
		define('LANG', 'de');
		$tpl = new ilphp('cookie_spammer.ilp', -1, NULL, '../../templates_c');
		$tpl->captcha_error = isset($_POST['captcha']);
		$tpl->ilphp_display();
		die;
	}
	
	protected function save_user() {
		$current_ip = inet6_pton(@$_SERVER['REMOTE_ADDR']);
		if($this->changed or $this->LAST_IP != $current_ip) {
			$update = array();
			if($this->LAST_IP != $current_ip) $update[] = "ip=0x".bin2hex($current_ip);
			if($this->changed) $update[] = "data=0x".bin2hex(gzcompress(parent::serialize()));
			db()->query("UPDATE user_sessions SET ".implode(', ', $update)." WHERE user_id='".USER_ID."' LIMIT 1");
			cache_L1::del('user_session_'.USER_ID);//DEL_GLOBAL
		}
		db()->query("
			UPDATE users
			SET
				time_on_page=time_on_page+IF(TIMEDIFF(CURRENT_TIMESTAMP,lastvisit)<'00:02:05',TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP,lastvisit)),0),
				lastvisit=CURRENT_TIMESTAMP()".(G::$USER_INTERACTED ? ",
				lastaction=CURRENT_TIMESTAMP" : "")."
			WHERE user_id='".USER_ID."'
			LIMIT 1");
		#cache_L1::del('user_'.USER_ID);//DEL_GLOBAL
	}
	protected function save_guest() {
		$update = array();
		$current_ip = inet6_pton(@$_SERVER['REMOTE_ADDR']);
		if($this->LAST_IP != $current_ip) $update[] = "ip=0x".bin2hex($current_ip);
		if($this->changed) $update[] = "data=0x".bin2hex(gzcompress(parent::serialize()));
		if(true or $update or $this->NUM_USED < 50 or $this->LAST_TIME + 20*60 < time()) {
			$update[] = "lasttime=CURRENT_TIMESTAMP";
			$update[] = "num_used=num_used+1";
		}
		if($update) {
			db()->query("UPDATE guest_sessions SET ".implode(', ', $update)." WHERE id=UNHEX('".USER_ID."') LIMIT 1");
			cache_L1::del('guest_session_'.USER_ID);//DEL_GLOBAL
		}
		db()->query("INSERT LOW_PRIORITY INTO guests SET id=UNHEX('".USER_ID."') ON DUPLICATE KEY UPDATE lasttime=CURRENT_TIMESTAMP");
	}
}

?>
