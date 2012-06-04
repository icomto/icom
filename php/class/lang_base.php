<?php

abstract class lang_base {
	public static $LANG_AVAILABLE_LANGUAGES = array('de', 'en');
	public static $LANGUAGE_PRIORITY = NULL;
	public static $LANGUAGE_PRIORITY_NUM = 0;
	
	
	public abstract function on_init_start();
	public abstract function on_init_end();
	
	//called when nothing changed
	public abstract function on_ok($lang);
	
	//called on language change; dual_mode = true when cookie and domain languages are different
	public abstract function on_change($lang, $old_lang = NULL, $dual_mode = false);
	
	
	public function __construct() {
		$this->on_init_start();
		
		$change_lang = (empty($_GET['_lang']) ? '' : $_GET['_lang']);
		$cookie_lang = (empty($_COOKIE['lang']) ? '' : $_COOKIE['lang']);
		
		//language change request ?!
		if($change_lang and in_array($_GET['_lang'], self::$LANG_AVAILABLE_LANGUAGES)) {
			echo "CHANGE!!!!!! $change_lang / $cookie_lang";
			$this->on_change($change_lang, $cookie_lang);
			$this->set_cookie($change_lang);
			$this->redirect($change_lang, 'change');
		}
		
		if(empty($_GET['_lllang'])) $domain_lang = '';
		else {
			$domain_lang = $_GET['_lllang'];
			unset($_GET['_lllang']);
		}
		
		//language from cookie...
		if($cookie_lang and in_array($cookie_lang, self::$LANG_AVAILABLE_LANGUAGES)) {
			if($domain_lang and $domain_lang != $cookie_lang and in_array($domain_lang, self::$LANG_AVAILABLE_LANGUAGES)) {
				$this->on_change($cookie_lang, $domain_lang, true);
				return $this->finalize($cookie_lang, $domain_lang);
			}
			$this->on_ok($cookie_lang);
			return $this->finalize($cookie_lang, $domain_lang);
		}
		
		//langauage from domain
		if($domain_lang and in_array($domain_lang, self::$LANG_AVAILABLE_LANGUAGES)) {
			$this->on_change($domain_lang);
			$this->set_cookie($domain_lang);
			return $this->finalize($domain_lang, $domain_lang);
		}
		
		//langauge from http header
		$header_lang = $this->get_by_http_header();
		if($header_lang) {
			$this->on_change($header_lang);
			$this->set_cookie($header_lang);
			return $this->finalize($header_lang, $domain_lang);
		}
		
		#$lang = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
		
		$this->redirect(self::$LANG_AVAILABLE_LANGUAGES[0], 'default');
	}
	
	
	private function set_cookie($lang = NULL) {
		if(preg_match('~^.*?\.([^\.]+\.[^\.]+)~', BASE_DOMAIN, $out)) $domain = '.'.$out[1];
		else $domain = '.'.BASE_DOMAIN;
		
		if($lang) {
			setcookie('lang', $lang, time()+60*60*24*365*5, '/', $domain, false, true);
			$_COOKIE['lang'] = $lang;
		}
		else {
			setcookie('lang', 'deleted', time()-3600, '/', $domain, false, true);
			$_COOKIE['lang'] = NULL;
		}
	}
	
	private function redirect($lang, $reason = '') {
		if(IS_AJAX) {
			G::$json_data['s1'][] = "location='http://".BASE_DOMAIN."/".$lang.htmlspecialchars(rebuild_location())."';";
			echo json_encode(G::$json_data);
		}
		else {
			#header("HTTP/1.1 301 Moved Permanently");
			header('Location: http://'.BASE_DOMAIN.'/'.$lang.rebuild_location());
		}
		if(function_exists('_save_session')) _save_session();
		die;
	}
	
	private function finalize($lang, $domain_lang) {
		//get lanuage from our domain and when invalid repair our domain
		if(!in_array($domain_lang, self::$LANG_AVAILABLE_LANGUAGES))
			$this->redirect($lang, true, 'repair');
		
		define('LANG_COOKIE_LANG', $lang);
		define('LANG', $domain_lang);
		
		$this->on_init_end();
	}
	
	private function get_by_http_header() {
		if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) return;
		$langs = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach($langs as $ll) {
			$ll = array_map('trim', explode(',', $ll));
			foreach($ll as $l)
				foreach(self::$LANG_AVAILABLE_LANGUAGES as $al)
					if(preg_match('~^'.$al.'(-[a-z]+)?$~i', $l))
						return $al;
		}
	}
}

?>
