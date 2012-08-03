<?php

if(time() % 5 == 0) {
	db()->query("DELETE FROM user_chat_online_users WHERE lasttime<SUBTIME(CURRENT_TIMESTAMP,'".LS_USER_ALIVE_TIME."')");
	db()->query("DELETE FROM user_chat_online_guests WHERE lasttime<SUBTIME(CURRENT_TIMESTAMP,'".LS_GUEST_ALIVE_TIME."')");
}

class m_chat extends imodule {
	use ilphp_trait;
	use im_way;
	use im_chat;

	protected $chat_id = 0;
	
	public function __construct() {
		parent::__construct(__DIR__);

		$this->chat = $this->im_chat_add('user', [
			'INIT_function' => 'INIT',
			'table' => 'user_chat_content',
			'id_field' => 'id',
			'subid_field' => 'subid',
			'time_field' => 'timeadded'
		]);
	}
	
	public function INIT(&$args) {
		if(!$this->chat_id) $this->chat_id = (int)(empty($args[$this->imodule_name]) ? $args['chat_id'] : $args[$this->imodule_name]);
		if(!$this->chat_id) throw new iexception('404', $this);
		
		$this->data = $this->query_data($this->chat_id);
		if(!$this->data) throw new iexception('403', $this);
		$this->way = array(array(LS('Chats'), '/'.LANG.'/chats/'));
		if($this->data['category_id']) {
			$this->way[] = array(
				$this->data['category_name'],
				'/chats/'.$this->data['category_id'].'-'.urlenc($this->data['category_name']).'/');
			if($this->data['has_sub_categorys'] and $this->data['sub_category_id'])
				$this->way[] = array($this->data[
					'sub_category_name'],
					'/chats/'.$this->data['category_id'].'-'.urlenc($this->data['category_name']).
					'/sub/'.$this->data['sub_category_id'].'-'.urlenc($this->data['sub_category_name']).'/');
		}
		$this->url = '/'.LANG.'/chat/'.$this->chat_id.'-'.urlenc($this->data['name']).'/';
		$this->chat->url = $this->url;
		$this->way[] = array($this->data['name'], $this->url);
		$this->chat->subid = $this->data['id'];
		$this->chat->has_mod_rights = $this->data['is_admin'];
		$this->chat->default_text = $this->data['default_text'];

		$this->deny_post = user()->denie_entrance('chat');
		if(!$this->deny_post) {
			$this->deny_post = (!IS_LOGGED_IN or (!$this->data['is_admin'] and $this->data['status'] != 'open'));
		}
		#if(!$this->allow_post) $this->reason = LS('Derzeit hast du keine Berechtigung in diesen Chat zu schreiben.');
		$this->chat->places->module->input_box = $this->data['input_box'];
		$this->weight = 4;
		$this->update_stats();

		$this->chat->INIT($args);
	}
	
	protected function MODULE(&$args) {
		if($this->data['is_admin'] and !empty($args['action'])) {
			$data = $this->settings($args, IS_AJAX);
			if($data !== false) return $data;
			if(IS_AJAX) page_redir($this->url.'settings/');
		}
		
		$this->im_way_title();
		return $this->ilphp_fetch('chat.php.ilp');
	}

	protected function POST_change_name(&$args) {
			$name = trim(@$args['name']);
			if($name) db()->query("UPDATE user_chats SET name='".es($name)."' WHERE id='".$this->data['id']."' LIMIT 1");
			return $this->settings_end();
	}
	protected function POST_change_default_text(&$args) {
		$default_text = trim(@$args['default_text']);
		db()->query("UPDATE user_chats SET default_text='".es($default_text)."' WHERE id='".$this->data['id']."' LIMIT 1");
		return $this->settings_end('change_default_text');
	}
	protected function POST_change_lang(&$args) {
		$lang = @$args['lang'];
		if($lang and in_array($lang, lang::$LANGUAGE_PRIORITY))
			db()->query("UPDATE user_chats SET lang='".es($lang)."' WHERE id='".$this->data['id']."' LIMIT 1");
		return $this->settings_end('change_lang');
	}
	protected function POST_change_status(&$args) {
		$status = @$args['status'];
		if($status and in_array($status, array('open', 'closed', 'deleted'))) {
			db()->query("UPDATE user_chats SET status='".es($status)."' WHERE id='".$this->data['id']."' LIMIT 1");
			if($status == 'deleted') page_redir('/'.LANG.'/chats/');
		}
		return $this->settings_end('change_status');
	}
	protected function POST_change_input_box(&$args) {
		$input_box = @$args['input_box'];
		if($input_box and in_array($input_box, array('textarea', 'textarea-ubb', 'input')))
			db()->query("UPDATE user_chats SET input_box='".es($input_box)."' WHERE id='".$this->data['id']."' LIMIT 1");
		return $this->settings_end('change_input_box');
	}
	protected function POST_change_place(&$args) {
		if(!m_chat_global::is_admin()) return false;
		$place = (int)@$args['place'];
		if($place) db()->query("UPDATE user_chats SET place='".es($place)."' WHERE id='".$this->data['id']."' LIMIT 1");
		return $this->settings_end('change_place');
	}
	protected function POST_add_admin(&$args) {
		$args['action'] = 'admins';
		if(preg_match('~/users/(\d+)~i', $args['link'], $out)) {
			$user_id = $out[1];
			$admins = explode_arr_list($this->data['admins']);
			if(!in_array($user_id, $admins) and db()->query("SELECT 1 FROM users WHERE user_id='$user_id' LIMIT 1")->num_rows > 0) {
				$admins[] = $user_id;
				$admins = implode_arr_list($admins);
				db()->query("UPDATE user_chats SET admins='".es($admins)."' WHERE id='".$this->data['id']."' LIMIT 1");
			}
		}
		return $this->settings_end('admins');
	}
	protected function POST_remove_admin(&$args) {
		$args['action'] = 'admins';
		$user_id = (int)@$args['user_id'];
		if($user_id) {
			$admins = explode_arr_list($this->data['admins']);
			if(in_array($user_id, $admins) and (count($admins) > 1 or count(explode_arr_list($this->data['admin_groups'])) > 0)) {
				$admins = implode_arr_list(remove_arr_value($admins, $user_id));
				db()->query("UPDATE user_chats SET admins='".es($admins)."' WHERE id='".$this->data['id']."' LIMIT 1");
			}
		}
		return $this->settings_end('admins');
	}
	protected function POST_add_admin_group(&$args) {
		$args['action'] = 'admin_groups';
		$group_id = preg_replace('~[^\d]~', '', @$args['group_id']);
		if($group_id) {
			$group_id = (int)$group_id;
			$admin_groups = explode_arr_list($this->data['admin_groups']);
			if(!in_array($group_id, $admin_groups) and db()->query("SELECT 1 FROM groups WHERE id='$group_id' LIMIT 1")->num_rows > 0) {
				$admin_groups[] = $group_id;
				$admin_groups = implode_arr_list($admin_groups);
				db()->query("UPDATE user_chats SET admin_groups='".es($admin_groups)."' WHERE id='".$this->data['id']."' LIMIT 1");
			}
		}
		return $this->settings_end('admin_groups');
	}
	protected function POST_remove_admin_group(&$args) {
		$args['action'] = 'admin_groups';
		$group_id = preg_replace('~[^\d]~', '', @$args['group_id']);
		if($group_id) {
			$group_id = (int)$group_id;
			$admin_groups = explode_arr_list($this->data['admin_groups']);
			if(in_array($group_id, $admin_groups) and (count($admin_groups) > 1 or count(explode_arr_list($this->data['admins'])) > 0)) {
				$admin_groups = implode_arr_list(remove_arr_value($admin_groups, $group_id));
				db()->query("UPDATE user_chats SET admin_groups='".es($admin_groups)."' WHERE id='".$this->data['id']."' LIMIT 1");
			}
		}
		return $this->settings_end('admin_groups');
	}
	protected function POST_add_user(&$args) {
		$args['action'] = 'users';
		if(preg_match('~/users/(\d+)~i', $args['link'], $out)) {
			$user_id = $out[1];
			$users = explode_arr_list($this->data['users']);
			if(!in_array($user_id, $users) and db()->query("SELECT 1 FROM users WHERE user_id='$user_id' LIMIT 1")->num_rows > 0) {
				$users[] = $user_id;
				$users = implode_arr_list($users);
				db()->query("UPDATE user_chats SET users='".es($users)."' WHERE id='".$this->data['id']."' LIMIT 1");
				if(@$args['send_pn'])
					user($user_id)->pn_system("Du wurdest in einen Chat eingeladen.\n[url=http://icom.to/chat/".$this->data['id'].'-'.urlenc($this->data['name']).'/]Klicke hier um zum Chat zu kommen[/url]');
			}
		}
		return $this->settings_end('users');
	}
	protected function POST_remove_user(&$args) {
		$args['action'] = 'users';
		$user_id = (int)@$args['user_id'];
		if($user_id) {
			$users = explode_arr_list($this->data['users']);
			if(in_array($user_id, $users)) {
				$new = array();
				foreach($users as $u) if($u != $user_id) $new[] = $u;
				$users = implode_arr_list($new);
				db()->query("UPDATE user_chats SET users='".es($users)."' WHERE id='".$this->data['id']."' LIMIT 1");
			}
		}
		return $this->settings_end('users');
	}
	protected function POST_add_banned_user(&$args) {
		$args['action'] = 'banned_users';
		if(preg_match('~/users/(\d+)~i', $args['link'], $out)) {
			$user_id = $out[1];
			$banned_users = explode_arr_list($this->data['banned_users']);
			if(!in_array($user_id, $banned_users) and db()->query("SELECT 1 FROM users WHERE user_id='$user_id' LIMIT 1")->num_rows > 0) {
				$banned_users[] = $user_id;
				$banned_users = implode_arr_list($banned_users);
				db()->query("UPDATE user_chats SET banned_users='".es($banned_users)."' WHERE id='".$this->data['id']."' LIMIT 1");
			}
		}
		return $this->settings_end('banned_users');
	}
	protected function POST_remove_banned_user(&$args) {
		$args['action'] = 'banned_users';
		$user_id = (int)@$args['user_id'];
		if($user_id) {
			$banned_users = explode_arr_list($this->data['banned_users']);
			if(in_array($user_id, $banned_users)) {
				$new = array();
				foreach($banned_users as $u) if($u != $user_id) $new[] = $u;
				$banned_users = implode_arr_list($new);
				db()->query("UPDATE user_chats SET banned_users='".es($banned_users)."' WHERE id='".$this->data['id']."' LIMIT 1");
			}
		}
		return $this->settings_end('banned_users');
	}
	protected function POST_add_group(&$args) {
		$args['action'] = 'groups';
		$group_id = preg_replace('~[^\d]~', '', @$args['group_id']);
		if($group_id != '') {
			$group_id = (int)$group_id;
			$groups = explode_arr_list($this->data['groups']);
			if(!in_array($group_id, $groups) and db()->query("SELECT 1 FROM groups WHERE id='$group_id' LIMIT 1")->num_rows > 0) {
				$groups[] = $group_id;
				$groups = implode_arr_list($groups);
				db()->query("UPDATE user_chats SET groups='".es($groups)."' WHERE id='".$this->data['id']."' LIMIT 1");
			}
		}
		return $this->settings_end('groups');
	}
	protected function POST_remove_group(&$args) {
		$args['action'] = 'groups';
		$group_id = preg_replace('~[^\d]~', '', @$args['group_id']);
		if($group_id != '') {
			$group_id = (int)$group_id;
			$groups = explode_arr_list($this->data['groups']);
			if(in_array($group_id, $groups)) {
				$new = array();
				foreach($groups as $g) if($g != $group_id) $new[] = $g;
				$groups = implode_arr_list($new);
				db()->query("UPDATE user_chats SET groups='".es($groups)."' WHERE id='".$this->data['id']."' LIMIT 1");
			}
		}
		return $this->settings_end('groups');
	}
	protected function POST_needed_points(&$args) {
		$points_from = (int)@$args['points_from'];
		$points_to = (int)@$args['points_to'];
		db()->query("UPDATE user_chats SET points_from='".$points_from."', points_to='".$points_to."' WHERE id='".$this->data['id']."' LIMIT 1");
		return $this->settings_end('needed_points');
	}
	protected function POST_change_content_ubb(&$args) {
		$content_ubb = trim(@$args['content_ubb']);
		if(!$this->data['allow_ubb']) $content_ubb = '';
		db()->query("UPDATE user_chats SET content_ubb='".es($content_ubb)."' WHERE id='".$this->data['id']."' LIMIT 1");
		return $this->settings_end();
	}
	protected function POST_change_content_html(&$args) {
		$content_html = trim(@$args['content_html']);
		if(!$this->data['allow_html']) $content_html = '';
		db()->query("UPDATE user_chats SET content_html='".es($content_html)."' WHERE id='".$this->data['id']."' LIMIT 1");
		return $this->settings_end();
	}
	protected function POST_change_category(&$args) {
		$category_id = (int)@$args['category_id'];
		if($category_id) {
			$category = db()->query("SELECT id, has_sub_categorys FROM user_chat_categorys WHERE id='$category_id' AND ".(m_chat_global::is_ultra_admin() ? "1" : "(MATCH (groups) AGAINST ('".implode(' ', user()->groups)."' IN BOOLEAN MODE) OR id='".$this->data['category_id']."')")." LIMIT 1")->fetch_assoc();
			if($category) {
				$sub_category_id = 0;
				$sub_category = db()->query("SELECT id FROM user_chat_sub_categorys WHERE category_id='$category_id' ORDER BY ".LQ('name_LL')." LIMIT 1")->fetch_assoc();
				if($sub_category) $sub_category_id = $sub_category['id'];
				db()->query("UPDATE user_chats SET category_id='".es($category_id)."', sub_category_id='".es($sub_category_id)."' WHERE id='".$this->data['id']."' LIMIT 1");
			}
		}
		return $this->settings_end();
	}
	protected function POST_change_sub_category(&$args) {
		$sub_category_id = (int)@$args['sub_category_id'];
		if($sub_category_id) {
			$sub_category = db()->query("SELECT 1 FROM user_chat_sub_categorys WHERE id='$sub_category_id' AND category_id='".$this->data['category_id']."' LIMIT 1")->fetch_assoc();
			if($sub_category) db()->query("UPDATE user_chats SET sub_category_id='".es($sub_category_id)."' WHERE id='".$this->data['id']."' LIMIT 1");
		}
		return $this->settings_end();
	}
	private function settings_end($sub = '') {
		$this->data = $this->query_data($this->data['id']);
		$this->init_setting_vars();
		if($sub and IS_AJAX) return $this->init_head_data(true, $sub);
		else page_redir($this->url.'settings/');
	}
	
	protected function MENU(&$args) {
		try {
			$this->INIT($args);
			if(user()->denie_entrance('chat')) return LS('403 Forbidden - Zugriff verweigert.');
			$this->chat_name = $this->data['name'];
			return $this->chat->RENDER('menu').'<p class="all-entries" style="border-top:0"><a href="'.htmlspecialchars($this->chat->url).'">'.LS('Alle Eintr&auml;ge').'</a>';
		}
		catch(iexception $e) {
			switch($e->msg) {
			default:
				throw $e;
			case '403':
			case 'ACCESS_DENIED':
				return;
			}
		}
	}
	
	protected function IDLE(&$idle) {
		$this->chat->IDLE($idle, null, null, function(&$args) {
			if(!empty($args['module'])) G::$json_data['e']['ModuleChat'.$this->data['id'].'Stats'] = $this->init_head_data(false);
		});
	}
	
	private function query_data($chat_id) {
		return db()->query("
			SELECT
				a.id AS id, a.category_id, a.sub_category_id,
				a.lang AS lang,
				a.name AS name,
				a.default_text AS default_text,
				a.creator AS creator,
				a.admins AS admins, a.admin_groups AS admin_groups, a.users AS users, a.banned_users AS banned_users, a.groups AS groups,
				a.status AS status, a.input_box AS input_box,
				a.place AS place,
				a.content_ubb AS content_ubb,
				a.content_html AS content_html,
				a.points_from AS points_from, a.points_to AS points_to,
				".LQ('b.name_LL')." AS category_name,
				b.has_sub_categorys AS has_sub_categorys,
				b.allow_ubb AS allow_ubb,
				b.allow_html AS allow_html,
				b.groups AS category_groups,
				".LQ('c.name_LL')." AS sub_category_name,
				".(m_chat_global::is_low_admin() ? "1" : (
					IS_LOGGED_IN ? "(
						MATCH (a.admins) AGAINST ('+".USER_ID."' IN BOOLEAN MODE) OR
						MATCH (a.admin_groups) AGAINST ('".implode(' ', user()->groups)."' IN BOOLEAN MODE)
					)" : "0"))." AS is_admin
			FROM user_chat_categorys b, user_chats a
			LEFT JOIN user_chat_sub_categorys c ON c.id=a.sub_category_id
			WHERE
				a.id='$chat_id' AND
				a.category_id=b.id AND
				".m_chat_global::build_where()."
			LIMIT 1")->fetch_assoc();
	}
	
	private function update_stats() {
		if(!IS_LOGGED_IN) {
			db()->query("INSERT INTO user_chat_online_guests SET chat_id='".$this->chat_id."', guest_id='".es(USER_ID)."' ON DUPLICATE KEY UPDATE lasttime=CURRENT_TIMESTAMP");
			return;
		}
		$user_visible = true;
		if(m_chat_global::is_admin() and $this->data['id'] != 137) {
			$banned_users = explode_arr_list($this->data['banned_users']);
			if(in_array(USER_ID, $banned_users)) $user_visible = false;
			else
				$admins = explode_arr_list($this->data['admins']);
				$allowed_users = explode_arr_list($this->data['users']);
				if(!in_array(USER_ID, $admins) and
				   !in_array(USER_ID, $allowed_users)) {
				   
				$admin_groups = explode_arr_list($this->data['admin_groups']);
				$found = false;
				foreach($admin_groups as $group_id) {
					if(user()->has_group($group_id)) {
						$found = true;
						break;
					}
				}
				if(!$found) $user_visible = false;
			}
		}
		if($user_visible) db()->query("INSERT INTO user_chat_online_users SET chat_id='".$this->chat_id."', user_id='".USER_ID."' ON DUPLICATE KEY UPDATE lasttime=CURRENT_TIMESTAMP");
	}
	
	public function init_head_data($with_admin_data, $admin_sub_function = '') {
		if($with_admin_data) $this->ilphp_init('chat.php.head_data.ilp'.($admin_sub_function ? '|'.$admin_sub_function : ''));
		else {
			$this->ilphp_init('chat.php.head_data.stats.ilp', 15, $this->data['id']);
			if(($data = cache_L1::get('chat.php.head_data.stats.'.$this->data['id'])) !== false) return $data;
			if(($data = $this->ilphp_cache_load()) !== false) {
				cache_L1::set('chat.php.head_data.stats.'.$this->data['id'], 10, $data);
				return $data;
			}
		}
		
		$this->stats_admins = explode_arr_list($this->data['admins']);
		$this->num_admins = count($this->stats_admins);
		$this->stats_admin_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE ".($this->data['admin_groups'] != '' ? "id IN (".$this->data['admin_groups'].")" : "0")." AND ".LQ('name_LL')." NOT LIKE '\\_%' ORDER BY name");
		$this->stats_banned_users = explode_arr_list($this->data['banned_users']);
		$this->num_banned_users = count($this->stats_banned_users);
		$this->stats_allowed_users = explode_arr_list($this->data['users']);
		$this->num_allowed_users = count($this->stats_allowed_users);
		$this->stats_allowed_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE ".($this->data['groups'] != '' ? "id IN (".$this->data['groups'].")" : "0")." AND ".LQ('name_LL')." NOT LIKE '\\_%' ORDER BY name");
		$this->stats_allowed_groups_array = explode_arr_list($this->data['groups']);
		
		#$this->update_stats();
		
		$this->stats_users = db()->query("
			SELECT user_id
			FROM user_chat_online_users
			WHERE chat_id='".$this->chat_id."'
			ORDER BY user_id");
		$this->stats_guests = db()->query("
			SELECT COUNT(*) AS num
			FROM user_chat_online_guests
			WHERE chat_id='".$this->chat_id."'")->fetch_object()->num;
		
		$this->display_settings = isset($_GET['settings']);
		if($with_admin_data and $this->data['is_admin'] and $this->display_settings) $this->init_setting_vars();
		return $this->ilphp_fetch();
	}
	private function init_setting_vars() {
		$this->LANGUAGE_PRIORITY =& lang::$LANGUAGE_PRIORITY;
		$this->category = db()->query("SELECT *, ".LQ("name_LL")." AS name FROM user_chat_categorys WHERE id='".$this->data['category_id']."' LIMIT 1")->fetch_assoc();
		$this->possible_categorys = db()->query("SELECT id, ".LQ("name_LL")." AS name FROM user_chat_categorys WHERE ".(m_chat_global::is_ultra_admin() ? "1" : "(MATCH (groups) AGAINST ('".implode(' ', user()->groups)."' IN BOOLEAN MODE) OR id='".$this->data['category_id']."')")." ORDER BY place, ".LQ('name_LL'));
		$this->possible_sub_categorys = db()->query("SELECT id, ".LQ("name_LL")." AS name FROM user_chat_sub_categorys WHERE category_id='".$this->data['category_id']."' ORDER BY ".LQ('name_LL'));
		$this->admins = db()->query("SELECT user_id id, nick FROM users WHERE ".($this->data['admins'] ? "user_id IN (".$this->data['admins'].")" : "0")." ORDER BY nick");
		$this->admin_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE id!=".BANNED_GROUPID." AND ".($this->data['admin_groups'] != '' ? "id IN (".$this->data['admin_groups'].")" : "0")." AND ".LQ('name_LL')." NOT LIKE '\\_%' ORDER BY ".LQ('name_LL'));
		$this->available_admin_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE id NOT IN (0,".BANNED_GROUPID.") AND ".LQ('name_LL')." NOT LIKE '\\_%' ".($this->data['admin_groups'] != '' ? " AND id NOT IN (".$this->data['admin_groups'].")" : "")." ORDER BY ".LQ('name_LL'));
		$this->users = db()->query("SELECT user_id id, nick FROM users WHERE ".($this->data['users'] ? "user_id IN (".$this->data['users'].")" : "0")." ORDER BY nick");
		$this->banned_users = db()->query("SELECT user_id id, nick FROM users WHERE ".($this->data['banned_users'] ? "user_id IN (".$this->data['banned_users'].")" : "0")." ORDER BY nick");
		$this->groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE id!=".BANNED_GROUPID." AND ".($this->data['groups'] != '' ? "id IN (".$this->data['groups'].")" : "0")." AND ".LQ('name_LL')." NOT LIKE '\\_%' ORDER BY ".LQ('name_LL'));
		$this->available_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE ".LQ('name_LL')." NOT LIKE '\\_%' ".($this->data['groups'] != '' ? " AND id!=".BANNED_GROUPID." AND id NOT IN (".$this->data['groups'].")" : "")." ORDER BY ".LQ('name_LL'));
	}
}

?>
