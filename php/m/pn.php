<?php

if(time() % 5 == 0) {
	db()->query("DELETE FROM user_pns3_online_users WHERE lasttime<SUBTIME(CURRENT_TIMESTAMP,'".LS_USER_ALIVE_TIME."')");
}

class m_pn extends imodule {
	use ilphp_trait;
	use im_way;
	use im_chat;

	protected $pn_id = 0;

	public function __construct() {
		parent::__construct(__DIR__);

		$this->pn = $this->im_chat_add('pn', [
			'INIT_function' => 'INIT_IDLE',
			'table' => 'user_pns3_content',
			'id_field' => 'id',
			'subid_field' => 'subid',
			'time_field' => 'timeadded'
		]);
	}

	protected function INIT(&$args) {
		if(!IS_LOGGED_IN) throw new iexception('403', $this);

		if(!$this->pn_id) $this->pn_id = (int)(empty($args[$this->imodule_name]) ? $args['pn_id'] : $args[$this->imodule_name]);
		if(!$this->pn_id) throw new iexception('403', $this);

		$this->way[] = array(LS('Private Nachrichten'), '/pns/');

		$this->data = $this->query_data($this->pn_id);
		if(!$this->data) throw new iexception('403', $this);
		if($this->data['name'] == '%-SYSTEM%') {
			$this->data['name'] = LS('Systembenachrichtigungen');
			$this->pn->deny_post = true;
		}
		$this->pn->subid = $this->data['id'];
		$this->pn->default_text = ' ';

		$this->url = '/'.LANG.'/pn/'.$this->data['id'].'-'.urlenc($this->data['name']).'/';
		$this->pn->url = $this->url;
		$this->way[] = array($this->data['name'], $this->url);

		$this->pn->INIT($args);
		$this->update_stats();
	}
	public function INIT_IDLE(&$args) {
		try {
			return $this->INIT($args);
		}
		catch(iexception $e) {
			switch($e->msg) {
			default:
				throw $e;
			case '403':
			case 'ACCESS_DENIED':
				return 'IGNORE';
			}
		}
	}

	protected function MODULE(&$args) {
		$this->im_way_title();
		return $this->ilphp_fetch('pn.php.ilp');
	}

	protected function IDLE(&$idle) {
		$this->pn->IDLE($idle, null, null, function(&$args) {
			if(!empty($args['module'])) {
				$key = 'ModulePN'.$this->data['id'].'Stats';
				G::$json_data['e'][$key] = $this->init_head_data(false);
				if(!G::$json_data['e'][$key]) unset(G::$json_data['e'][$key]);
			}
		});
		if(isset($idle['icon'])) {
			G::$json_data['e']['MenuPNs'] = $this->ICON($args);
			if(isset($args['status']) and $args['status'] == $this->imodule_args['icon'])
				unset(G::$json_data['e']['MenuPNs']);
		}
	}

	protected function ICON(&$args) {
		$this->has_new_pns = db()->query("
			SELECT 1
			FROM user_pns3_links
			WHERE user_id='".USER_ID."' AND has_new_message=1
			LIMIT 1")->num_rows;
		$this->imodule_args['icon'] = ($this->has_new_pns ? 1 : 0);
		return $this->ilphp_fetch('pn.php.icon.ilp');
	}

	public function update_stats() {
		db()->query("INSERT INTO user_pns3_online_users SET pn_id='".$this->pn_id."', user_id='".USER_ID."' ON DUPLICATE KEY UPDATE lasttime=CURRENT_TIMESTAMP");
		db()->query("UPDATE user_pns3_links SET has_new_message=0 WHERE user_id='".USER_ID."' AND pn_id='".$this->data['id']."'");
	}

	protected function POST_add_user(&$args) {
		if(!preg_match('~/users/(\d+)~i', $args['link'], $out)) return $this->settings_end(true, 'add_user');
		$user_id = $out[1];
		$users = explode_arr_list($this->data['users']);
		if(in_array($user_id, $users)) return $this->settings_end(true, 'add_user');
		$user =& user($user_id)->i;
		if(!$user) return $this->settings_end(true, 'add_user');
		if(db()->query("SELECT 1 FROM user_pns3_polls WHERE pn_id='".$this->data['id']."' AND user_id='$user_id' AND status='open'")->num_rows)
			return $this->settings_end(true, 'add_user');
		if(count($users) > 1) {
			user(0)->pn_message($this->data['id'], LS('Die Abstimmung zur Teilnahme am Gespr&auml;ch von [url=http://%1%/users/%2%-%3%/]%4%[/url] hat begonnen.', SITE_DOMAIN, $user['user_id'], urlenc($user['nick']), $user['nick']));
			db()->query("
				INSERT INTO user_pns3_polls
				SET
					pn_id='".$this->data['id']."',
					user_id='$user_id',
					reason='invite',
					status='open',
					votes_yes='".USER_ID."'");
		}
		else {
			$users[] = $user_id;
			$users = implode_arr_list($users);
			$involved_users = explode_arr_list($this->data['involved_users']);
			if(!in_array($user_id, $involved_users)) $involved_users[] = $user_id;
			$involved_users = implode_arr_list($involved_users);
			db()->query("UPDATE user_pns3 SET users='".es($users)."', involved_users='".es($involved_users)."' WHERE pn_id='".$this->data['id']."' LIMIT 1");
			db()->query("INSERT IGNORE INTO user_pns3_links SET user_id='$user_id', pn_id='".$this->data['id']."'");
			user(0)->pn_message($this->data['id'], LS('[url=http://%1%/users/%2%-%3%/]%4%[/url] kann jetzt an diesem Gespr&auml;ch teilnehmen.', SITE_DOMAIN, $user['user_id'], urlenc($user['nick']), $user['nick']));
		}
		return $this->settings_end(true, 'add_user');
	}
	protected function POST_remove_user(&$args) {
		$user_id = (int)@$args['user_id'];
		if(!$user_id) page_redir($this->url);
		$users = explode_arr_list($this->data['users']);
		if(!in_array($user_id, $users) or count($users) < 2) page_redir($this->url);
		$new = array();
		foreach($users as $u) if($u != $user_id) $new[] = $u;
		$users = implode_arr_list($new);
		db()->query("UPDATE user_pns3 SET users='".es($users)."' WHERE pn_id='".$this->data['id']."' LIMIT 1");
		db()->query("DELETE FROM user_pns3_links WHERE user_id='$user_id' AND pn_id='".$this->data['id']."'");
		$user =& user($user_id)->i;
		if($user) user(0)->pn_message($this->data['id'], LS('[url=http://%1%/users/%2%-%3%/]%4%[/url] hat das Gespr&auml;ch verlassen.', SITE_DOMAIN, $user['user_id'], urlenc($user['nick']), $user['nick']), false);
		page_redir($this->url);
	}
	protected function POST_leave_pn(&$args) {
		$users = explode_arr_list($this->data['users']);
		if(!in_array(USER_ID, $users)) page_redir($this->url);
		$new = array();
		foreach($users as $u) if($u != USER_ID) $new[] = $u;
		$users = implode_arr_list($new);
		db()->query("UPDATE user_pns3 SET users='".es($users)."' WHERE pn_id='".$this->data['id']."' LIMIT 1");
		db()->query("DELETE FROM user_pns3_links WHERE user_id='".USER_ID."' AND pn_id='".$this->data['id']."'");
		$user =& user(USER_ID)->i;
		if($user) user(0)->pn_message($this->data['id'], LS('[url=http://%1%/users/%2%-%3%/]%4%[/url] hat das Gespr&auml;ch verlassen.', SITE_DOMAIN, $user['user_id'], urlenc($user['nick']), $user['nick']), false);
		page_redir('/'.LANG.'/pns/');
	}
	protected function POST_vote_for_user(&$args) {
		$poll_id = (int)@$args['poll_id'];
		if(!$poll_id) return $this->settings_end(false, 'vote_for_user');
		$yes_or_no = es(@$args['yes_or_no']);
		if(!in_array($yes_or_no, array('yes', 'no'))) return $this->settings_end(false, 'vote_for_user');
		$poll = db()->query("SELECT user_id, reason, votes_yes, votes_no FROM user_pns3_polls WHERE id='$poll_id' AND pn_id='".$this->data['id']."' LIMIT 1")->fetch_assoc();
		if(!$poll or USER_ID == $poll['user_id']) return $this->settings_end(false, 'vote_for_user');
		$yes = explode_arr_list($poll['votes_yes']);
		$no = explode_arr_list($poll['votes_no']);
		if(in_array(USER_ID, $yes) or in_array(USER_ID, $no)) return $this->settings_end(false, 'vote_for_user');
		if($yes_or_no == 'yes') $yes[] = USER_ID;
		else $no[] = USER_ID;
		db()->query("UPDATE user_pns3_polls SET votes_yes='".es(implode_arr_list($yes))."', votes_no='".es(implode_arr_list($no))."' WHERE id='$poll_id' AND pn_id='".$this->data['id']."' LIMIT 1");
		$users = explode_arr_list($this->data['users']);

		if((count($yes) / count($users)) > 0.5) $final_result = 'yes';
		elseif((count($no) / count($users)) >= 0.5) $final_result = 'no';
		else return $this->settings_end(false, 'vote_for_user');

		db()->query("UPDATE user_pns3_polls SET status='$final_result' WHERE id='$poll_id' AND pn_id='".$this->data['id']."' LIMIT 1");
		$user =& user($poll['user_id'])->i;
		if(!$user) return $this->settings_end(false, 'vote_for_user');
		if($final_result == 'yes') {
			//send YES pn
			if($poll['reason'] == 'invite') {
				if(in_array($poll['user_id'], $users)) return $this->settings_end(false, 'vote_for_user');
				$users[] = $poll['user_id'];
				$users = implode_arr_list($users);
				$involved_users = explode_arr_list($this->data['involved_users']);
				if(!in_array($poll['user_id'], $involved_users)) $involved_users[] = $poll['user_id'];
				$involved_users = implode_arr_list($involved_users);
				db()->query("UPDATE user_pns3 SET users='".es($users)."', involved_users='".es($involved_users)."' WHERE pn_id='".$this->data['id']."' LIMIT 1");
				db()->query("INSERT IGNORE INTO user_pns3_links SET user_id='".$poll['user_id']."', pn_id='".$this->data['id']."'");
				user(0)->pn_message($this->data['id'], LS('[url=http://%1%/users/%2%-%3%/]%4%[/url] kann jetzt an diesem Gespr&auml;ch teilnehmen.', SITE_DOMAIN, $user['user_id'], urlenc($user['nick']), $user['nick']));
			}
			else {
				if(!in_array($poll['user_id'], $users)) return $this->settings_end(false, 'vote_for_user');
				$new = array();
				foreach($users as $u) if($u != $user_id) $new[] = $u;
				$users = implode_arr_list($new);
				db()->query("UPDATE user_pns3 SET users='".es($users)."' WHERE pn_id='".$this->data['id']."' LIMIT 1");
				db()->query("DELETE FROM user_pns3_links WHERE user_id='".$poll['user_id']."' AND pn_id='".$this->data['id']."'");
				user(0)->pn_message($this->data['id'], LS('[url=http://%1%/users/%2%-%3%/]%4%[/url] wurde aus diesem Gespr&auml;ch ausgeschlossen.', SITE_DOMAIN, $user['user_id'], urlenc($user['nick']), $user['nick']));
			}
		}
		else {
			//send NO pn
			if($poll['reason'] == 'invite') {
				user(0)->pn_message($this->data['id'], LS('[url=http://%1%/users/%2%-%3%/]%4%[/url] wird nicht am Gespr&auml;ch teilnehmen.', SITE_DOMAIN, $user['user_id'], urlenc($user['nick']), $user['nick']));
			}
			else {
				user(0)->pn_message($this->data['id'], LS('[url=http://%1%/users/%2%-%3%/]%4%[/url] wird nicht aus diesem Gespr&auml;ch ausgeschlossen.', SITE_DOMAIN, $user['user_id'], urlenc($user['nick']), $user['nick']));
			}
		}
		return $this->settings_end(false, 'vote_for_user');
	}
	private function settings_end($with_settings = true, $sub = '') {
		if($sub and IS_AJAX) {
			$this->data = $this->query_data($this->data['id']);
			if($this->data['name'] == '%-SYSTEM%') {
				$this->data['name'] = LS('Systembenachrichtigungen');
				$this->data['read_only'] = true;
			}
			else $this->data['read_only'] = false;
			return $this->init_head_data($with_settings, $sub);
		}
		else page_redir($this->url);
	}


	private function query_data($pn_id) {
		return db()->query("
			SELECT
				a.pn_id AS id, a.name AS name,
				a.creator AS creator,
				a.users AS users,
				a.involved_users AS involved_users
			FROM user_pns3 a IGNORE INDEX (users)
			WHERE
				a.pn_id='$pn_id' AND
				MATCH (users) AGAINST ('+".USER_ID."' IN BOOLEAN MODE)
			LIMIT 1")->fetch_assoc();
	}

	public function init_head_data($with_settings, $admin_sub_function = '') {
		if($this->pn->deny_post) return;

		$this->users = db()->query("
			SELECT a.user_id, a.nick nick, b.user_id online, a.languages languages
			FROM users a
			LEFT JOIN user_pns3_online_users b ON b.pn_id='".$this->data['id']."' AND b.user_id=a.user_id
			WHERE ".($this->data['users'] ? "a.user_id IN (".$this->data['users'].")" : "0")." AND a.user_id!='".USER_ID."'
			ORDER BY a.nick");
		$this->polls = db()->query("
			SELECT
				id, user_id, reason, votes_yes, votes_no,
				MATCH (votes_yes,votes_no) AGAINST ('".USER_ID."' IN BOOLEAN MODE) AS has_voted
			FROM user_pns3_polls
			IGNORE INDEX (votes)
			WHERE
				pn_id='".$this->data['id']."' AND
				status='open'
			ORDER BY has_voted DESC, user_id");

		return $this->ilphp_fetch('pn.php.head_data'.($with_settings ? '' : '.stats').'.ilp'.($admin_sub_function ? '|'.$admin_sub_function : ''));
	}

	protected function on_new_message(&$args) {
		db()->query("UPDATE user_pns3_links SET has_new_message=1 WHERE pn_id='".$this->data['id']."' AND user_id!='".USER_ID."'");
	}
}

?>
