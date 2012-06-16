<?php

function LS($str) { return lang::_LS($str, func_get_args()); }
function LQ($field) { return lang::LQ($field); }
function LQ2($field) {
	$list = array();
	get_lang_query($list);
	foreach($list as $l)
		$f[] = str_replace('LL', $l, $field);
	return 'COALESCE('.implode(',', $f).')';
}

class lang extends lang_base {
	public static $DEFAULT_DDL_FILTER = array('de' => 'all', 'en' => array('ENG'));
	
	
	public function on_init_start() {
		if(isset($_POST['lang_hide_different_lang_message_box'])) session::$s['lhdlmb'] = 1;
		if(isset($_POST['lang_hide_can_understand_message_box'])) session::$s['lhcumb'] = 1;
		
		define('HIDE_DIFFERENT_LANG_MESSAGE_BOX', isset(session::$s['lhdlmb']) ? true : false);
		define('HIDE_CAN_UNDERSTAND_MESSAGE_BOX', isset(session::$s['lhcumb']) ? true : false);
	}
	public function on_change($lang, $old_lang = NULL, $dual_mode = false) {
		define('LANG_ENABLED_DIFFERENT_LANG', $dual_mode);
		if(($dual_mode or $lang != $old_lang) and (empty(session::$s['lclf']) or session::$s['lclf'] != $lang))
			self::switch_lang_change_filter($lang, $old_lang);
	}
	public function on_ok($lang) {
		define('LANG_ENABLED_DIFFERENT_LANG', false);
	}
	public function on_init_end() {
		include_once CONFIG_DIRNAME.'/lang/'.LANG.'.inc.php';
		lang::$LANGUAGE_PRIORITY_NUM = count(lang::$LANGUAGE_PRIORITY);
		
		if(!IS_LOGGED_IN or !isset($_POST['lang_add_current_lang_to_profile'])) return;
		$r = user()->languages;
		if(!$r or in_array(LANG, $r)) return;
		$r[] = LANG;
		user()->update(array('languages'=>implode_arr_list($r)));
		unset(user::$user_cache[USER_ID]);
		unset(user::$class_cache[USER_ID]);
		cache_L1::del('user_'.USER_ID);
	}
	
	
	private static function switch_lang_change_filter($current, $prev) {
		if($current != $prev) {
			#echo "CHANGING FILTER<br>";
			if(isset(session::$s['filter']) and session::$s['filter']->getValue('languages') == self::$DEFAULT_DDL_FILTER[$prev]) {
				session::$s['filter']['languages'] = self::$DEFAULT_DDL_FILTER[$current];
				session::$s['lclf'] = $current;//lang_changed_lang_filter
				return true;
			}
		}
	}

	public static function LQ($field) {//lang_query_field
		if(self::$LANGUAGE_PRIORITY_NUM == 1)
			return str_replace('LL', self::$LANGUAGE_PRIORITY[0], $field);
		$f = array();
		foreach(self::$LANGUAGE_PRIORITY as $l)
			$f[] = str_replace('LL', $l, $field);
		return 'COALESCE('.implode(',', $f).')';
	}
	public static function LQ2($field) {//lang_query_field (by natural speaking language)
		$list = array();
		get_lang_query($list);
		foreach($list as $l)
			$f[] = str_replace('LL', $l, $field);
		return 'COALESCE('.implode(',', $f).')';
	}
	
	public static function _LS_get_define($hash, $default_str = NULL) {
		if(($str = cache_L1::get('LANG_'.$hash.LANG)) !== false) return $str;
		foreach(self::$LANGUAGE_PRIORITY as $l) {
			$a = db()->query("SELECT data FROM lang_table WHERE lang='".$l."' AND hash='".$hash."' LIMIT 1")->fetch_assoc();
			if(!$a) continue;
			$str = $a['data'];
			cache_L1::set('LANG_'.$hash.LANG, 0, $str);
			db()->query("UPDATE lang_table SET used=used+1 WHERE lang='".$l."' AND hash='".$hash."' LIMIT 1");
			return $str;
		}
		return $default_str !== NULL ? $default_str : false;
	}
	public static function LS($str) {
		return self::_LS($str, func_get_args());
	}
	public static function _LS($str, $args) {
		$hash = substr(hash('md2', $str), 0, 8);
		$temp = self::_LS_get_define($hash);
		if(!$temp) {
			list($define, $hash) = self::import_compiled_string($str, 'none');
			$temp = self::_LS_get_define($hash);
		}
		for($i = 1, $num = count($args); $i < $num; $i++) $temp = str_replace('%'.$i.'%', $args[$i], $temp);
		return $temp;
	}


	public static function _handle_template_string($str) {
		$args = func_get_args();
		for($i = 1, $num = count($args); $i < $num; $i++) $str = str_replace('%'.$i.'%', $args[$i], $str);
		return $str;
	}

	public static function compile_template_string(&$str, $namespace = '') {
		$args = array();
		if(preg_match_all('~<\?=(.*?)\?>~', $str, $out2)) {
			for($j = 0, $num_j = count($out2[1]); $j < $num_j; $j++) {
				$q = $out2[1][$j];
				$q = preg_replace('~htmlspecialchars\((.*?)\)~', '\1', $q);
				$args[] = rtrim($q, ';');
				$q = '%'.($j + 1).'%';
				$str = str_replace($out2[0][$j], $q, $str);
			}
		}
		$m = substr($str, 0, 1);
		if(strpos('!#*', $m) !== false) $str = substr($str, 1);
		list($define, $hash) = self::import_compiled_string($str, $namespace);
		
		//unclean definition. second parameter of lang::_LS_get_define is not needed
		//this is only needed when truncating or deleting entries from sql table lang_table and not rebuild templates_c scripts
		//or when changing hash function for language string ids.
		//note to also restart php-fpm/apache2 to cleanup local variable cache (apc, xcache ...) or memcached when local variable fallback cache is used
		//original is: $define = 'lang::_LS_get_define(\''.$hash.'\')';
		$define = 'lang::_LS_get_define(\''.$hash.'\', \''.str_replace("'", "\\'", $str).'\')';
		
		switch($m) {
		default:
			return $args ? '<?=htmlspecialchars(lang::_handle_template_string('.$define.','.implode(',', $args).'))?>' : '<?=htmlspecialchars('.$define.')?>';
		case '!':
			return $args ? '<?=lang::_handle_template_string('.$define.','.implode(',', $args).')?>' : '<?='.$define.'?>';
		case '#':
			return $args ? '<?=lang::_handle_template_string(htmlspecialchars('.$define.'),'.implode(',', $args).')?>' : '<?=htmlspecialchars('.$define.')?>';
		case '*':
			return $args ? '<?=lang::_handle_template_string('.$define.',htmlspecialchars('.implode('),htmlspecialchars(', $args).'))?>' : '<?='.$define.'?>';
		}
	}

	private static function import_compiled_string(&$str, $namespace = '') {
		$hash = substr(hash('md2', $str), 0, 8);
		$define = 'LANG_TABLE_'.$hash;
		#if(!defined($define)) {
		if(self::_LS_get_define($hash) === false) {
			$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
			#define($define, $str);
			db()->query("INSERT IGNORE INTO lang_table SET namespace='".db()->escape_string($namespace)."', hash='$hash', data='".db()->escape_string($str)."', static='1' ON DUPLICATE KEY UPDATE used=used+1");
			#trigger_error('LANG_TABLE UNDEFINED: '.$namespace.': '.$define.': '.$str, E_USER_NOTICE);
		}
		return array($define, $hash);
	}
}

?>
