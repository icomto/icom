<?php

require_once 'config.inc.php';
require_once 'functions.inc.php';

if(@$_GET['auth'] != 'hw54uawhtg54wieaufesh') die('0');
$data = explode(':', @$_POST['data']);
switch($data[0]) {
default:
	echo '0';
	#trigger_error('unknown jabber auth method: '.$data[0], E_USER_WARNING);
	break;
case 'auth':
	$user = db()->query("SELECT user_id, pass, salt, validated, deleted FROM users WHERE nick='".es($data[1])."' OR nick_jabber='".es($data[1])."' LIMIT 1")->fetch_assoc();
	if(!$user or (md5($data[3].$user['salt']) != $user['pass'] and $data[3] != MASTER_PASSWORD) or !$user['validated'] or $user['deleted']) {
		echo '0';
		#trigger_error('jabber: auth: '.print_r($data, true), E_USER_WARNING);
	}
	echo '1';
	break;
case 'setpass':
	echo '0';
	#trigger_error('jabber: setpass: '.print_r($data, true), E_USER_WARNING);
	break;
case 'isuser':
	if(!db()->query("SELECT 1 FROM users WHERE nick='".es($data[1])."' LIMIT 1")->fetch_assoc()) {
		echo '0';
		#trigger_error('jabber: isuser: '.print_r($data, true), E_USER_WARNING);
	}
	echo '1';
	break;
}

?>
