<?php

class m_banned extends imodule {
	use ilphp_trait;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function ENGINE(&$args) {
		theme::init($this);
		$this->LANG_TIME =& G::$LANG_TIME;
		
		$this->SITE_TITLE = LS('Du wurdest von %1% gebannt', SITE_NAME);
		$this->META_KEYWORDS =& G::$META_KEYWORDS;
		$this->META_DESCRIPTION =& G::$META_DESCRIPTION;
		
		if(!has_privilege('banned')) page_redir('/'.LANG.'/');
		
		db()->query("INSERT IGNORE INTO banned_ips SET ip=0x".bin2hex(inet6_pton($_SERVER['REMOTE_ADDR'])).", user_id='".USER_ID."'");
		
		$this->warnings = db()->query("
			SELECT
			user_warnings.*, 0 AS ended,
			UNIX_TIMESTAMP(user_warnings.timeadded) AS _timeadded, UNIX_TIMESTAMP(user_warnings.timeending) AS _timeending
			FROM user_warnings
			WHERE user_id='".USER_ID."' AND (timeending='0000-00-00 00:00:00' OR timeending>=CURRENT_TIMESTAMP)
			UNION SELECT
			user_warnings.*, 1 AS ended,
			UNIX_TIMESTAMP(user_warnings.timeadded) AS _timeadded, UNIX_TIMESTAMP(user_warnings.timeending) AS _timeending
			FROM user_warnings
			WHERE user_id='".USER_ID."' AND NOT timeending='0000-00-00 00:00:00' AND timeending<CURRENT_TIMESTAMP
			ORDER BY timeending DESC
		");
		
		$this->ilphp_display('banned.php.ilp');
	}
}

?>
