<?php

function create_jabber_id($user_id, $nick) {
	$onick = preg_replace('~[^a-z0-9_]~i', '_', $nick);
	$onick = preg_replace('~__+~i', '_', $onick);
	$nick = $onick;
	$i = 1;
	while(db()->query("SELECT 1 FROM users WHERE user_id!='$user_id' AND nick_jabber='".es($nick)."' LIMIT 1")->fetch_assoc())
		$nick = $onick.($i++);
	db()->query("UPDATE users SET nick_jabber='".es($nick)."' WHERE user_id='$user_id' LIMIT 1");
	return $nick;
}



function page_redir($location) {
	$location = '/_redir/'.(mt_rand()^time()).'/'.ltrim($location, '/');
	if(IS_AJAX) {
		G::$json_data['keep_loading'] = true;
		G::$json_data['s1'][] = "location='$location';";
		echo json_encode(G::$json_data);
		_save_session();
		die;
	}
	header('Location: '.$location);
	_save_session();
	die;
}



function set_site_title($title) {
	G::$SITE_TITLE = $title;
}
function set_site_title_add($title) {
	G::$SITE_TITLE[] = $title;
}
function set_site_title_i($title, $i) {
	G::$SITE_TITLE = array();
	foreach($title as $t) G::$SITE_TITLE[] = $t[$i];
}
function set_site_title_add_i($title, $i) {
	foreach($title as $t) G::$SITE_TITLE[] = $t[$i];
}


$FAST_AJAX_UPDATE = false;
function set_fast_ajax_update($weight) {
	global $FAST_AJAX_UPDATE;
	$FAST_AJAX_UPDATE = $weight;
}
function do_fast_ajax_update() {
	global $FAST_AJAX_UPDATE;
	return $FAST_AJAX_UPDATE;
}






function strsize($size, $numdot = 2) {
	if($size > 1024*1024*1024*1024) return str_replace('.', ',', sprintf('%0.'.$numdot.'f TB', $size/(1024*1024*1024*1024)));
	elseif($size > 1024*1024*1024) return str_replace('.', ',', sprintf('%0.'.$numdot.'f GB', $size/(1024*1024*1024)));
	elseif($size > 1024*1024) return str_replace('.', ',', sprintf('%0.'.$numdot.'f MB', $size/(1024*1024)));
	elseif($size > 1024) return str_replace('.', ',', sprintf('%0.'.$numdot.'f kB', $size/1024));
	else return ($size ? $size : 0).' bytes';
}



function random_string($length = 4, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
	$rv = "";
	srand((double)microtime()*1000000);
	for($i = 0; $i < $length; $i++) $rv .= substr($chars, rand()%(strlen($chars)-1), 1);
	return $rv;
}

function default_time_format($time) {
	return date('j. F Y H:i', is_numeric($time) ? $time : strtotime($time));
}
function default_date_format($date) {
	return date('j. F Y', is_numeric($date) ? $date : strtotime($date));
}

function timeago($timestamp, $ago = true, $now = NULL) {
	if(!is_numeric($timestamp)) $timestamp = strtotime($timestamp);
    $difference = time() - $timestamp;
	if($difference > 60*60*24*7*4.35*12*30) return '<span title="'.date('j. F Y H:i', $timestamp).'">'.sprintf(G::$LANG_TIME['MORE_THAN_X_YEARS'], 30).'</span>';
	elseif($difference > 60*60*24*7*4.35*12*20) return '<span title="'.date('j. F Y H:i', $timestamp).'">'.sprintf(G::$LANG_TIME['MORE_THAN_X_YEARS'], 20).'</span>';
	elseif($difference > 60*60*24*7*4.35*12*10) return '<span title="'.date('j. F Y H:i', $timestamp).'">'.sprintf(G::$LANG_TIME['MORE_THAN_X_YEARS'], 10).'</span>';
    if($difference <= 0) {
        $remaining = true;
        $difference = $timestamp - time();
    }
	else $remaining = false;
    $lengths = array(60, 60, 24, 7, 4.35, 12);
    for($j = 0; count($lengths) > $j and $difference >= $lengths[$j]; $j++) $difference /= $lengths[$j];
	$mode = 'MODE_'.($ago ? 1 : 2);
    if($difference == 0) $retval = G::$LANG_TIME[$mode][0];
	else {
		$difference = round($difference);
		if($difference == 1) $retval = G::$LANG_TIME['ONE'][$j].' '.G::$LANG_TIME['SINGULAR'][$j];
		else $retval = $difference.' '.G::$LANG_TIME['PLURAL'][$j];
		$retval = sprintf(G::$LANG_TIME[$mode][$remaining ? 2 : 1], $retval);
	}
	return '<span title="'.default_time_format($timestamp).'" data-timestamp="'.(time() - $timestamp).'">'.$retval.'</span>';
}
function timeelapsed($secs, $past = 'vor', $future = 'in') {
	if($secs < 0) {
		$secs *= -1;
		$t = $past;
	}
	else $t = $future;
	if($secs == 0) return G::$LANG_JUST_NOW[0];
	elseif($secs <= 90) return $t.' '.(($x = $secs) == 1 ? G::$LANG_TIME['ONE'][0] : $x).' '.($secs == 1 ? G::$LANG_TIME['SINGULAR'][0] : G::$LANG_TIME['PLURAL'][0]);
	elseif($secs <= 90*60) return $t.' '.(($x = round($secs/60)) == 1 ? G::$LANG_TIME['ONE'][1] : $x).' '.($secs == 1 ? G::$LANG_TIME['SINGULAR'][1] : G::$LANG_TIME['PLURAL'][1]);
	elseif($secs <= 36*60*60) return $t.' '.(($x = round($secs/60/60)) == 1 ? G::$LANG_TIME['ONE'][2] : $x).' '.($secs == 1 ? G::$LANG_TIME['SINGULAR'][2] : G::$LANG_TIME['PLURAL'][2]);
	elseif($secs <= 45*24*60*60) return $t.' '.(($x = round($secs/60/60/24)) == 1 ? G::$LANG_TIME['ONE'][3] : $x).' '.($secs == 1 ? G::$LANG_TIME['SINGULAR'][3] : G::$LANG_TIME['PLURAL'][3]);
	elseif($secs <= 18*30*24*60*60) return $t.' '.(($x = round($secs/60/60/24/30)) == 1 ? G::$LANG_TIME['ONE'][4] : $x).' '.($secs == 1 ? G::$LANG_TIME['SINGULAR'][3] : G::$LANG_TIME['PLURAL'][4]);
	else return $t.' '.(($x = round($secs/60/60/24/30/12)) == 1 ? G::$LANG_TIME['ONE'][5] : $x).' '.($secs == 1 ? G::$LANG_TIME['SINGULAR'][5] : G::$LANG_TIME['PLURAL'][5]);
}

function create_pages_sub($current, $first, $last, &$furl, &$delim, &$num_format, &$link_format, &$selected_format) {
	$page_text = '';
	$first = floor($first);
	$last = floor($last);
	for($i = $first; $i <= $last; $i++) {
		if($i == $current) $page_text .= sprintf($selected_format, sprintf($num_format, $i));
		else $page_text .= sprintf($link_format, sprintf($furl, $i), sprintf($num_format, $i));
	}
	return $page_text;
}
#echo "<html><head><script type='text/javascript' src='/lib/jquery/jquery-1.3.2.min.js'></script></head><body>ajflkajf<br>dsajioa<br>";
#echo create_pages(10, 5000, "test/%s");
#echo "<br>jfglajf<br>fjglojiorjgierjflsjflsjafl<br>";
function create_pages($current, $num_pages, $furl, $backnext = true, $delim = ' &nbsp;', $num_format = '%02s', $link_format = '<a href="%s">%s</a>', $selected_format = '<span class="selected">%s</span>') {
	$furl = preg_replace('~(%[^s])~', '%\1', $furl);
	$num_pages++;
	if($num_pages > 9) {
		$rv = "";
		if($current < 5) $rv .= create_pages_sub($current, 1, $current < 2 ? 3 : $current+1, $furl, $delim, $num_format, $link_format, $selected_format);
		else $rv .= create_pages_sub($current, 1, 3, $furl, $delim, $num_format, $link_format, $selected_format);
		if($current >= 5 and $current < $num_pages-4) $rv .= '<span class="etc">...</span>'.create_pages_sub($current, $current-1, $current+1, $furl, $delim, $num_format, $link_format, $selected_format);
		else if($num_pages > 14) {
			$i = floor($num_pages/2);
			$rv .= '<span class="etc">...</span>'.create_pages_sub($current, $i-1, $i+1, $furl, $delim, $num_format, $link_format, $selected_format);
		}
		if($current >= $num_pages-4 and $current < $num_pages-1) $rv .= '<span class="etc">...</span>'.create_pages_sub($current, $current-1, $num_pages, $furl, $delim, $num_format, $link_format, $selected_format);
		else $rv .= '<span class="etc">...</span>'.create_pages_sub($current, $num_pages-2, $num_pages, $furl, $delim, $num_format, $link_format, $selected_format);
	}
	else $rv = create_pages_sub($current, 1, $num_pages, $furl, $delim, $num_format, $link_format, $selected_format);
	if($backnext) $rv = ($current>1 ? sprintf($link_format, sprintf($furl, floor($current-1)), "&lt;"):"&lt;").$rv.($current<($num_pages-1) ? sprintf($link_format, sprintf($furl, floor($current+1)), "&gt;") : " &gt;");
	if($num_pages > 9 and $backnext) {
		$id = 'PagesJumpTo'.(mt_rand()+$num_pages);
		$code = '<span class="pages-jump-to" onclick="$(this).next().css({left:$(this).position().left+\'px\'}).show().children(\'input:first\').focus();"><img src="'.STATIC_CONTENT_DOMAIN.'/img/p.gif" class="arrow-down-icon" alt=""></span>';
		$code .= '<form method="post" action="'.htmlspecialchars($furl).'" onsubmit="if(this.p.value>0&&this.p.value<='.$num_pages.'){this.action=this.action.replace(/%s/,this.p.value);document.location=this.action;}return false;">';
		$code .= 'Gehe zu: <input type="text" class="page" name="p" onfocus="clearTimeout('.$id.');" onblur="'.$id.'=setTimeout(function(){$(\'#'.$id.' form\').hide();},200);"> ';
		$code .= '<button type="submit" class="button" onfocus="clearTimeout('.$id.');" onblur="'.$id.'=setTimeout(function(){$(\'#'.$id.' form\').hide();},200);">Los</button>';
		$code .= '</form>';
		$rv .= '<span id="'.$id.'"></span><script type="text/javascript">$(\'#'.$id.'\').html(\''.addslashes($code).'\');</script>';
	}
	return $rv;
}


function urlenc($d) {
	$a = array('Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ü' => 'ue', 'ö' => 'oe', 'ß' => 'ss');
	foreach($a as $k=>$v) $d = str_replace($k, $v, $d);
	foreach($a as $k=>$v) $d = str_replace(utf8_encode($k), $v, $d);
	return urlencode(preg_replace('~[^A-Za-z0-9\.]~', '-', truncate(stripslashes($d))));
}

function remote_url_ready($d) {
	if(!preg_match('~^(https?|ftp)://~i', $d)) $d= 'http://'.$d;
	return $d;
}

function ilchk($data) {
	$a = md5($data);
	$c = base_convert($a, 16, 36);
	if(strlen($c) > 6) $c = substr($c, 2, 6);
	return substr($a, 0, 2).$c;
}

function create_invite_code() {
	$invite = ilchk(rand().rand().time().rand());
	while(db()->query("SELECT id FROM invite_codes WHERE code='$invite' LIMIT 1")->num_rows)
		$invite = ilchk($invite.rand());
	db()->query("INSERT INTO invite_codes SET code='$invite'");
	return $invite;
}


function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
	if($length == 0) return '';
	if(mb_strlen($string) > $length) {
		$length -= min($length, mb_strlen($etc));
		if(!$break_words && !$middle) {
			$string = mb_ereg_replace('~\s+?(\S+)?$~', '', mb_substr($string, 0, $length+1));
		}
		if(!$middle) return mb_substr($string, 0, $length) . $etc;
		else return mb_substr($string, 0, $length/2) . $etc . mb_substr($string, -$length/2);
	}
	else return $string;
}


function set_metatags($keywords, $description) {
	G::$META_KEYWORDS = str_replace('%%', G::$META_KEYWORDS, $keywords);
	G::$META_DESCRIPTION = str_replace('%%', G::$META_DESCRIPTION, $description);
}


function explode_arr_list($str) {
	$rv = explode(',', preg_replace('~,,+~', ',', trim($str, ',')));
	if(count($rv) == 1 and $rv[0] === '') return array();
	return $rv;
}
function implode_arr_list($arr) {
	return trim(preg_replace('~,,+~', ',', implode(',', $arr)), ',');
}
function remove_arr_value($arr, $value) {
	$new = [];
	foreach($arr as $k=>$v)
		if($v != $value)
			$new[$k] = $v;
	return $new;
}




function calculate_pages($num_entries, $step) {
	$a = $num_entries / $step;
	$b = floor($a);
	return $a == $b ? $a : $b + 1;
}


function get_user_language_strings($languages) {
	$r = array();
	if($languages === NULL) $languages = user()->languages;
	foreach($languages as $lang)
		if(isset(G::$LANG_NAMES[$lang]))
			$r[] = G::$LANG_NAMES[$lang];
	return implode(', ', $r);
}

function get_sitelang_string($lang) {
	return @G::$LANG_NAMES[$lang];
}
function get_sitelang_flag($lang) {
	if(!isset(G::$LANG_NAMES[$lang])) return;
	return '<img style="margin:0;" src="'.STATIC_CONTENT_DOMAIN.'/img/sitelang/'.$lang.'.gif" alt="'.$lang.'" title="'.htmlspecialchars(G::$LANG_NAMES[$lang]).'" class="countryflag">';
}
function get_sitelang_flag2($lang_de, $lang_en) {
	if($lang_de and $lang_en) {
		if(LANG == 'de') {
			$img = 'de-en';
			$alt = 'de / en';
			$title = G::$LANG_NAMES['de'].' / '.G::$LANG_NAMES['en'];
		}
		else {
			$img = 'en-de';
			$alt = 'en / de';
			$title = G::$LANG_NAMES['en'].' / '.G::$LANG_NAMES['de'];
		}
	}
	elseif($lang_de) {
		$img = 'de';
		$alt = 'de';
		$title = G::$LANG_NAMES['de'];
	}
	elseif($lang_en) {
		$img = 'en';
		$alt = 'en';
		$title = G::$LANG_NAMES['en'];
	}
	else return;
	return '<img style="margin:0;" src="'.STATIC_CONTENT_DOMAIN.'/img/sitelang/'.$img.'.gif" alt="'.$alt.'" title="'.htmlspecialchars($title).'" class="countryflag">';
}
function get_sitelang_flags($langs) {
	if(!is_array($langs)) $langs = explode_arr_list($langs);
	if(!$langs) return;
	return implode('', array_map('get_sitelang_flag', $langs));
}
function get_lang_query(&$list = array()) {
	static $cache = NULL;
	if($cache === NULL) {
		$temp = array(LANG);
		foreach(user()->languages as $lang)
			if($lang and !in_array($lang, $temp)) $temp[] = $lang;
		if(count($temp) == 2) $cache = array('', $temp);
		else $cache = array($temp[0], $temp);
	}
	$list = $cache[1];
	return $cache[0];
	/*$q = array();
	foreach(user()->languages as $lang)
		$q[] = $table."lang='$lang'";
	return "AND (".implode(' OR ', $q).")";*/
}


function bigbrother($type, $messages) {
	db()->query("
		INSERT LOW_PRIORITY INTO log
		SET
			uid='".USER_ID."',
			type='$type',
			message='".es(implode(" - ", $messages))."'");
}


function wiki_urlencode($wiki) {
	return str_replace('%3A', ':', urlencode(str_replace(' ', '_', $wiki)));
}
function wiki_urldecode($wiki) {
	return str_replace('_', ' ', $wiki);
}
function wiki_idencode($wiki) {
	return str_replace('%', '-', urlencode(str_replace(' ', '_', $wiki)));
}
function wiki_id_text($wiki) {
	#return false;
	static $WIKI_CACHE = [];
	$where = array();
	if(!isset($WIKI_CACHE[$wiki])) {
		if(($WIKI_CACHE[$wiki] = cache_L1::get('wiki_'.$wiki)) !== false) {
			if($WIKI_CACHE[$wiki] == 'EMPTY') $WIKI_CACHE[$wiki] = false;
			return $WIKI_CACHE[$wiki];
		}
	}
	elseif(!$WIKI_CACHE[$wiki]) return false;
	$WIKI_CACHE[$wiki] = db()->query("
		SELECT a.id, h.content
		FROM wiki_history h, wiki_pages a
		LEFT JOIN wiki_aliases b ON b.page=a.id
		WHERE
			(
				a.name='".es($wiki)."' OR
				b.name='".es($wiki)."'
			) AND
			a.lang='".LANG."' AND
			a.deleted=0 AND
			a.history=h.id AND
			a.id=h.page
		GROUP BY a.id
		LIMIT 1")->fetch_assoc();
	if(!$WIKI_CACHE[$wiki]) {
		$WIKI_CACHE[$wiki] = false;
		cache_L1::set('wiki_'.$wiki, 30*60, 'EMPTY');
	}
	else {
		$WIKI_CACHE[$wiki]['content'] = wikicode::parse($wiki, $WIKI_CACHE[$wiki]['content']);
		$WIKI_CACHE[$wiki]['content'] = $WIKI_CACHE[$wiki]['content']->data;
		cache_L1::set('wiki_'.$wiki, 30*60, $WIKI_CACHE[$wiki]);
	}
	return $WIKI_CACHE[$wiki];
}
function wiki_exists($wiki) {
	return wiki_id_text($wiki) ? true : false;
}
function wiki_url_with_check($wiki, $text, $postfix = '') {
	return false;
	return '<a href="/'.LANG.'/wiki/'.str_replace('%3A', ':', urlencode(str_replace(' ', '_', $wiki))).'/'.$postfix.'"'.(wiki_id($wiki) ? '' : ' class="new"').'>'.htmlspecialchars($text).'</a>';
}







function encrypt_ip($ip) {
	return sprintf('%u', crc32($ip.'zh74gi5ofirewgzhr7iseu54jiwt8i5eu'));
	//return mt_rand() % 0xffffff;
	//return ip2long($ip);
	//return sprintf('%u', hexdec(crc32($ip)));
	/*return sprintf('%u', hexdec(hash('adler32', $ip.'soheo5esixjwaplnjes5ojhmtesomhuyst5zemiutojsd\SRHI%CGAJCOI%EUAXRLGCHEKASxgjoiae5xuTZ%EZHOUILJXTGTRGdr'.
		hash('adler32', $ip.'gjesoigasdfjL%CJE%RIAGFF%EOIAPXW$OJRUAGCOJKJTDVHZFbj64d5f4hc86sdc4eafxtcdnnnnj5l,fcrsd').
		"\x14\x53\xa7\x95\x a\x72\x38\x6b\x56\x58\xe4\x40\xa5\x7b\x95\x5d\xda\xab\x23\xbf\x60\xd3\x f\x68\x84\xdb\xe6\x54\x9a\xd7\xed\xbc\x58\xca\x61\xe8\x21\xa5\xee\x77\xb7\x8f\x5c\xb6\x32\xf9\x82\x6d\x41\xe0\x5d\xd9\x13\x52\x5b\xc5\xd0\x17\x64\x4c\xA4\x27\x1c\xe8\x3d\xf9\x25\x5b\xc3\xf6\x8c\x32\xaa\xd9\xbf\x5f\x46\xf7\x54\x4c\x90\xf2\x8b\x66\x9e\x66\x5d\x87\xbd\x64\x75\xa4\x82\xf3\xe4\xdd\xaa\xF5\x18\x71")));*/
}


function rebuild_location() {
	static $location = '';
	if($location) return $location;
	$temp = $_GET;
	if(isset($temp['_action'])) {
		if($temp['_action'] == 'admin' and isset($temp['_admin'])) {
			$location .= '/admin';
			if(!empty($temp['_extended']) and isset($temp[$temp['_admin']])) {
				$location .= '/'.$temp['_admin'].'/'.$temp[$temp['_admin']].'-'.$temp['_extended'];
				unset($temp[$temp['_admin']]);
			}
		}
		elseif(!empty($temp['_extended']) and isset($temp[$temp['_action']])) {
			$location .= '/'.$temp['_action'].'/'.$temp[$temp['_action']].'-'.$temp['_extended'];
			unset($temp[$temp['_action']]);
		}
	}
	foreach($temp as $k=>$v) if(substr($k, 0, 1) != '_') $location .= '/'.urlencode($k).'/'.urlencode($v);
	$location = rtrim($location, '/').'/';
	return $location;
}

function theme_explode_set($set) {
	$set = explode(' ', trim($set));
	$rv = array();
	foreach($set as $v)
		if(preg_match('~^([^!]+)!(.+)~', trim($v), $out))
			$rv[$out[1]] = $out[2];
	return $rv;
}




function url_base64_encode($data) {
	return str_replace('=', '.', str_replace('/', '_', base64_encode($data)));
}
function url_base64_decode($data) {
	return @base64_decode(str_replace('.', '=', str_replace('_', '/', str_replace(' ', '+', $data))));
}



function captcha_encrypt($text) {
	return url_base64_encode(mcrypt_encrypt(
		MCRYPT_RIJNDAEL_256,
		md5('gg5EGRSTA5gehtW&$%EWT$G%we4a3R§%GHE'.@$_SERVER['REMOTE_ADDR'].@$_SERVER['USER_AGENT']),
		$text,
		MCRYPT_MODE_ECB,
		mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
}
function captcha_decrypt($text) {
	return trim(mcrypt_decrypt(
		MCRYPT_RIJNDAEL_256,
		md5('gg5EGRSTA5gehtW&$%EWT$G%we4a3R§%GHE'.@$_SERVER['REMOTE_ADDR'].@$_SERVER['USER_AGENT']),
		url_base64_decode($text),
		MCRYPT_MODE_ECB,
		mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
}





function view_fsk18_blocked() {
	$tpl = new ilphp('fsk18_blocked.ilp');
	return $tpl->ilphp_fetch();
}

function get_avatar_url($id) {
	if(($a = cache_L1::get('avatar_'.$id)) !== false) return $a;
	$a = image::querylinks($id);
	if($a) $a = $a[0];
	else $a = STATIC_CONTENT_DOMAIN.'/img/no_avatar.jpg';
	cache_L1::set('avatar_'.$id, 60, $a);
	return $a;
}

function my_file_get_contents($url, $data = NULL) {
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_AUTOREFERER => true,
		CURLOPT_BINARYTRANSFER => true,
		CURLOPT_COOKIESESSION => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => array('Accept-Language: en-en,en;q=0.8,en-us;q=0.5,en;q=0.3'),
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1',
		CURLOPT_URL => $url
	]);
	if(PROXY_HOST)
		curl_setopt_array($curl, [
			CURLOPT_PROXY => PROXY_HOST,
			CURLOPT_PROXYPORT => PROXY_PORT,
			CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5
		]);
	if($data)
		curl_setopt_array($curl, [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $data
		]);
	$data = curl_exec($curl);
	curl_close($curl);
	return $data;
}

function inet6_pton($ip) {
	$p = inet_pton($_SERVER['REMOTE_ADDR']);
	if(strlen($p) == 4) return "\0\0\0\0\0\0\0\0\0\0\0\0".$p;
	return $p;
}

function hash_to_sql($hash) {
	$out = array();
	foreach($hash as $k=>$v) $out[] = "$k='".es($v)."'";
	return $out;
}














function get_trash_mails() {
	return array(
		'kurzepost.de',
		'objectmail.com',
		'proxymail.eu',
		'rcpt.at',
		'trash-mail.at',
		'trashmail.at',
		'trashmail.me',
		'trashmail.net',
		'wegwerfmail.de',
		'wegwerfmail.net',
		'wegwerfmail.org',
		'owlpic.com',
		'guerrillamailblock.com',
		'trash2009.com',
		'mt2009.com',
		'trashymail.com',
		'mytrashmail.com',
		'tempinbox.com',
		'DingBone.com',
		'FudgeRub.com',
		'BeefMilk.com',
		'LookUgly.com',
		'SmellFear.com',
		'spam.la',
		'trash-mail.com',
		'anonbox.net',
		'sofort-mail.de',
		'mailinator.com',
		'spamherelots.com',
		'MailEater.com',
		'spamgourmet.com',
		'anon-mail.de',
		'spambog.com',
		'spambog.de',
		'discardmail.com',
		'discardmail.de',
		'nervmich.net',
		'spambox.us',
		'tempemail.net',
		'bugmenot.com',
		'mailexpire.com',
		'jetable.org',
		'spamhole.com',
		'temporaryinbox.com',
		'guerrillamail.com',
		'twinmail.de',
		'temp-mail.org',
		'10minutemail.com',
		'sogetthis.com',
		'misterpinball.de',
		'mailexpire.com',
		'spamtrail.com',
		'0815.ru',
		'10minutemail.com',
		'3d-painting.com',
		'antichef.net',
		'BeefMilk.com',
		'bio-muesli.info',
		'bio-muesli.net',
		'cust.in',
		'despammed.com',
		'DingBone.com',
		'discardmail.com',
		'discardmail.de',
		'dontsendmespam.de',
		'emailias.com',
		'ero-tube.org',
		'film-blog.biz',
		'FudgeRub.com',
		'geschent.biz',
		'great-host.in',
		'guerillamail.org',
		'imails.info',
		'jetable.com',
		'kulturbetrieb.info',
		'kurzepost.de',
		'LookUgly.com',
		'mail4trash.com',
		'mailinator.com',
		'mailnull.com',
		'nervmich.net',
		'nervtmich.net',
		'nomail2me.com',
		'nurfuerspam.de',
		'objectmail.com',
		'owlpic.com',
		'proxymail.eu',
		'rcpt.at',
		'recode.me',
		's0ny.net',
		'sandelf.de',
		'SmellFear.com',
		'sneakemail.com',
		'snkmail.com',
		'sofort-mail.de',
		'spam.la',
		'spambog.com',
		'spambog.de',
		'spambog.ru',
		'spamex.com',
		'spamgourmet.com',
		'spammotel.com',
		'super-auswahl.de',
		'teewars.org',
		'tempemail.net',
		'trash-mail.at',
		'trash-mail.com',
		'trash2009.com',
		'trashmail.at',
		'trashmail.me',
		'trashmail.net',
		'trashmail.ws',
		'watch-harry-potter.com',
		'watchfull.net',
		'wegwerf-email.net',
		'wegwerfadresse.de',
		'wegwerfmail.de',
		'wegwerfmail.net',
		'wegwerfmail.org',
		'willhackforfood.biz',
		'yopmail.com');
}


function check_valid_email($email) {
	if(!preg_match('~.+\@.+\..+~', $email)) return 'INVALID_EMAIL';
	if(preg_match('~@('.implode('|', array_map('preg_quote', get_trash_mails())).')$~i', $_POST['email'])) return 'EMAIL_HOST_FORBIDDEN';
}



function array_map_key($key, $arr) {
	return array_map(create_function('$a', 'return $a[\''.$key.'\'];'), $arr);
}
function array_map_call($fn, $arr) {
	return array_map(create_function('$a', 'return $a->'.$fn.'();'), $arr);
}
function array_filter_key($arr, $key, $val) {
	return array_filter($arr, create_function('$a', 'return $a[\''.$key.'\'] != "'.$val.'";'));
}

function array_has_key_value(&$arr, $key, $value) {
	foreach($arr as &$a) {
		if($a[$key] == $value) {
			return true;
		}
	}
	return false;
}

?>
