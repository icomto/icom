<?php

class m_bookmark_engine extends imodule {
	public function __construct() {
		if(!IS_LOGGED_IN) throw new iexception('ACCESS_DENIED', $this);
	}
	protected function POST(&$args) {
		if(!($thing = $args['thing'])) return;
		if(!($id = (int)@$args['id'])) return;
		switch($args['action']) {
		default: return;
		case 'add':		db()->query("INSERT IGNORE INTO user_bookmarks SET user_id='".USER_ID."', thing='$thing', thing_id='$id'"); break;
		case 'remove': db()->query("DELETE FROM user_bookmarks WHERE user_id='".USER_ID."' AND thing='$thing' AND thing_id='$id'"); break;
		}
		if(IS_AJAX) return bookmark_engine::_icon(NULL, $thing, $id);
	}
}

?>
