<?php

require_once 'config.inc.php';
require_once 'functions.inc.php';


function _save_session() {
	#if(function_exists('fastcgi_finish_request')) fastcgi_finish_request();
	ilphp::lock_destroy_all();
	if(session::$s) session()->save();
}



$s = session::start();
new lang();

if(IS_LOGGED_IN) user()->init_session();
$s->init_defaults();


define('CURRENT_LAYOUT', session::$s['layout']);
define('DISPLAY_COMMUNITY_ELEMENTS', session::$s['layout'] == 2);





if(!isset(session::$s['verified_fsk18'])) session::$s['verified_fsk18'] = false;
if(isset($_POST['i_am_old_enough'])) session::$s['verified_fsk18'] = true;
if(!USING_COOKIES and !session::$s['verified_fsk18']) session::$s['verified_fsk18'] = true;





if(has_privilege('banned') and @$_GET['_engine'] != 'banned')
	page_redir('/'.LANG.'/banned/');

?>
