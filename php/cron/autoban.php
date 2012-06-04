<?php

require '../config.inc.php';
require '../functions.inc.php';
header('Content-Type: text/plain');

$aa = db()->query("SELECT user_id FROM users ORDER BY user_id");
while($a = $aa->fetch_assoc()) {
	if($a['user_id'] % 250 == 0) echo $a['user_id']."\n";
	
	$user = user($a['user_id']);
	switch($user->check_warning_points()) {
	case 'banned':
		echo sprintf("%5s %-18s   %s %s   %3s => BANNED\n", $user->user_id, utf8_encode($user->nick), $user->regtime, $user->lastvisit, $user->open_warnings);
		break;
	case 'unbanned':
		echo sprintf("%5s %-18s   %s %s   %3s => unbanned\n", $user->user_id, utf8_encode($user->nick), $user->regtime, $user->lastvisit, $user->open_warnings);
		break;
	}
	
	$points = $user->i['points'];
	$forum_posts = $user->i['forum_posts'];
	if($user->update_points())
		echo sprintf("%5s %-18s   %7.1f -> %7.1f points   %5s -> %5s forum_posts\n", $user->user_id, utf8_encode($user->nick), $points, $user->i['points'], $forum_posts, $user->i['forum_posts']);
	
	if($user->check_special_groups(true, false))
		echo implode("\n", $changed)."\n";
}

?>
