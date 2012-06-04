<?php

define('ADMIN_USERS_STEP', 10);

class m_admin_users extends imodule {
	use ilphp_trait;
	use im_pages;
	use im_way;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$init) {
		if(!has_privilege('usermanager')) throw new iexception('ACCESS_DENIED', $this);
		
		$this->url = '/'.LANG.'/admin/users/';
		$this->way[] = [LS('Admin'), ''];
		$this->way[] = [LS('Benutzer'), $this->url];
	}
	
	protected function POST(&$args) {
		if(!empty($args['action']))
			return $this->post_($args);
	}
	protected function MODULE(&$args) {
		$this->im_pages_get(@$args['users']);
		$this->im_pages_way();
		$this->im_way_title();
		
		$this->users = db()->query("SELECT SQL_CALC_FOUND_ROWS * FROM users ORDER BY nick LIMIT ".(($this->page - 1)*ADMIN_USERS_STEP).", ".ADMIN_USERS_STEP);
		$this->num_pages = db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num/ADMIN_USERS_STEP;
		
		return $this->ilphp_fetch('users.php.ilp');
	}
	
	public function row($i) {
		$this->i =& $i;
		$this->page = (@$_GET['users'] ? es($_GET['users']) : 1);
		
		$this->groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE FIND_IN_SET(id, '".$i['groups']."') ORDER BY ".LQ('name_LL'));
		$this->available_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE NOT FIND_IN_SET(id, '".$i['groups']."') ORDER BY ".LQ('name_LL'));
		
		return $this->ilphp_fetch('users.php.ilp|row');
	}

	private function post_(&$args) {
		if(!($user_id = (int)@$args['user_id'])) return;
		switch($args['action']) {
		default:
			return;
		case 'rename':
			db()->query("UPDATE users SET nick='".es($args['nick'])."' WHERE user_id='".$user_id."' LIMIT 1");
			return IS_AJAX ? $this->row(db()->query("SELECT * FROM users WHERE user_id='".$user_id."' LIMIT 1")->fetch_assoc()) : true;
		
		case 'add_group':
			$groups = explode_arr_list(db()->query("SELECT groups FROM users WHERE user_id='".$user_id."' LIMIT 1")->fetch_object()->groups);
			$group_id = es($args['group_id']);
			if(!in_array($group_id, $groups)) $groups[] = $group_id;
			db()->query("UPDATE users SET groups='".implode_arr_list($groups)."' WHERE user_id='".$user_id."' LIMIT 1");
			return IS_AJAX ? $this->row(db()->query("SELECT * FROM users WHERE user_id='".$user_id."' LIMIT 1")->fetch_assoc()) : true;
		
		case 'del_group':
			$groups = explode_arr_list(db()->query("SELECT groups FROM users WHERE user_id='".$user_id."' LIMIT 1")->fetch_object()->groups);
			$group_id = es($args['group_id']);
			$groups = remove_arr_value($groups, $group_id);
			db()->query("UPDATE users SET groups='".implode_arr_list($groups)."' WHERE user_id='".$user_id."' LIMIT 1");
			return IS_AJAX ? $this->row(db()->query("SELECT * FROM users WHERE user_id='".$user_id."' LIMIT 1")->fetch_assoc()) : true;
		}
	}
}

?>
