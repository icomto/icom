<?php

class m_online_users extends imodule {
	use ilphp_trait;
	public function __construct() {
		parent::__construct(__DIR__);
	}

	protected function MODULE_query(&$args) {
		$team_groups = array(1, 2, 3, 175, 194, 184, 171, 187, 154, 169);
		$this->online_team = db()->query("
			SELECT user_id, nick, groups
			FROM users
			WHERE
				/*NOT user_id IN (1,167,541,236,5451,4509,5561) AND*/
				(FIND_IN_SET(".implode(',groups) OR FIND_IN_SET(', $team_groups).",groups)) AND
				UNIX_TIMESTAMP(lastvisit)>".(time() - 60)."
			ORDER BY nick");
		$this->online_users = db()->query("
			SELECT user_id, nick
			FROM users
			WHERE
				/*NOT user_id IN (1,167,541,236,5451,4509,5561) AND*/
				UNIX_TIMESTAMP(lastvisit)>".(time() - 60)."
			ORDER BY nick");
	}
	protected function MODULE(&$args) {
		$this->imodule_args['module'] = 1;
		$this->ilphp_init('online_users.php.module.ilp', 10);
		$this->MODULE_query($args);
		return $this->ilphp_fetch();
	}
	protected function MENU(&$args) {
		$this->imodule_args['menu'] = 1;
		$this->ilphp_init('online_users.php.menu.ilp', 10);
		if(($data = cache_L1::get($this->ilphp_cache_file)) !== false) return $data;
		if(($data = $this->ilphp_cache_load()) !== false) {
			cache_L1::set($this->ilphp_cache_file, 5, $data);
			return $data;
		}
		db()->multi_query("
			DELETE FROM guests WHERE lasttime<SUBTIME(CURRENT_TIMESTAMP,'".GUEST_ALIVE_TIME."');
			SELECT SQL_CACHE COUNT(*) AS num FROM users;
			SELECT COUNT(*) AS num FROM guests WHERE lasttime>SUBTIME(CURRENT_TIMESTAMP,'".GUEST_ALIVE_TIME."');
			SELECT SQL_CACHE COUNT(*) AS num FROM users WHERE UNIX_TIMESTAMP(lastvisit)>".(time() - 60));
		db()->next_result();
		$rv = db()->store_result();
		$this->num_registered = $rv->fetch_object()->num;
		$rv->free();
		db()->next_result();
		$rv = db()->store_result();
		$this->num_guests_online = $rv->fetch_object()->num;
		$rv->free();
		db()->next_result();
		$rv = db()->store_result();
		$this->num_online_users = $rv->fetch_object()->num;
		$rv->free();
		return $this->ilphp_fetch();
	}
	protected function IDLE(&$idle) {
		if(!empty($idle['module'])) G::$json_data['r']['ModuleOnlineUsers'] = $this->RUN('MODULE');
		if(!empty($idle['menu'])) G::$json_data['e']['IM_MENU_'.$this->imodule_name] = $this->RUN('MENU');
	}
}

?>
