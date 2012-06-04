<?php

abstract class session_engine extends MonitoredArray {
	public static $s;
	
	public $changed = false;
	
	protected $LAST_IP;		//users and guests
	protected $LAST_TIME;	//guests
	protected $NUM_USED;	//guests
	
	
	public static function start() {
	}
	
	public abstract function get_cookie_user(); //returns array(id, hash) or null
	public abstract function set_cookie_user($user_id, $hash);
	public abstract function del_cookie_user();
	
	public abstract function get_cookie_guest(); //returns hash or null
	public abstract function set_cookie_guest($hash);
	public abstract function del_cookie_guest();
	
	protected abstract function cookie_spammer_check();	//check if spammer validated himself; returns true on success
	protected abstract function cookie_spammer_drop();	//guest is a spammer. drop some validation code for him. return true to disable cookies
	
	protected abstract function save_user();
	protected abstract function save_guest();
	
	
	protected function onArrayChanged($k) {
		if(self::$s) self::$s->changed = true;
	}
	protected function onArrayUnchanged() {
		if(self::$s) self::$s->changed = false;
	}
	
	public static function s() {
		return self::$s;
	}
	
	protected function init() {
		$this->init_basics();
		$this->init_cookies();
		
		if(IS_LOGGED_IN) $this->init_user();
		elseif(USING_COOKIES) $this->init_guest();
	}
	
	protected function init_basics() {
		if(isset($_SERVER['HTTP_X_REAL_IP'])) $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
		#if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		
		#define('BASE_DOMAIN', SITE_DOMAIN);
		if(preg_match('~^.*?([^\.]+\.[^\.]+)$~', @$_SERVER['HTTP_HOST'], $out)) define('BASE_DOMAIN', $out[1]);
		elseif(@$_SERVER['HTTP_HOST']) define('BASE_DOMAIN', $_SERVER['HTTP_HOST']);
		else define('BASE_DOMAIN', SITE_DOMAIN);
		
		function _init_http_vars($str) {
			if(is_array($str)) return array_map('_init_http_vars', $str);
			if(get_magic_quotes_gpc()) $str = stripslashes($str);
			return trim($str);
		}
		$_GET = array_map('_init_http_vars', $_GET);
		$_POST = array_map('_init_http_vars', $_POST);
		$_COOKIE = array_map('_init_http_vars', $_COOKIE);
	}
	
	protected function init_cookies() {
		$cookie = $this->get_cookie_user();
		if($cookie) {
			$user = user(db()->escape_string($cookie[0]), true);
			if(!$user->i or $user->i['salt'] != $cookie[1]) $this->del_cookie_user();
			else {
				define('USING_COOKIES', true);
				define('IS_LOGGED_IN', true);
				define('USER_ID', $user->i['user_id']);
				return;
			}
		}
		
		define('IS_LOGGED_IN', false);
		
		if($this->is_crawler()) {
			define('USING_COOKIES', false);
			define('USER_IS_CRAWLER', true);
			define('USER_ID', 'NOT_USING_COOKIES');
			return;
		}
		if($this->is_feed_fetcher()) {
			define('USING_COOKIES', false);
			define('USER_IS_CRAWLER', false);
			define('USER_ID', 'NOT_USING_COOKIES');
			return;
		}
		
		define('USER_IS_CRAWLER', false);
		
		$cookie = $this->get_cookie_guest();
		if($cookie) {
			define('USING_COOKIES', true);
			define('USER_ID', db()->escape_string($cookie));
			return;
		}
		if($this->is_cookieless_session()) {
			define('USING_COOKIES', false);
			define('USER_ID', 'NOT_USING_COOKIES');
			return;
		}
		
		define('USING_COOKIES', true);
		$guest_uid = $_SERVER['REMOTE_ADDR'].'respect.the.privacy.of.everyone!';
		do $guest_uid = md5($guest_uid.mt_rand());
		while(db()->query("SELECT SQL_CACHE 1 FROM guest_sessions WHERE id=UNHEX('$guest_uid') LIMIT 1")->num_rows);
		$this->set_cookie_guest($guest_uid);
		define('USER_ID', $guest_uid);
	}
	protected function is_cookieless_session() {
		if($this->cookie_spammer_check()) return true;
		//drop table guest_sessions;
		//alter table guest_sessions_bin rename guest_sessions;
		//alter table guest_sessions change ip ip binary(16) not null;
		//alter table user_sessions change ip ip binary(16) not null;
		$num_sessions = db()->query("SELECT SQL_CACHE COUNT(*) AS num FROM guest_sessions WHERE ip=0x".bin2hex(inet6_pton($_SERVER['REMOTE_ADDR']))." AND num_used<=2 AND validated=0")->fetch_object()->num;
		if($num_sessions  < 30) return false;
		$num_sessions_validated = db()->query("SELECT SQL_CACHE COUNT(*) AS num FROM guest_sessions WHERE ip=0x".bin2hex(inet6_pton($_SERVER['REMOTE_ADDR']))." AND validated=1")->fetch_object()->num;
		if($num_sessions_validated > 10) return true;
		return $this->cookie_spammer_drop();
	}
	protected function is_crawler() {
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		//SELECT INET6_NTOP(ip), COUNT(*) num, MAX(t) t, user_agent FROM guest_sessions_blocked WHERE user_agent NOT REGEXP 'Intel Mac OS X 10_6_4|MSIE [0-9].0; Windows NT|Ubuntu/9.04' GROUP BY ip, referer, user_agent ORDER BY num;
		return
			isset($_GET['agt85go5ieugjoaijg']) or
			preg_match('~^66\.249\.66\.~', $_SERVER['REMOTE_ADDR']) or //googlebot
			preg_match('~^93\.159\.111\.~', $_SERVER['REMOTE_ADDR']) or //Suggy
			preg_match('~^207\.46\.~', $_SERVER['REMOTE_ADDR']) or //MS
			preg_match('~^65\.52\.~', $_SERVER['REMOTE_ADDR']) or //MS
			preg_match('~^(RockMeltEmbedService)$~', $user_agent) or
			preg_match('~^Mozilla/5\.0 \(compatible; (Baiduspider|bingbot|Googlebot|MJ12bot|YandexBot)/~', $user_agent) or
			preg_match('~^Mozilla/5\.0 \(Windows; U; Windows NT 6\.0; en-GB; rv:1\.0; trendictionbot0.4.5; trendiction search~', $user_agent) or
			preg_match('~^Mozilla/5\.0 \([^\)]+\) AppleWebKit/\d+\.\d+ \(KHTML, like Gecko; Google Web Preview~', $user_agent) or
			preg_match('~^(Googlebot-Image|magpie-crawler|rogerbot|TurnitinBot|facebookexternalhit|msnbot|Sogou web spider|vlc|vik-robot)/~', $user_agent) or
			preg_match('~^(yacybot|ia_archiver) \(~', $user_agent);
	}
	protected function is_feed_fetcher() {
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		return
			strpos($user_agent, 'Feedfetcher-Google; (+http://www.google.com/feedfetcher.html;') !== false or
			strpos($user_agent, 'Windows-RSS-Platform/2.0') !== false or
			strpos($user_agent, 'Feedoo7 Crawler') !== false or
			$user_agent == 'Feedreader 3.14 (Powered by Newsbrain)';
	}
	
	
	protected function init_user() {
		/*if(($data = cache_L1::get('user_session_'.USER_ID)) !== false) {
			$this->LAST_IP = $data['ip'];
			parent::set($data['data']);
			return;
		}*/
		$i = 0;
		$data = false;
		while($data === false and $i++ < 3) {
			$data = db()->query("SELECT ip, data FROM user_sessions WHERE user_id='".USER_ID."' LIMIT 1");
			if($data->num_rows == 0) {
				db()->query("INSERT INTO user_sessions SET user_id='".USER_ID."', ip=0x".bin2hex(inet6_pton(@$_SERVER['REMOTE_ADDR'])).", data=0x".bin2hex(gzcompress(serialize(array()))));
				$data = false;
				break;
			}
			$data = $data->fetch_assoc();
			$this->LAST_IP = $data['ip'];
			if($data['data']) $data = unserialize(gzuncompress($data['data']));
			if($data === false) sleep(1);
		}
		if($data === false) {
			$this->LAST_IP = 0;
			$data = array();
		}
		#cache_L1::set('user_session_'.USER_ID, 60, array('ip'=>$this->LAST_IP, 'data'=>$data));
		parent::set($data);
	}
	protected function init_guest() {
		/*if(($data = cache_L1::get('guest_session_'.USER_ID)) !== false) {
			$this->LAST_IP = $data['ip'];
			$this->LAST_TIME = $data['lasttime'];
			$this->NUM_USED = $data['num_used'];
			parent::set($data['data']);
			return;
		}*/
		$i = 0;
		$data = false;
		while($data === false and $i++ < 3) {
			$data = db()->query("SELECT SQL_CACHE ip, lasttime, num_used, data FROM guest_sessions WHERE id=UNHEX('".USER_ID."') LIMIT 1");
			if($data->num_rows == 0) {
				db()->query("INSERT IGNORE INTO guest_sessions SET id=UNHEX('".USER_ID."'), ip=0x".bin2hex(inet6_pton(@$_SERVER['REMOTE_ADDR'])).", data=0x".bin2hex(gzcompress(serialize(array()))));
				$data = false;
				break;
			}
			$data = $data->fetch_assoc();
			$this->LAST_IP = $data['ip'];
			$this->LAST_TIME = strtotime($data['lasttime']);
			$this->NUM_USED = $data['num_used'];
			if($data['data']) $data = unserialize(gzuncompress($data['data']));
			if($data === false) sleep(1);
		}
		if($data === false) {
			$this->LAST_IP = 0;
			$this->LAST_TIME = 0;
			$this->NUM_USED = 0;
			$data = array();
		}
		#cache_L1::set('guest_session_'.USER_ID, 60, array('ip'=>$this->LAST_IP, 'lasttime'=>$this->LAST_TIME, 'num_used'=>$this->NUM_USED, 'data'=>$data));
		parent::set($data);
	}
	
	
	public function save() {
		if(IS_LOGGED_IN) $this->save_user();
		elseif(USING_COOKIES) $this->save_guest();
	}
}

?>
