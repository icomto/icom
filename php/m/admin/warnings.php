<?php

class m_admin_warnings extends imodule {
	use ilphp_trait;
	use im_pages;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		if(!has_privilege('community_master')) throw new iexception('ACCESS_DENIED', $this);
		
		$this->url = '/'.LANG.'/admin/warnings/';
		
		$this->user_id = (int)$args[$this->imodule_name];
		$this->url .= $this->user_id.'-'.urlenc(user($this->user_id)->nick).'/';
	}
	
	protected function POST_del_warning(&$args) {
		if(!has_privilege('user_warnings')) return;
		if(!($user_id = (int)$args['user_id'])) return;
		if(!($warning_id = (int)$args['warning_id'])) return;
		user($user_id)->del_warning(USER_ID, $warning_id);
		if(IS_AJAX) return '<p class="success">OK</p>';
	}
	
	protected function MODULE(&$args) {
		$this->im_pages_get(@$args['page']);
		$this->warnings = db()->query("
			SELECT SQL_CALC_FOUND_ROWS
				user_warnings.warning_id, user_warnings.timeadded, user_warnings.timeending, user_warnings.points, user_warnings.reason,
				users.user_id, users.nick,
				IF(timeending<CURRENT_TIMESTAMP,1,0) AS ended
			FROM user_warnings
			JOIN users USING (user_id)
			WHERE user_warnings.warner_id='".$this->user_id."'
			ORDER BY timeadded DESC
			LIMIT ".(($this->page - 1)*15).", 15");
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num_pages")->fetch_object()->num_pages, 15);
		return $this->ilphp_fetch('warnings.php.ilp');
	}
}

?>
