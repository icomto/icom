<?php

class m_users extends im_tabs {
	use ilphp_trait;
	use im_pages;
	
	protected $im_tabs_var = 'profile';
	
	public $user = NULL;
	public $errors = [];
	public $allowed_groups_to_change = [];
	
	private $m_bookmarks;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		if(!IS_LOGGED_IN) throw new iexception('ACCESS_DENIED', $this);
		
		if(!$this->user) {
			if(!empty($args['users'])) $user_id = (int)$args['users'];
			else throw new iexception('USER_NOT_FOUND', $this);
			$this->user = db()->query("SELECT *, avatar_img avatar FROM users WHERE user_id='$user_id' LIMIT 1")->fetch_assoc();
			if(!$this->user) throw new iexception('USER_NOT_FOUND', $this);
		}
		if($this->user['deleted']) throw new iexception('USER_DELETED', $this);
		
		$this->allowed_groups_to_change = array();
		if(has_privilege('forum_super_mod')) {
			$this->allowed_groups_to_change[] = LEVEL2_GROUPID;
			$this->allowed_groups_to_change[] = BIRTHDAY_GROUPID;
		}
		if(user()->has_group(RADIOADMIN_GROUPID) or user()->has_group(ADMIN_GROUPID) or user()->has_group(CO_ADMIN_GROUPID))
			$this->allowed_groups_to_change[] = GUEST_DJ_GROUPID;
		
		
		$this->url = '/'.LANG.'/users/'.$this->user['user_id'].'-'.urlenc($this->user['nick']).'/profile/';
		$this->way[] = [LS('Profil von %1%', $this->user['nick']), $this->url];
		
		$this->im_tabs_add('infos', LS('Infos'), $this->user ? TAB_SELF : false);
		$this->im_tabs_add('bookmarks', LS('Lesezeichen'), user()->has_priv($this->user['priv_bookmarks'], user($this->user['user_id'])) ? TAB_SELF : false);
		$this->im_tabs_add('friends', LS('Freunde'), user()->has_priv($this->user['priv_friends'], user($this->user['user_id'])) ? TAB_SELF : false);
		$this->im_tabs_add('myspace', LS('iL Space'), $this->user['myspace'] ? TAB_SELF : false);
		$this->im_tabs_add('guestbook', LS('G&auml;stebuch'), user()->has_priv($this->user['priv_guestbook'], user($this->user['user_id'])) ? TAB_SELF : false);
		
		parent::INIT($args);
	}
	
	
	protected function TAB_infos_POST_add_thrusted_user(&$args) {
		if(USER_ID == $this->user['user_id']) return;
		$trusted_by = explode_arr_list($this->user['trusted_by']);
		if(in_array(USER_ID, $trusted_by)) return;
		if(count($trusted_by) > 5) $this->errors[] = 'TRUST_LIMIT_REACHED';
		$trusted_by[] = USER_ID;
		$this->user['trusted_by'] = implode_arr_list($trusted_by);
		user($this->user['user_id'])->update(['trusted_by' => $this->user['trusted_by']]);
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	protected function TAB_infos_POST_remove_thrusted_user(&$args) {
		$trusted_by = explode_arr_list($this->user['trusted_by']);
		if(USER_ID != $this->user['user_id'] and !in_array($args['user_id'], $trusted_by)) return;
		$this->user['trusted_by'] = implode_arr_list(remove_arr_value($trusted_by, $args['user_id']));
		user($this->user['user_id'])->update(['trusted_by' => $this->user['trusted_by']]);
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	
	
	protected function TAB_infos_POST_frendship_request(&$args) {
		if(user()->friendship_request($this->user['user_id']))
			$this->errors[] = 'FRIENDSHIP_ALREADY_EXISTS';
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	protected function TAB_infos_POST_frendship_cancel(&$args) {
		if(user()->friendship_cancel($this->user['user_id']))
			$this->errors[] = 'FRIENDSHIP_REQUEST_ALREADY_SENT';
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	protected function TAB_infos_POST_frendship_ignore(&$args) {
		if(user()->friendship_ignore($this->user['user_id']))
			$this->errors[] = 'FRIENDSHIP_NOT_RECEIVED';
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	protected function TAB_infos_POST_frendship_accept(&$args) {
		if(user()->friendship_accept($this->user['user_id']))
			$this->errors[] = 'FRIENDSHIP_NOT_RECEIVED';
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	protected function TAB_infos_POST_frendship_end(&$args) {
		if(user()->friendship_end($this->user['user_id']))
			$this->errors[] = 'FRIENDSHIP_NOT_RECEIVED';
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	
	protected function TAB_infos_POST_add_group(&$args) {
		$group_id = (int)$args['group_id'];
		if(!in_array($group_id, $this->allowed_groups_to_change)) return;
		$user = user($this->user['user_id']);
		if(!$user->add_group($group_id)) return;
		$this->user['groups'] = implode_arr_list($user->groups);
		bigbrother('group_added', array($this->user['user_id'], $group_id));
		switch($group_id) {
		case LEVEL2_GROUPID:
			$user->pn_system(utf8_encode('Du hast jetzt vollen Zugang zu den Level 2 Forensektionen.'));
			break;
		case GUEST_DJ_GROUPID:
			$user->pn_system(utf8_encode('Du bist jetzt Mitglied der Gast DJ Benutzergruppe.'));
			break;
		}
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	protected function TAB_infos_POST_remove_group(&$args) {
		$group_id = (int)$args['group_id'];
		if(!in_array($group_id, $this->allowed_groups_to_change)) return;
		$user = user($this->user['user_id']);
		if(!$user->del_group($group_id)) return;
		$this->user['groups'] = implode_arr_list($user->groups);
		bigbrother('group_removed', array($this->user['user_id'], $group_id));
		switch($group_id) {
		case LEVEL2_GROUPID:
			$user->pn_system(utf8_encode('Du hast jetzt keinen Zugang mehr zu den Level 2 Forensektionen.'));
			break;
		case GUEST_DJ_GROUPID:
			$user->pn_system(utf8_encode('Du bist jetzt kein Mitglied der Gast DJ Benutzergruppe mehr.'));
			break;
		}
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	
	protected function TAB_infos_POST_add_note(&$args) {
		if(!has_privilege('forum_mod')) return;
		$message = es($args['message']);
		if($message) db()->query("INSERT INTO user_notes SET user_id='".$this->user['user_id']."', writer_id='".USER_ID."', message='$message'");
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	protected function TAB_infos_POST_remove_note(&$args) {
		if(!has_privilege('forum_mod')) return;
		db()->query("UPDATE user_notes SET status='deleted' WHERE id='".es($args['note_id'])."' AND user_id='".$this->user['user_id']."' LIMIT 1");
		if(IS_AJAX) return '';
	}
	
	protected function TAB_infos_POST_add_warning(&$args) {
		if(!has_privilege('user_warnings')) return;
		user($this->user['user_id'])->add_warning(USER_ID, $args['points'], $args['days'], $args['reason']);
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	protected function TAB_infos_POST_remove_warning(&$args) {
		if(!has_privilege('user_warnings')) return;
		user($this->user['user_id'])->del_warning(USER_ID, $args['warning_id']);
		if(IS_AJAX) return '';
	}
	
	protected function TAB_infos_POST_add_denied_entrance(&$args) {
		if(!has_privilege('shoutboxmaster')) return;
		$place = $args['place'];
		$timeending = (int)$args['days']*60*24 + (int)$args['hours']*60 + (int)$args['minutes'];
		if($timeending > 0) $timeending = "CURRENT_TIMESTAMP+INTERVAL $timeending MINUTE";
		else $timeending = 0;
		$reason = es($args['reason']);
		db()->query("
			INSERT INTO user_denie_entrance
			SET
				user_id='".$this->user['user_id']."',
				mod_id='".USER_ID."',
				place='".es($place)."',
				denie='write',
				timeending=$timeending,
				reason='".es($args['reason'])."'");
		bigbrother('denied_entrance_created', array(db()->insert_id, $this->user['user_id']));
		cache_L1::del('denie_entrance');//DEL_GLOBAL
		user($this->user['user_id'])->pn_system('Du wurdest aus dem Bereich '.$place.' ausgesperrt.');
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	protected function TAB_infos_POST_remove_denied_entrance(&$args) {
		if(!has_privilege('shoutboxmaster')) return;
		db()->query("DELETE FROM user_denie_entrance WHERE id='".es($args['denied_entrance_id'])."' AND user_id='".$this->user['user_id']."' LIMIT 1");
		bigbrother('denied_entrance_deleted', array($args['denied_entrance_id'], $this->user['user_id']));
		cache_L1::del('denie_entrance');//DEL_GLOBAL
		user($this->user['user_id'])->pn_system('Eine Zugangssperre von Dir wurde aufgehoben.');
		if(IS_AJAX) return '';
	}
	
	protected function TAB_infos_POST_change_signature(&$args) {
		if(!has_privilege('forum_admin')) return;
		db()->query("UPDATE users SET signature='".es($args['signature'])."' WHERE user_id='".$this->user['user_id']."' LIMIT 1");
		$this->user['signature'] = $args['signature'];
		if(IS_AJAX) return $this->TAB_infos($args);
	}
	
	protected function TAB_infos(&$args) {
		$lastupdate = strtotime($this->user['lastupdate']);
		$lastaction = strtotime($this->user['lastaction']);
		if($lastupdate < $lastaction and $lastupdate + 1*60*60 < time()) {
			user($this->user['user_id'])->update_points();
			db()->query("UPDATE users SET lastupdate=CURRENT_TIMESTAMP WHERE user_id='".$this->user['user_id']."' LIMIT 1");
		}
		
		$warnings = db()->query("SELECT SUM(points) AS points FROM user_warnings WHERE user_id='".$this->user['user_id']."' AND (timeending='0000-00-00 00:00:00' OR timeending>=CURRENT_TIMESTAMP)")->fetch_object()->points + 0;
		if($warnings != $this->user['open_warnings']) {
			db()->query("UPDATE users SET open_warnings='$warnings' WHERE user_id='".$this->user['user_id']."' LIMIT 1");
			$this->user['open_warnings'] = $warnings;
			cache_L1::del('init_session_user_'.$this->user['user_id'].'_'.$this->user['salt']);
		}
		
		$this->group_ids = explode_arr_list($this->user['groups']);
		
		$this->user['trusted_by'] = explode_arr_list($this->user['trusted_by']);
		
		$this->friend_status = user()->get_friendship_status(user($this->user['user_id']));
		
		$this->forum_threads = db()->query("SELECT COUNT(*) AS num FROM forum_posts a, forum_threads b WHERE a.user_id='".$this->user['user_id']."' AND a.thread_id=b.thread_id AND a.post_id=b.firstpost")->fetch_object()->num;
		$this->forum_posts = db()->query("SELECT COUNT(*) AS num FROM forum_posts WHERE user_id='".$this->user['user_id']."'")->fetch_object()->num;
		$this->wiki_stats = db()->query("
			SELECT
				SUM(IF(action='article_created',1,0)) AS articles,
				SUM(IF(action='content_changed',1,0)) AS changes
			FROM wiki_changes
			WHERE
				wiki_changes.user='".$this->user['user_id']."' AND
				action IN ('article_created','content_changed')")->fetch_assoc();
		$this->shouts =
				db()->query("SELECT COUNT(*) AS num FROM shoutbox_de WHERE user_id='".$this->user['user_id']."'")->fetch_object()->num + db()->query("SELECT COUNT(*) AS num FROM shoutbox_de_archive WHERE user_id='".$this->user['user_id']."'")->fetch_object()->num +
				db()->query("SELECT COUNT(*) AS num FROM shoutbox_en WHERE user_id='".$this->user['user_id']."'")->fetch_object()->num + db()->query("SELECT COUNT(*) AS num FROM shoutbox_en_archive WHERE user_id='".$this->user['user_id']."'")->fetch_object()->num;
		
		$this->forum_reported_posts = db()->query("SELECT SUM(IF(was_good_ticket=1,1,0)) AS good, SUM(IF(was_good_ticket=0,1,0)) AS bad FROM forum_reported_posts WHERE user_id='".$this->user['user_id']."' AND open=0")->fetch_assoc();
		$this->bookmarks = db()->query("SELECT COUNT(*) AS num FROM user_bookmarks WHERE user_id='".$this->user['user_id']."'")->fetch_object()->num;
		
		$this->notes = db()->query("
			SELECT *
			FROM user_notes
			WHERE user_id='".$this->user['user_id']."' AND status='ok'
			ORDER BY timeadded DESC");
		$this->warnings = db()->query("
			SELECT user_warnings.*, IF(timeending='0000-00-00 00:00:00' OR timeending>=CURRENT_TIMESTAMP,0,1) AS ended
			FROM user_warnings
			WHERE user_id='".$this->user['user_id']."'
			ORDER BY timeending DESC");
		$this->denied_entrances = db()->query("
			SELECT
				user_denie_entrance.*,
				UNIX_TIMESTAMP(timeending) AS timeending,
				IF(timeending!=0 AND timeending<CURRENT_TIMESTAMP,1,0) AS ended
			FROM user_denie_entrance
			WHERE user_id='".$this->user['user_id']."'
			ORDER BY IF(timeending=0,'a',CONCAT('b',timeending))");
		
		$this->visitor_user_id = (IS_LOGGED_IN ? USER_ID : 0);
		
		if(has_privilege('groupmanager')) {
			$this->privileges =& G::$DEFAULT_PRIVILEGES;
			user::init_privileges(user($this->user['user_id'])->i);
		}
		
		$this->user['languages'] = explode_arr_list($this->user['languages']);
		
		if(!$args['action'] and cache_L1::get('user_profile_views_'.$this->user['user_id']) === false) {
			cache_L1::set('user_profile_views_'.$this->user['user_id'], 5*60, 1);
			db()->query("UPDATE users SET profile_views=profile_views+1 WHERE user_id='".$this->user['user_id']."' LIMIT 1");
			$this->user['profile_views']++;
		}
		
		return $this->ilphp_fetch('users.php.infos.ilp');
	}
	
	
	
	protected function TAB_bookmarks_INIT(&$args) {
		$this->im_tabs_init_sub($args, ['users', 'bookmarks'], $this->user['user_id'] == USER_ID);
	}
	protected function TAB_bookmarks_POST_change_priv_bookmarks(&$args) {
		if($this->user['user_id'] != USER_ID) throw new iexception('ACCESS_DENIED', $this);
		user()->set_priv('priv_bookmarks', $args['priv_bookmarks']);
		if(!IS_AJAX) page_redir($this->url);
	}
	protected function TAB_bookmarks(&$args) {
		return ($this->user['user_id'] == USER_ID ? $this->ilphp_fetch('bookmarks.php.ilp') : '').$this->im_tabs_sub->RUN('MODULE');
	}
	
	protected function TAB_friends_INIT(&$args) {
		$this->im_tabs_init_sub($args, ['users', 'friends'], $this->user['user_id'] == USER_ID);
	}
	protected function TAB_friends_POST_change_priv_friends(&$args) {
		if($this->user['user_id'] != USER_ID) throw new iexception('ACCESS_DENIED', $this);
		user()->set_priv('priv_friends', $args['priv_friends']);
		if(!IS_AJAX) page_redir($this->url);
	}
	protected function TAB_friends() {
		return ($this->user['user_id'] == USER_ID ? $this->ilphp_fetch('friends.php.ilp') : '').$this->im_tabs_sub->RUN('MODULE');
	}
	
	protected function TAB_guestbook_POST_change_priv_guestbook(&$args) {
		if($this->user['user_id'] != USER_ID) throw new iexception('ACCESS_DENIED', $this);
		user()->set_priv('priv_guestbook', $args['priv_guestbook']);
		if(!IS_AJAX) page_redir($this->url);
	}

	protected function TAB_guestbook() {
		$this->page = (@$_GET['page'] > 1 ? (int)$_GET['page'] : 1);
		if(isset($_POST['guestbook_message']) or isset($_POST['delete_guestbook_entrie']) or has_userrights()) $this->ilphp_init('users.php.guestbook.ilp');
		else {
			$this->ilphp_init('users.php.guestbook.ilp', 10, $this->user['user_id'].'-'.USER_ID.'-'.$this->page);
			if(($data = $this->ilphp_cache_load()) !== false) return $data;;
		}
		
		if(user()->has_priv($this->user['priv_guestbook'], user($this->user['user_id']))) {
			if((USER_ID == $this->user['user_id'] or has_privilege('guestbook_master')) and isset($_POST['delete_guestbook_entrie']))
				db()->query("DELETE FROM user_guestbook WHERE id='".es($_POST['delete_guestbook_entrie'])."' AND user_id='".$this->user['user_id']."' LIMIT 1");
		
			$this->allow_see = true;
			if(db()->query("SELECT 1 FROM user_guestbook WHERE user_id='".$this->user['user_id']."' AND writer='".USER_ID."' AND timeadded>(CURRENT_TIMESTAMP - INTERVAL 15 DAY) LIMIT 1")->num_rows)
				$this->allow_post = 'POST_LIMIT_REACHED';
			else {
				if(isset($_POST['guestbook_message']) and $_POST['guestbook_message']) {
					$this->allow_post = 'POST_LIMIT_REACHED';
					db()->query("
						INSERT INTO user_guestbook
						SET
							user_id='".$this->user['user_id']."',
							writer='".USER_ID."',
							message='".es($_POST['guestbook_message'])."'");
					user($this->user['user_id'])->pn_system('Ein Benutzer hat einen Beitrag in dein Gaestebuch geschrieben.');
				}
				else
					$this->allow_post = 'ALLOW';
			}
			$this->entries = db()->query("
				SELECT SQL_CALC_FOUND_ROWS
					user_guestbook.id AS id,
					user_guestbook.timeadded AS timeadded,
					user_guestbook.message AS message,
					users.user_id AS user_id,
					users.nick AS user_nick,
					users.groups AS user_groups,
					users.points AS user_points,
					users.avatar_img AS user_avatar
				FROM user_guestbook
				LEFT JOIN users ON users.user_id=user_guestbook.writer
				WHERE user_guestbook.user_id='".$this->user['user_id']."'
				ORDER BY user_guestbook.timeadded DESC
				LIMIT ".(($this->page - 1)*20).", 20");
			$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, 20);
		}
		else $this->allow_see = false;
		return $this->ilphp_fetch();
	}
	
	protected function TAB_myspace() {
		return $this->ilphp_fetch('users.php.myspace.ilp');
	}
}

?>
