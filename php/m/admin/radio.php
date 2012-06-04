<?php

class m_admin_radio extends imodule {
	use ilphp_trait;
	use im_way;
	
	public $errors = array();
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		$this->url = '/'.LANG.'/admin/radio/';
		$this->way[] = [LS('Admin'), ''];
		$this->way[] = [LS('Radio'), $this->url];
	}
	
	private function initialize(&$args) {
		$where = [];
		if(has_privilege('radio_admin')) {
			$is_admin = 1;
			$is_guest = 0;
			$where[] = 1;
		}
		else {
			$is_admin = "FIND_IN_SET('".USER_ID."', admins)";
			$is_guest = "FIND_IN_SET('".USER_ID."', guests)";
			$where[] = "($is_admin OR $is_guest)";
		}
		if(@$args['channel']) $where[] = "channel='".es($args['channel'])."'";
		
		return db()->query("
			SELECT
				*,
				$is_admin is_admin,
				$is_guest AS is_guest
			FROM radio
			WHERE ".implode(" AND ", $where)."
			ORDER BY channel");
	}
	
	public function prepare_channel() {
		$this->i['admins'] = explode_arr_list($this->i['admins']);
		$this->i['guests'] = explode_arr_list($this->i['guests']);
		$this->i['djs'] = array_merge($this->i['admins'], $this->i['guests']);
	}
	
	
	protected function POST(&$args) {
		$this->i = $this->initialize($args)->fetch_assoc();
		$this->prepare_channel();
		
		$this->update = [];
		parent::POST($args);
		
		if($this->update) {
			$this->update = hash_to_sql($this->update);
			db()->query("UPDATE radio SET ".implode(', ', $this->update)." WHERE channel='".es($this->i['channel'])."' LIMIT 1");
			
		}
		if(IS_AJAX) {
			$this->i = $this->initialize($args)->fetch_assoc();
			return $this->ilphp_fetch('radio.php.ilp|channel');
		}
	}
	protected function MODULE(&$args) {
		$this->channels = $this->initialize($args);
		return $this->ilphp_fetch('radio.php.ilp');
	}
	
	
	protected function POST_server(&$args) {
		if(!$this->i['is_admin']) return;
		$this->update['host'] = $args['host'];
		$this->update['port'] = (int)$args['port'];
		if(!$this->update['host'] or !$this->update['port']) return;
	}
	
	protected function POST_add_admin(&$args) {
		if(!has_privilege('radio_admin')) return;
		$user_id = (int)preg_replace('~^.*/users/(\d+).*$~', '\1', $args['admin']);
		if(!$user_id) return;
		user($user_id);
		if(in_array($user_id, $this->i['guests'])) $this->update['guests'] = implode_arr_list(remove_arr_value($this->i['guests'], $user_id));
		if(!in_array($user_id, $this->i['admins'])) $this->update['admins'] = implode_arr_list(array_merge($this->i['admins'], [$user_id]));
}
	protected function POST_remove_admin(&$args) {
		if(!has_privilege('radio_admin')) return;
		$user_id = (int)$args['admin_id'];
		if(!$user_id) return;
		user($user_id);
		$this->update['admins'] = implode_arr_list(remove_arr_value($this->i['admins'], $user_id));
	}
	protected function POST_add_guest(&$args) {
		if(!$this->i['is_admin']) return;
		$user_id = (int)preg_replace('~^.*/users/(\d+).*$~', '\1', $args['guest']);
		if(!$user_id) return;
		user($user_id);
		if(in_array($user_id, $this->i['admins'])) return;
		if(!in_array($user_id, $this->i['guests'])) $this->update['guests'] = implode_arr_list(array_merge($this->i['guests'], [$user_id]));
	}
	protected function POST_remove_guest(&$args) {
		if(!$this->i['is_admin']) return;
		$user_id = (int)$args['guest_id'];
		if(!$user_id) return;
		user($user_id);
		$this->update['guests'] = implode_arr_list(remove_arr_value($this->i['guests'], $user_id));
	}
	protected function POST_dj(&$args) {
		if(!$this->i['is_admin'] and !$this->i['is_guest']) return;
		$user_id = (int)$args['dj'];
		if($user_id == 'auto') $user_id = 0;
		else {
			user($user_id);
			if(!in_array($user_id, $this->i['admins']) and !in_array($user_id, $this->i['guests'])) return;
		}
		$this->update['current_dj'] = $user_id;
	}
	protected function POST_infos(&$args) {
		if(!$this->i['is_admin'] and !$this->i['is_guest']) return;
		$this->update['infos'] = $args['infos'];
	}
	protected function POST_chat(&$args) {
		if(!$this->i['is_admin']) return;
		$chat_id = (int)preg_replace('~^.*/chat/(\d+).*$~', '\1', $args['chat']);
		$this->update['chat_id'] = $chat_id;
	}
}

?>
