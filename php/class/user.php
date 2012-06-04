<?php

// user_id -1 = current user
// user_id  0 = system

class user {
	use user_pns;
	use user_points;
	use user_friends;
	use user_warnings;
	
	public $i;
	public $loaded_from_cache = false; //needed for init_session
	
	public static $class_cache = array();
	public static $user_cache = array();
	public static $group_cache = array();
	public static $denied_entrance_cache = NULL;
	
	public static $USER_TOOLTIPS = [];
	
	public function __construct($user_id = -1, $init_session = false) {
		$this->init($user_id, $init_session);
	}
	public static function c($user_id = -1, $init_session = false) {
		if(!isset(self::$class_cache[$user_id])) self::$class_cache[$user_id] = new user($user_id, $init_session);
		return self::$class_cache[$user_id];
	}
	public function init($user_id = -1, $init_session = false, $flushing = false) {
		if(!$init_session) {
			if(!self::$group_cache) self::init_group_cache();
			if(!is_array(self::$denied_entrance_cache)) self::init_denied_entrance_cache();
		}
		if($user_id == -1) {
			$php_errormsg = NULL;
			if(IS_LOGGED_IN) $user_id = USER_ID;
			else $user_id = 0;
			if($php_errormsg) {
				$ee = debug_backtrace();
				$s = '';
				foreach($ee as $e) $s .= basename($e['file']).': '.$e['function'].':'.$e['line']."\n";
				trigger_error('ERROR: '.$php_errormsg."\n".$s, E_USER_NOTICE);
			}
		}
		if(!$user_id) {
			self::$user_cache[0] = array(
				'user_id'=>0,
				'nick'=>'System',
				'groups'=>array(0),
				'languages'=>array(LANG),
				'lastvisit'=>0,
				'lastaction'=>0,
				'open_warnings'=>0,
				'display_signatures'=>1,
				'points'=>0,
				'forum_posts'=>0,
				'salt'=>'',
				'priv_bookmarks'=>'public',
				'priv_friends'=>'users',
				'priv_guestbook'=>'users',
				'avatar_img'=>'',
				'deleted'=>'0');
			$this->i =& self::$user_cache[0];
			return;
		}
		if(!isset(self::$user_cache[$user_id])) {
			if((self::$user_cache[$user_id] = cache_L1::get('user_'.$user_id)) === false) {
				self::$user_cache[$user_id] = db()->query("
					SELECT
						user_id, nick, groups, languages, lastvisit, lastaction,
						open_warnings, display_signatures, points, forum_posts, salt,
						priv_bookmarks, priv_friends, priv_guestbook,
						avatar_img, deleted
					FROM users
					WHERE user_id='$user_id'
					LIMIT 1")->fetch_assoc();
				if(!self::$user_cache[$user_id]) throw new Exception('USER_NOT_FOUND');
				self::$user_cache[$user_id]['groups'] = explode_arr_list(self::$user_cache[$user_id]['groups']);
				self::$user_cache[$user_id]['groups'][] = 0;
				self::$user_cache[$user_id]['languages'] = explode_arr_list(self::$user_cache[$user_id]['languages']);
				#if(IS_LOGGED_IN and USER_ID == $user_id) $this->init_privileges(self::$user_cache[$user_id]);
				if(!$init_session) {
					#if(defined('IS_LOGGED_IN') and IS_LOGGED_IN and $user_id == USER_ID) $this->init_privileges(self::$user_cache[$user_id]);
					cache_L1::set('user_'.$user_id, 20, self::$user_cache[$user_id]);
				}
			}
		}
		else
			$this->loaded_from_cache = true;
		$this->i =& self::$user_cache[$user_id];
	}
	public function __get($k) {
		if(!isset($this->i[$k])) {
			switch($k) {
			default:
				throw new Exception('KEY NOT FOUND: '.$k);
			case 'email':
			case 'regtime':
			case 'time_on_page':
			case 'lastupdate':
				$a = db()->query("SELECT $k FROM users WHERE user_id='".$this->i['user_id']."' LIMIT 1")->fetch_assoc();
				$this->i[$k] = $a[$k];
				break;
			}
		}
		return $this->i[$k];
	}
	
	public function flush_cache() {
		unset(self::$class_cache[$this->i['user_id']]);
		cache_L1::del('user_'.$this->i['user_id']);//DEL_GLOBAL
		$this->init($this->i['user_id'], false, true);
	}
	
	public static function init_group_cache() {
		if(self::$group_cache) return;
		if(!defined('LANG')) {
			$aa = db()->query("SELECT SQL_CACHE *, name_en AS name FROM groups");
			while($a = $aa->fetch_assoc()) self::$group_cache[$a['id']] = $a;
		}
		elseif((self::$group_cache = cache_L1::get('groups_'.LANG)) === false) {
			$aa = db()->query("SELECT SQL_CACHE *, ".LQ('name_LL')." AS name FROM groups");
			while($a = $aa->fetch_assoc()) self::$group_cache[$a['id']] = $a;
			cache_L1::set('groups_'.LANG, 15*60, self::$group_cache);
		}
	}
	public static function init_denied_entrance_cache() {
		if(is_array(self::$denied_entrance_cache)) return;
		if((self::$denied_entrance_cache = cache_L1::get('denie_entrance')) === false) {
			$aa = db()->query("
				SELECT user_id, place, denie, IF(timeending=0,0,timeending) AS timeending, reason
				FROM user_denie_entrance
				WHERE timeending=0 OR timeending>CURRENT_TIMESTAMP");
			self::$denied_entrance_cache = array();
			while($a = $aa->fetch_assoc()) {
				if(!isset(self::$denied_entrance_cache[$a['user_id']])) self::$denied_entrance_cache[$a['user_id']] = array();
				self::$denied_entrance_cache[$a['user_id']][$a['place']] = $a;
			}
			cache_L1::set('denie_entrance', 20, self::$denied_entrance_cache);
		}
	}
	
	public function html_rank() {
		if($this->i['deleted'])
			return array(
				'p' => 0,
				'de' => LS('Gel&ouml;schter Benutzer'),
				'en' => LS('Deleted user'),
				'css' => 'color:gray;');
		global $FORUM_RANKS;
		foreach($FORUM_RANKS as $rank)
			if($this->i['points'] >= $rank['p'])
				return $rank;
	}
	
	public function has_groups() {
		return !$this->i['deleted'] and $this->i['groups'];
	}
	public function html_groups($max = 1, $delim = '', $ignore = array(USER_GROUPID, LEVEL2_GROUPID), $show_names = false) {
		if($max < 0 or !$this->i['user_id'] or $this->i['deleted']) return;
		$rv = array();
		/*if(!is_array($this->i['groups'])) {
			trigger_error('GROUPS: '.var_dump($this->i['groups']), E_USER_NOTICE);
			$this->i['groups'] = explode_arr_list($this->i['groups']);
		}*/
		$php_errormsg = NULL;
		foreach($this->i['groups'] as $g) {
			if(isset(self::$group_cache[$g]) and self::$group_cache[$g]['public'] and !in_array($g, $ignore)) {
				if($g == 2) {
					if($this->i['user_id'] == 236) $n = $g.'a';
					elseif($this->i['user_id'] == 5451) $n = $g.'b';
					elseif($this->i['user_id'] == 5561) $n = $g.'c';
					elseif($this->i['user_id'] == 1253) $n = $g.'d';
					else $n = $g;
				}
				else $n = $g;
				$rv[self::$group_cache[$g]['weight']] = '<a href="/'.LANG.'/community/users/group/'.$g.'-'.urlenc(self::$group_cache[$g]['name']).'/" class="user-group"><img src="'.STATIC_CONTENT_DOMAIN.'/img/groups/'.$n.'.gif" alt="" title="'.htmlspecialchars(self::$group_cache[$g]['name']).'">'.($show_names ? ' '.htmlspecialchars(self::$group_cache[$g]['name']) : '').'</a>';
			}
		}
		if($php_errormsg) {
			$ee = debug_backtrace();
			$s = '';
			foreach($ee as $e) $s .= basename($e['file']).': '.$e['function'].':'.$e['line']."\n";
			trigger_error('ERROR: '.$php_errormsg."\n".$s, E_USER_NOTICE);
			
		}
		ksort($rv);
		while($max and count($rv) > $max) array_pop($rv);
		return $rv ? implode($delim, $rv) : '';
	}
	public function html_sitelang() {
		if(!$this->i['user_id'] or $this->i['deleted']) return '';
		if(!is_array($this->i['languages'])) $this->i['languages'] = explode_arr_list($this->i['languages']);
		return get_sitelang_flag2(in_array('de', $this->i['languages']), in_array('en', $this->i['languages']));
	}
	public function html($max = 1, $delim = ' ', $ignore = array(USER_GROUPID), $tooltip = true) { //max = 1:one group, 0:all, -1:none
		if(!$this->i['user_id']) return '<strong>'.htmlspecialchars($this->i['nick']).'</strong>';
		if($this->i['deleted']) return '<strong>'.(has_privilege('forum_mod') ? htmlspecialchars($this->i['nick']) : LS('Gast')).'</strong>';
		if($tooltip) {
			if(!isset(self::$USER_TOOLTIPS[$this->i['user_id']])) {
				if(!is_numeric($this->i['lastvisit'])) $this->i['lastvisit'] = strtotime($this->i['lastvisit']);
				if(time() - $this->i['lastvisit'] > 2*60) $status = '<span class="error">('.LS('offline').')</span>';
				elseif(time() - strtotime($this->i['lastaction']) > 5*60) $status ='<span class="warning">('.LS('abwesend').')</span>';
				else $status = '<span class="success">('.LS('online').')</span>';
				
				$tt = ['<a href="/'.LANG.'/users/'.$this->i['user_id'].'-'.urlenc($this->i['nick']).'/" class="user">'.htmlspecialchars($this->i['nick']).'</a> '.$status];
				if($this->i['groups']) $tt[] = LS('Gruppen').': '.$this->html_groups(0, '', array());
				if($this->i['languages']) $tt[] = LS('Sprachen').': '.$this->html_sitelang();
				if($this->i['open_warnings']) $tt[] = LS('Verwarnpunkte').': <span class="error">'.$this->i['open_warnings'].' / 100</span>';
				if($this->i['points']) $tt[] = LS('Punkte').': '.number_format($this->i['points'], 0, ',', '.');
				if($rank = $this->html_rank()) $tt[] = LS('Rang').': <span style="'.$rank['css'].'">'.$rank['de'].'</span>';
				if(IS_LOGGED_IN) $tt[] = '<a href="/'.LANG.'/pn_new/'.$this->i['user_id'].'-'.urlenc($this->i['nick']).'/">'.LS('Private Nachricht senden').'</a>';
				#self::$USER_TOOLTIPS[$this->i['user_id']] = 'data-dd="ildd.user" data-dd-body="'.htmlspecialchars(implode('<br>', $tt)).'"';
				self::$USER_TOOLTIPS[$this->i['user_id']] = implode('<br>', $tt);
			}
			#$tt =& self::$USER_TOOLTIPS[$this->i['user_id']];
			$tt = 'data-dd="ildd.user" data-dd-user-id="'.$this->i['user_id'].'"';
		}
		return ($max < 0 ? '' : $this->html_groups($max, $delim, $ignore)).
			$delim.
			'<a href="/'.LANG.'/users/'.$this->i['user_id'].'-'.urlenc($this->i['nick']).'/" class="user"'.($tooltip ? ' '.$tt : '').'>'.
			htmlspecialchars($this->i['nick']).
			'</a>';
	}
	
	
	public static function USER_TOOLTIPS_JSON() {
		return json_encode(self::$USER_TOOLTIPS);
	}
	
	
	public function is_equal(&$user) {
		return $this->i['user_id'] == $user->i['user_id'];
	}
	
	public function has_priv($value, $user = NULL) {
		if(has_privilege('forum_admin')) return true;
		switch($value) {
		default:
		case 'private':
			return $user and $this->i['user_id'] == $user->i['user_id'];
		case 'friends':
			return $user and ($this->i['user_id'] == $user->i['user_id'] or $this->is_friend_with($user));
		case 'users':
			return IS_LOGGED_IN;
		case 'public':
			return true;
		}
	}
	public function validate_priv($value) {//returns true when ok
		return in_array($value, array('private', 'friends', 'users', 'public'));
	}
	public function set_priv($priv, $value) {
		if($this->i[$priv] == $value) return $this->i[$priv];
		if(!$this->validate_priv($value)) return $this->i[$priv];
		$this->update([$priv => $value]);
		return $value;
	}
	
	public function can_understand_one_language($languages) {
		if(!$languages or !$this->i['languages']) return false;
		foreach($this->i['languages'] as $lang) if(in_array($lang, $languages)) return true;
		return false;
	}
	
	
	public function has_public_group() {
		foreach($this->i['groups'] as $g) if(isset(self::$group_cache[$g]) and self::$group_cache[$g]['public']) return true;
		return false;
	}
	
	public function add_group($group_id) {
		switch($group_id) {
		case USER_GROUPID:
			if(!$this->has_public_group()) return $this->_add_group(USER_GROUPID);
			return false;
		case LEVEL2_GROUPID:
		case LEVEL2_HIDDEN_GROUPID:
			$this->_del_group(USER_GROUPID);
			$retval = $this->_add_group(LEVEL2_HIDDEN_GROUPID);
			if(!$this->has_public_group()) $this->_add_group(LEVEL2_GROUPID);
			return $retval;
		}
		if(isset(self::$group_cache[$group_id]) and self::$group_cache[$group_id]['public']) {//is_public_group
			$this->_del_group(USER_GROUPID);
			$this->_del_group(LEVEL2_GROUPID);
		}
		return $this->_add_group($group_id);
	}
	private function _add_group($group_id) {
		if(in_array($group_id, $this->i['groups'])) return false;
		$this->i['groups'][] = $group_id;
		return $this->update(array('groups'=>implode_arr_list($this->i['groups'])));
	}
	
	public function del_group($group_id) {
		switch($group_id) {
		default:
			$retval = $this->_del_group($group_id);
			break;
		case LEVEL2_GROUPID:
		case LEVEL2_HIDDEN_GROUPID:
			$this->_del_group(LEVEL2_GROUPID);
			$retval = $this->_del_group(LEVEL2_HIDDEN_GROUPID);
			break;
		}
		if(!$this->has_public_group()) {
			if(in_array(LEVEL2_HIDDEN_GROUPID, $this->i['groups'])) $this->_add_group(LEVEL2_GROUPID);
			else $this->_add_group(USER_GROUPID);
		}
		return $retval;
	}
	private function _del_group($group_id) {
		if(!in_array($group_id, $this->i['groups'])) return false;
		$new = array();
		foreach($this->i['groups'] as $g) if($g != $group_id) $new[] = $g;
		$this->i['groups'] = $new;
		return $this->update(array('groups'=>implode_arr_list($this->i['groups'])));
	}
	
	public function has_group($group_id) {
		return in_array($group_id, $this->i['groups']);
	}
	
	
	public function update($update, $flush_cache = true) {
		if(!$update) return true;
		db()->query("UPDATE users SET ".implode(', ', hash_to_sql($update))." WHERE user_id='".$this->i['user_id']."' LIMIT 1");
		if($flush_cache) $this->flush_cache();
		return db()->affected_rows ? true : false;
	}
	
	
	
	//system function
	public function init_session() {
		$this->i['lastvisit'] = time();
		if(!IS_AJAX) $this->i['lastaction'] = date('Y-m-d H:i:s');####### ADD ajax post requests
		if(!isset($this->i['privileges'])) {
			if(!self::$group_cache) self::init_group_cache();
			self::init_privileges($this->i);
			#$this->loaded_from_cache = false;
		}
		if($this->loaded_from_cache) cache_L1::del('user_'.$this->i['user_id']);
		@cache_L1::set('user_'.$this->i['user_id'], 20, $this->i);
	}
	public static function init_privileges(&$i) {
		if(isset($i['privileges'])) return;
		$keys = array_keys(G::$DEFAULT_PRIVILEGES);
		$i['privileges'] = array();
		foreach($i['groups'] as $g) {
			if(!isset(self::$group_cache[$g])) continue;
			foreach($keys as $k) if(self::$group_cache[$g][$k]) $i['privileges'][$k] = true;
		}
	}
	public function has_privilege($privilege) {
		#if(!isset($this->i['privileges'])) $this->init_privileges();
		return IS_LOGGED_IN and isset($this->i['privileges'][$privilege]) and $this->i['privileges'][$privilege] === true;
	}
	
	public function denie_entrance($place) {
		if(isset(self::$denied_entrance_cache[$this->i['user_id']]) and isset(self::$denied_entrance_cache[$this->i['user_id']][$place]))
			return self::$denied_entrance_cache[$this->i['user_id']][$place];
	}
	
	public function has_avatar() {
		return !$this->i['deleted'] and $this->i['avatar_img'];
	}
	public function avatar_html() {
		if($this->i['deleted'] or !$this->i['avatar_img']) return '';
		return '<a href="/'.LANG.'/users/'.$this->i['user_id'].'-'.urlenc($this->i['nick']).'/"><img src="'.htmlspecialchars(get_avatar_url($this->i['avatar_img'])).'" alt=""></a>';
	}
}

?>
