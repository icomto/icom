<?php

if(!isset($_GET['type']) or !isset($_POST['content']) or !$_POST['content']) die;
if(!isset($_GET['_lllang'])) {
	if(isset($_COOKIE['lang'])) $_GET['_lllang'] = $_COOKIE['lang'];
	else $_GET['_lllang'] = 'de';
}

require_once 'init_session.inc.php';

switch($_GET['type']) {
case 'bbcode':
	echo ubbcode::add_smileys(ubbcode::compile($_POST['content']));
	break;
case 'wiki':
	$ws = wikicode::parse('-', $_POST['content']);
	echo $ws->output;
	break;
}

?>
