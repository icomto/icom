<?php

require_once 'class/G.php';

define('THEME_INI_DIRECTORY', '../themes/_ini');

define('STATIC_CONTENT_DOMAIN', '');

define('RELEASE_VERSION', '5.4.3b');
define('RELEAES_DATE', '2011-04-16 00:42:17');

define('REGISTER_CLOSED', false);
define('REGISTER_NEED_INVITE_CODE', false);
define('REGISTER_OPT_IN', true);

define('USERS_IN_GROUP_PER_PAGE', 250);
define('USERS_PNBOX_MESSAGES_PER_PAGE', 50);

define('NOREPLY_EMAIL', 'noreply@icom.to');

#define('RATING_UNITS', 10);
#define('RATING_UNITWIDTH', 30);

define('FORUM_THREAD_NUM_THREADS_PER_SITE', 15);
define('FORUM_THREAD_NUM_POSTS_PER_SITE', 10);
define('FORUM_SEARCH_ROWS_PER_SITE', 20);
define('FORUM_MENU_STEP', 10);
define('FORUM_LATEST_STEP', 100);

define('SHOUTBOX_MENU_STEP', 5);
define('SHOUTBOX_VIEW_STEP', 25);

define('COMMENTS_MENU_STEP', 5);
define('COMMENTS_VIEW_STEP', 25);

define('DEFAULT_LAYOUT', 2);
define('DEFAULT_THEME_INI', 'a');
define('DEFAULT_THEME_PRESET', 'white');

define('IMAGE_DIRECTORY', '../s/i');
define('THUMB_DIRECTORY', '../s/t');
define('TEMP_DIRECTORY', '/tmp');

define('AVATAR_DIRECTORY', '../avatars');
define('AVATAR_BASEURL', '/avatars/');
define('AVATAR_MAX_WIDTH', 120);
define('AVATAR_MAX_HEIGHT', 120);

define('MAX_WARNING_POINTS', 100);
define('USER_GROUPID', 6);
define('LEVEL2_GROUPID', 197);
define('LEVEL2_HIDDEN_GROUPID', 208);
define("LEVEL2_POINTS", 500);//needed to get this group
define('BANNED_GROUPID', 158);
define('NOAD_GROUPID', 182);
define("NOAD_POINTS", 250);//needed to get this group
define("NOAD_TIMEONPAGE", 350*60*60);//needed to get this group
define('VIP_GROUPID', 180);
define('USER_USER_GROUPID', 196);
define('BIRTHDAY_GROUPID', 192);

define('ADMIN_GROUPID', 1);
define('CO_ADMIN_GROUPID', 2);
define('RADIOADMIN_GROUPID', 187);
define('GUEST_DJ_GROUPID', 204);



$FORUM_RANKS = array(
	array('p'=>7500, 'de'=>'iCom Omniszienz', 	'en'=>'iCom Myth',			'css'=>'color:red;'),
	array('p'=>5000, 'de'=>'iCom Guru',	 	'en'=>'iCom Myth',			'css'=>'color:red;'),
	array('p'=>4100, 'de'=>'iCom Ikone', 		'en'=>'iCom Emperor',		'css'=>'color:red;'),
	array('p'=>3300, 'de'=>'iCom Mythos',		'en'=>'iCom Grandmaster',	'css'=>'color:red;'),
	array('p'=>2600, 'de'=>'iCom Meister',		'en'=>'iCom Legend',		'css'=>'color:red;'),
	array('p'=>2000, 'de'=>'iCom Weiser', 		'en'=>'iCom Ikone',		'css'=>'color:#ff4e00;'),
	array('p'=>1500, 'de'=>'iCom Zombie', 	'en'=>'iCom Ikone',		'css'=>'color:#ff4e00;'),
	array('p'=>1100, 'de'=>'iCom Legende', 		'en'=>'iCom Star',			'css'=>'color:#ff4e00;'),
	array('p'=> 800, 'de'=>'iCom Star', 		'en'=>'iCom Freak',		'css'=>'color:green;'),
	array('p'=> 400, 'de'=>utf8_encode('iCom Jünger'),		'en'=>'iCom Pro',			'css'=>'color:green;'),
	array('p'=> 200, 'de'=>utf8_encode('iCom Süchtling'),	'en'=>'iCom Junkie',		'css'=>'color:green;'),
	array('p'=> 100, 'de'=>'iCom Stammgast',	'en'=>'iCom Regular',		'css'=>''),
	array('p'=>  25, 'de'=>'Hobby Sauger',		'en'=>'Hobby Leecher',		'css'=>''),
	array('p'=>   0, 'de'=>'Neuling',			'en'=>'Newcomer',			'css'=>'')
);

define('GUEST_ALIVE_TIME', '00:14:00.00000');

define('LS_USER_ALIVE_TIME', '00:02:00.00000');
define('LS_GUEST_ALIVE_TIME', '00:10:00.00000');
define('LS_CHAT_LIMIT', 20);
define('LS_CHAT_LIMIT_2', 40);

define('RADIO_DEFAULT_CHANNEL', 'iC1');


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

require_once 'defaults.inc.php';

?>
