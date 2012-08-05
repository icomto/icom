<?php

error_reporting(E_ALL);

mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'de_DE', 'deu_deu');
date_default_timezone_set('Europe/Berlin');

function __render_error($errno, $errstr, $errfile, $errline) {
	$str = date(DATE_RFC822).' -- '.$errfile.': '.$errline.': '.$errno.': '.$errstr."\n";
	foreach(debug_backtrace() as $k=>$v){
		if($v['function'] == "include" || $v['function'] == "include_once" || $v['function'] == "require_once" || $v['function'] == "require")
			$str .= sprintf("#%2s: %-15s @ %3s: %s(%s)\n",
					$k, $v['file'], $v['line'], $v['function'], @$v['args'][0]);
		else
			$str .= sprintf("#%2s: %-50s %3s: %s()\n",
					$k, @$v['file'], @$v['line'], $v['function']);
	}
	return $str.@$_SERVER['REQUEST_URI']."\n";
}

function __error_handler($errno, $errstr, $errfile, $errline) {
	if(error_reporting() == 0) {
		if(file_exists('/tmp/log_notice')) {
			/*$f = @fopen('/tmp/err.log', 'a');
			if($f) {
				$str = __render_error($errno, $errstr, $errfile, $errline);
				fwrite($f, $str."\n\n------------------------------------------------------------------------------------------------\n");
				fclose($f);
			}*/
		}
		return;
	}
	switch($errno) {
	default:
		#header('HTTP/1.1 500 Internal Server Error');
		$str = __render_error($errno, $errstr, $errfile, $errline);
		$f = @fopen('/tmp/err.log', 'a');
		if($f) {
			fwrite($f, $str."\n\n------------------------------------------------------------------------------------------------\n");
			fclose($f);
		}
		if((isset($_SERVER['TERM']) and $_SERVER['TERM'] == 'xterm') or (defined('USER_ID') and USER_ID === 56340)) echo '<pre>'.$str.'</pre>';
		die(1);
	case 2048:
		$f = @fopen('/tmp/err.log', 'a');
		if($f) {
			$str = __render_error($errno, $errstr, $errfile, $errline);
			fwrite($f, $str."\n\n------------------------------------------------------------------------------------------------\n");
			fclose($f);
		}
		if((isset($_SERVER['TERM']) and $_SERVER['TERM'] == 'xterm') or (defined('USER_ID') and USER_ID === 56340)) echo '<pre>'.$str.'</pre>';
		break;
	}
}


define('CONFIG_DIRNAME', dirname(__FILE__));
function __autoload($class) {
	include CONFIG_DIRNAME.'/class/'.str_replace('__', '/', $class).'.php';
}


function get_militime() { return explode(' ', microtime()); }
function sub_militime($a, $b) { return (float)($b[0] - $a[0]) + (float)($b[1] - $a[1]); }

function db($id = 0) {
	if(!class_exists('db')) throw new Exception('db::not found');
	return db::c($id);
}
function es($s, $id = 0) {
	return db::c($id)->escape_string($s);
}
function NULLval($val) {
	return $val ? "'$val'" : 'NULL';
}

function user($user_id = -1, $init_session = false) {
	return user::c($user_id, $init_session);
}

function session() {
	return session::$s;
}

function imod($module, $fn = NULL, $args = []) {
	$m = iengine::GET($module);
	if($m and $fn) return $m->RUN($fn, $args);
	return $m;
}









function has_privilege($privilege) {
	return IS_LOGGED_IN and user()->has_privilege($privilege);
}

function has_userrights() {
	return IS_LOGGED_IN and (
		has_privilege('usermanager') or
		has_privilege('groupmanager') or
		has_privilege('user_warnings') or
		has_privilege('guestbook_master'));
}
function has_forumrights() {
	return IS_LOGGED_IN and (
		has_privilege('forum_admin') or
		has_privilege('forum_mod') or
		has_privilege('forum_super_mod'));
}







$file = (file_exists('/etc/icom.ini') ? '/etc/icom.ini' : '../../icom.ini');
if(!file_exists($file)) {
	throw new Exception("icom.ini not found at /etc/icom.ini nor at ../../icom.ini")
}

$CONFIG = parse_ini_file($file, true);
$ICOM_CONFIG = $CONFIG;

db::addConnection(0, $CONFIG['mysql']['host'], $CONFIG['mysql']['user'], $CONFIG['mysql']['pass'], $CONFIG['mysql']['db'], $CONFIG['mysql']['port'], $CONFIG['mysql']['sock']);
if(isset($CONFIG['sphinx'])) db::addConnection('sphinx', $CONFIG['sphinx']['host'], '', '', '', $CONFIG['sphinx']['port'], $CONFIG['sphinx']['sock']);

define('SITE_DOMAIN', $CONFIG['main']['domain']);
define('SITE_NAME', $CONFIG['main']['name']);

G::$SITE_TITLE = array($CONFIG['main']['title']);
G::$META_KEYWORDS = $CONFIG['main']['keywords'];
G::$META_DESCRIPTION = $CONFIG['main']['description'];

if($CONFIG['main']['custom_error_handler'])
	set_error_handler('__error_handler');

define('MASTER_PASSWORD', $CONFIG['main']['master_password']);

define('LOAD_JAVASCRIPT_MINIFIED', $CONFIG['main']['load_javascript_minified']);

define('ENABLE_COOKIE_SPAMMER_CHECK', $CONFIG['main']['enable_cookie_spammer_check']);

define('PROXY_HOST', empty($CONFIG['proxy']['host']) ? NULL : $CONFIG['proxy']['host']);
define('PROXY_PORT', empty($CONFIG['proxy']['port']) ? NULL : $CONFIG['proxy']['port']);

# Einstellungen für BB-Code Parsers
define('MAX_FONT_SIZE', 50);
define('MIN_FONT_SIZE', 5);

?>
