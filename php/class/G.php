<?php

class G {
	public static $json_data;
	
	public static $LANG_NAMES;
	public static $LANG_TIME;
	public static $LANG_JUST_NOW;
	
	public static $SITE_TITLE;
	public static $META_KEYWORDS;
	public static $META_DESCRIPTION;
	
	public static $USER_INTERACTED = false;
	
	public static $DEFAULT_PRIVILEGES;
}

G::$json_data = ['e' => [], 's1' => [], 's' => [], 'keep_loading' => false];

G::$DEFAULT_PRIVILEGES = [
	'email' => false,
	'developer' => false,
	'shoutboxmaster' => false,
	'newswriter' => false,
	'usermanager' => false,
	'groupmanager' => false,
	'user_warnings' => false,
	'forum_admin' => false,
	'forum_mod' => false,
	'forum_super_mod' => false,
	'community_master' => false,
	'inviter' => false,
	'radio' => false,
	'radio_admin' => false,
	'guestbook_master' => false,
	'wiki_admin' => false,
	'wiki_mod' => false,
	'report_page' => false,
	'noads' => false,
	'banned' => false
];

?>
