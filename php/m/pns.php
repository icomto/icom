<?php

class m_pns extends imodule {
	use ilphp_trait;
	use im_way;
	use im_pages;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		if(!IS_LOGGED_IN) throw new iexception('ACCESS_DENIED', $this);
		$this->url = '/'.LANG.'/pns/';
		$this->way[] = array(LS('Private Nachrichten'), $this->url);
	}
	
	protected function MODULE(&$args) {
		db()->query("DELETE FROM user_pns3_online_users WHERE lasttime<SUBTIME(CURRENT_TIMESTAMP,'".LS_USER_ALIVE_TIME."')");
		
		$this->im_pages_get($args[$this->imodule_name]);
		$this->im_pages_way();
		$this->im_way_title();
		
		$this->pns = db()->query("
			SELECT SQL_CALC_FOUND_ROWS
				a.pn_id AS pn_id, a.name AS name, a.users AS users, a.involved_users AS involved_users,
				COALESCE(MAX(b.timeadded),a.timecreated) AS lastmessage,
				COUNT(b.id) AS num_messages,
				l.has_new_message AS has_new_message
			FROM user_pns3 a, user_pns3_links l
			LEFT JOIN user_pns3_content b ON l.pn_id=b.subid
			WHERE l.user_id='".USER_ID."' AND l.pn_id=a.pn_id
			GROUP BY l.pn_id
			ORDER BY lastmessage DESC
			LIMIT ".(($this->page - 1)*USERS_PNBOX_MESSAGES_PER_PAGE).", ".USERS_PNBOX_MESSAGES_PER_PAGE);
		$this->im_pages_calc_sql(USERS_PNBOX_MESSAGES_PER_PAGE);
		
		return $this->ilphp_fetch('pns.php.ilp');
	}
}

?>
