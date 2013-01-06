<?php

class m_settings extends im_tabs {
	use ilphp_trait;
	
	public $info = [];
	public $error = [];
	
	public function __construct() {
		parent::__construct(__DIR__);
		
		if(!isset(session::$s['m_settings'])) session::$s['m_settings'] = [];
		if(isset(session::$s['passwort_change_without_old_password'])) {
			session::$s['m_settings']['password_recover'] = session::$s['passwort_change_without_old_password'];
			unset(session::$s['passwort_change_without_old_password']);
		}
	}
	protected function INIT(&$args) {
		$this->url = '/'.LANG.'/settings/';
		$this->way[] = [LS('Einstellungen'), $this->url];
		
		$this->im_tabs_add('profile', LS('Profil'), IS_LOGGED_IN ? TAB_SELF : false);
		$this->im_tabs_add('layout', LS('Layout'), TAB_SELF);
		$this->im_tabs_add('themes', LS('Themen'), TAB_SELF);
		$this->im_tabs_add('kit', LS('Baukasten'), TAB_SELF);
		$this->im_tabs_add('tickets', LS('Tickets'), (IS_LOGGED_IN or @$args['get']['ticket_id']) ? TAB_SELF : false);
		$this->im_tabs_add('filter', LS('Filter'), TAB_SELF);
		
		parent::INIT($args);
	}
	
	protected function TAB_profile($args) {
		$this->user = db()->query("SELECT *, avatar_img avatar FROM users WHERE user_id='".USER_ID."' LIMIT 1")->fetch_assoc();
		$this->user['languages'] = explode_arr_list($this->user['languages']);
		$this->password_recover = (empty(session::$s['m_settings']['password_recover']) ? false : true);
		return $this->ilphp_fetch('settings.php.profile.ilp');
	}
	
	protected function TAB_profile_POST(&$args) {
		$this->user = db()->query("SELECT *, avatar_img avatar FROM users WHERE user_id='".USER_ID."' LIMIT 1")->fetch_assoc();
		
		switch($args['action']) {
		default:
			break;
		
		case 'email':
			$this->profile_email($args);
			return IS_AJAX ? $this->ilphp_fetch('settings.php.profile.ilp|email') : true;
		
		case 'password':
			$this->profile_password($args);
			if(IS_AJAX) {
				$this->password_recover = (empty(session::$s['m_settings']['password_recover']) ? false : true);
				return $this->ilphp_fetch('settings.php.profile.ilp|password');
			}
			return true;
		
		case 'languages':
			$languages = array();
			if(!empty($args['de'])) $languages[] = 'de';
			if(!empty($args['en'])) $languages[] = 'en';
			if(!$languages) $this->error['languages'] = 'NEED_AT_LEAST_ONE_LANGUAGE';
			else {
				$languages = implode_arr_list($languages);
				if($languages != $this->user['languages']) {
					user()->update(['languages' => $languages]);
					$this->user['languages'] = $languages;
				}
			}
			if(IS_AJAX) {
				$this->user['languages'] = explode_arr_list($this->user['languages']);
				return $this->ilphp_fetch('settings.php.profile.ilp|languages');
			}
			return true;
		
		case 'emails_allowed':
			user()->update(['emails_allowed' => empty($args['allow']) ? 0 : 1]);
			if(IS_AJAX) {
				$this->user = db()->query("SELECT *, avatar_img avatar FROM users WHERE user_id='".USER_ID."' LIMIT 1")->fetch_assoc();
				return $this->ilphp_fetch('settings.php.profile.ilp|emails_allowed');
			}
			return true;
		
		case 'forum':
			$this->profile_avatar($args);
			user()->update(['display_signatures' => empty($args['display_signatures']) ? 0 : 1]);
			if(IS_AJAX) {
				$this->user = db()->query("SELECT *, avatar_img avatar FROM users WHERE user_id='".USER_ID."' LIMIT 1")->fetch_assoc();
				return $this->ilphp_fetch('settings.php.profile.ilp|forum');
			}
			return true;
		
		case 'signature':
			if($args['signature'] != $this->user['signature']) {
				$temp = ubbcode::compile($args['signature']);
				$ok = true;
				if(preg_match_all('~\<img[^\>]+src=["\']((http|ftp)s?://.*?)["\']~i', $temp, $out)) {
					for($i = 0, $num = count($out[1]); $i < $num; $i++) {
						if(strlen(@file_get_contents($out[1][$i])) > 2*1024*1024) {
							$ok = false;
							break;
						}
					}
				}
				if(!$ok) $this->error['signature'] = 'SIGNATURE_TOO_BIG';
				else {
					user()->update(['signature' => $args['signature']]);
					$this->user['signature'] = $args['signature'];
					$this->info['signature'] = 'SIGNATURE_CHANGED';
				}
			}
			return IS_AJAX ? $this->ilphp_fetch('settings.php.profile.ilp|signature') : true;
		
		case 'myspace':
			$update = [];
			if(es($args['name']) != $this->user['myspace_name']) {
				$this->user['myspace_name'] = $args['name'];
				$update['myspace_name'] = $args['name'];
			}
			if(es($args['background']) != $this->user['myspace_background']) {
				$this->user['myspace_background'] = $args['background'];
				$update['myspace_background'] = $args['background'];
			}
			if(es($args['content']) != $this->user['myspace']) {
				$this->user['myspace'] = $args['content'];
				$update['myspace'] = $args['content'];
			}
			
			if($update) user()->update($update);
			return IS_AJAX ? $this->ilphp_fetch('settings.php.profile.ilp|myspace') : true;
			
		case 'contact':
			$update = [];
			if(es($args['icq']) != $this->user['icq_num']) {
				$this->user['icq_num'] = $args['icq'];
				$update['icq_num'] = $args['icq'];
			}
			if(es($args['steam']) != $this->user['steam_id']) {
				$this->user['steam_id'] = $args['steam'];
				$update['steam_id'] = $args['steam'];
			}
	
			if($update) user()->update($update);
			return IS_AJAX ? $this->ilphp_fetch('settings.php.profile.ilp|contact') : true;
		}
	}
	private function profile_email($args) {
		if($args['email'] == $this->user['email']) return;
		if(!filter_var($args['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = 'INVALID_EMAIL';
			return;
		}
		if(!$args['pass']) {
			$this->error['email'] = 'NEED_PASSWORD_TO_CHANGE_EMAIL';
			return;
		}
		if(md5($args['pass'].$this->user['salt']) != $this->user['pass']) {
			$this->error['email'] = 'INVALID_PASSWORD';
			return;
		}
		user()->update(['email'=>$args['email']]);
		$this->user = db()->query("SELECT *, avatar_img avatar FROM users WHERE user_id='".USER_ID."' LIMIT 1")->fetch_assoc();
		$this->info['email'] = 'EMAIL_CHANGED';
	}
	private function profile_password($args) {
		if(!$args['new'] and !$args['new2']) return;
		if(!$args['new'] or !$args['new2']) {
			$this->error['password'] = 'PASSWORTS_NOT_MATCH';
			return;
		}
		if(empty(session::$s['m_settings']['password_recover'])) {
			if(!$args['old']) {
				$this->error['password'] = 'NEED_PASSWORD_TO_CHANGE_PASSWORD';
				return;
			}
			if(md5($args['old'].$this->user['salt']) != $this->user['pass']) {
				$this->error['password'] = 'INVALID_PASSWORD';
				return;
			}
		}
		else unset(session::$s['m_settings']['password_recover']);
		
		$salt = $this->user['nick'].$this->user['email'].@$args['old'].$args['new'];
		for($i = 0; $i < 100; $i++) $salt .= mt_rand();
		$salt = md5($salt);
		$pass = md5($args['new'].$salt);
		user()->update(array('pass'=>$pass, 'salt'=>$salt));
		session::$s->del_cookie_user();
		page_redir('/'.LANG.'/login/password_changed/');
		die;
	}
	private function profile_avatar($args) {
		if(!$args['avatar'] and $this->user['avatar']) {
			user()->update(['avatar_img' => '']);
			cache_L1::del('avatar_'.$this->user['user_id']);
			$this->info['forum'] = 'AVATAR_DELETED';
			return;
		}
		if(!$args['avatar'] or $args['avatar'] == $this->user['avatar']) return;
		$avatar = remote_url_ready($args['avatar']);
		$ext = strtolower(substr(strtolower(strrchr($avatar, '.')), 1));
		if($ext != 'jpg' and $ext != 'jpeg' and $ext != 'gif' and $ext != 'png' and $ext != 'bmp') $ext = 'jpg';
		if($ext == 'jpeg') $ext = 'jpg';
		$data = @my_file_get_contents($avatar);
		if(!$data) {
			$this->error['forum'] = 'AVATAR_DOWNLOAD_FAILED';
			return;
		}
		if(strlen($data) > 1*1024*1024) {
			$this->error['forum'] = 'AVATAR_FILESIZE_TOO_BIG';
			return;
		}
		$tempfile = TEMP_DIRECTORY.'/'.USER_ID.'.'.$ext;
		$fh = fopen($tempfile, 'w');
		if(!$fh) {
			$this->error['forum'] = 'AVATAR_SAVE_FAILED';
			return;
		}
		fwrite($fh, $data);
		fclose($fh);
		list($width, $height) = getimagesize($tempfile);
		if($width <= 0 or $height <= 0 or $width > AVATAR_MAX_WIDTH or $height > AVATAR_MAX_HEIGHT) {
			$this->error['forum'] = 'AVATAR_INVALID_SIZE';
			return;
		}
		@unlink($tempfile);
		/*$avatar = AVATAR_DIRECTORY.'/'.USER_ID.'.'.$ext;
		if(file_exists($avatar)) unlink($avatar);
		rename($tempfile, $avatar);
		chmod($avatar, 0777);*/
		$id = image::insert($data, USER_ID.'.'.$ext, false);
		if(!$id) {
			$this->error['forum'] = 'AVATAR_SAVE_FAILED';
			return;
		}
		#$this->user['avatar'] = USER_ID.'.'.$ext;
		$this->user['avatar_img'] = $id;
		#image::spread(array(array(AVATAR_DIRECTORY.'/'.$this->user['avatar'], 'avatars', $this->user['avatar'])));
		#$this->update['avatar'] = $this->user['avatar'];
		user()->update(['avatar_img' => $id]);
		cache_L1::del('avatar_'.$id);
		$this->info['forum'] = 'AVATAR_CHANGED';
	}
	
	
	protected function TAB_layout() {
		$this->LAYOUTS = array(1, 2);
		return $this->ilphp_fetch('settings.php.layout.ilp');
	}
	
	
	protected function TAB_themes() {
		$this->theme_ini = session::$s['theme_ini'];
		$this->ini = parse_ini_file(THEME_INI_DIRECTORY.'/'.session::$s['theme_ini'].'.ini', true);
		return $this->ilphp_fetch('settings.php.themes.ilp');
	}
	
	
	protected function TAB_kit($args) {
		$this->ini = parse_ini_file(THEME_INI_DIRECTORY.'/'.session::$s['theme_ini'].'.ini', true);
		if(!session::$s['theme_preset'] and is_array(session()->getValue('theme_userset'))) {
			$this->current = session::$s['theme_userset'];
			$this->current_url = array();
			foreach($this->current as $k=>$v) $this->current_url[] = "$k!$v";
			$this->current_url = 'http://'.SITE_DOMAIN.'/'.LANG.'/_theme/'.session::$s['theme_ini'].'/_userset/'.implode_arr_list($this->current_url).'/';
		}
		else {
			$this->current = theme_explode_set($this->ini['THEME_SETS'][session::$s['theme_preset']]);
			$this->current_url = 'http://'.SITE_DOMAIN.'/'.LANG.'/_theme/'.session::$s['theme_ini'].'/_preset/'.session::$s['theme_preset'].'/';
		}
		return $this->ilphp_fetch('settings.php.kit.ilp');
	}
	protected function TAB_kit_POST_save(&$args) {
		$ini = parse_ini_file(THEME_INI_DIRECTORY.'/'.session::$s['theme_ini'].'.ini', true);
		$new_set['background'] = trim($args['background']);
		$new_set['mm-style'] = trim($args['mm-style']);
		$new_set['mm-logo'] = trim($args['mm-logo']);
		$new_set['mm-icons'] = trim($args['mm-icons']);
		$new_set['mm-places'] = trim($args['mm-places']);
		$new_set['side-menu'] = trim($args['side-menu']);
		$new_set['module'] = trim($args['module']);
		$new_set['tooltip'] = trim($args['tooltip']);
		$keys = array_keys($new_set);
		foreach($ini['THEME_SETS'] as $name=>$set) {
			$set = theme_explode_set($set);
			$match = true;
			foreach($keys as $key) {
				if($new_set[$key] != $set[$key]) {
					$match = false;
					break;
				}
			}
			if($match) page_redir('/'.LANG.'/_theme/'.session::$s['theme_ini'].'/_preset/'.$name.rebuild_location());
		}
		$rv = array();
		foreach($new_set as $k=>$v) $rv[] = "$k!$v";
		$rv = implode_arr_list($rv);
		page_redir('/'.LANG.'/_theme/'.session::$s['theme_ini'].'/_userset/'.$rv.rebuild_location());
	}
	
	protected function TAB_tickets(&$args) {
		$where = array();
		$where['report_id'] = (int)@$args['ticket_id'];
		if($where['report_id']) {
			if(@$args['pw']) $where['password'] = $args['pw'];
			elseif(IS_LOGGED_IN) $where['user_id'] = USER_ID;
			else throw new iexception('ACCESS_DENIED', $this);
			$this->reports = db()->query("SELECT * FROM report_page WHERE ".implode(' AND ', hash_to_sql($where))." LIMIT 1");
			if(!$this->reports->num_rows) throw new iexception('ACCESS_DENIED', $this);
			$this->page = 1;
			$this->num_pages = 1;
		}
		elseif(!IS_LOGGED_IN) throw new iexception('ACCESS_DENIED', $this);
		else {
			$this->page = (int)@$args['page'];
			if($this->page <= 0) $this->page = 1;
			$this->reports = db()->query("SELECT SQL_CALC_FOUND_ROWS * FROM report_page WHERE user_id='".USER_ID."' ORDER BY t DESC LIMIT ".(($this->page - 1) * 5).", 5");
			$this->num_pages = db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num/5;
		}
		return $this->ilphp_fetch('settings.php.tickets.ilp');
	}

	protected function TAB_filter(&$args) {
		$this->verified_fsk18 = !empty(session::$s['verified_fsk18']);
		return $this->ilphp_fetch('settings.php.filter.ilp');
	}
	protected function TAB_filter_POST_save(&$args) {
		session::$s['verified_fsk18'] = !empty($args['verified_fsk18']);
	}
}

?>
