<?php

require_once '../config.inc.php';
require_once '../functions.inc.php';
#require_once '../init_session.inc.php';
header('Content-Type: text/plain');

db()->DEBUG = true;

require_once '../init_session.inc.php';
require_once '../user.polls.inc.php';

define('IS_LOGGED_IN', false);
session::$s['groups'] = array(0);

$aa = db()->query("select id, content from wiki_history");
#$aa = db()->query("select id, content from forum_posts");
#$aa = db()->query("select id, content from forum_posts where id in (1241,7974,26169,100556,100791,297262,142665,145836,146406,216692,163086,164227,164604,171961,179009,187837,189372,227469,227720,239026,252824,265961,268798,281651,281656,297262,311587)");
while($a = $aa->fetch_assoc()) {
	echo $a['id'].' ... ';
	$t = get_militime();
	#ubbcode::compile($a['content']);
	wikicode::parse($a['id'], $a['content']);
	$t = sub_militime($t, get_militime());
	if($t > 0.5) echo $t."\n";
	else echo "\r";
}
echo "\n";

?>
