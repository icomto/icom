<?php

require_once '../config.inc.php';

$todo = array(
	array('forum_posts', 'post_id', 'content'),
	array('invite_requests', 'id', 'message'),
	array('news', 'news_id', 'introduce_content'),
	array('news', 'news_id', 'content'),
	array('radio', 'channel', 'infos'),
	array('shoutbox_de', 'id', 'message'),
	array('shoutbox_de_archive', 'id', 'message'),
	array('shoutbox_en', 'id', 'message'),
	array('shoutbox_en_archive', 'id', 'message'),
	array('user_chat_content', 'id', 'message'),
	array('user_chats', 'id', 'content_ubb'),
	array('user_chats', 'id', 'content_html'),
	array('user_denie_entrance', 'id', 'reason'),
	array('user_denie_entrance', 'id', 'reason'),
	array('user_guestbook', 'id', 'message'),
	array('user_notes', 'id', 'message'),
	array('user_pns3_content', 'id', 'message'),
	array('user_poll_answers', 'id', 'answer'),
	array('user_polls', 'id', 'question'),
	array('user_warnings', 'warning_id', 'reason'),
	array('users', 'user_id', 'signature'),
	array('users', 'user_id', 'myspace_background'),
	array('users', 'user_id', 'myspace'),
	array('wiki_history', 'id', 'content'),
	array('wiki_tickets', 'id', 'message')
);

foreach($todo as $t) {
	echo sprintf("%-20s - %-10s - %-20s ... ", $t[0], $t[1], $t[2]);
	$i = 0;
	#$aa = db()->query("SELECT {$t[1]} id, {$t[2]} content FROM {$t[0]} WHERE {$t[2]} LIKE '%picit.me%'");
	$aa = db()->query("SELECT {$t[1]} id, {$t[2]} content FROM {$t[0]} WHERE ({$t[2]}) REGEXP 'http://[a-z0-9]*\\.?iload.(to|tv)'");
	while($a = $aa->fetch_assoc()) {
		/*$a['content'] = preg_replace('~(https?://([a-z0-9]*\.)?picit\.me)/v/~i', '\1/i/', $a['content']);
		$a['content'] = preg_replace('~https?://([a-z0-9]*\.)?picit\.me/([ti])/([a-z0-9]{8})[a-z0-9\.\-_%]*[\._](jpe?g|gif|png|bmp)~i', 'http://icom.to/s/\2/\3.\4', $a['content']);
		$a['content'] = preg_replace('~https?://([a-z0-9]*\.)?picit\.me/([ti])/([a-z0-9]{8})\d+/[a-z0-9\.\-_%]*\.(jpe?g|gif|png|bmp)~i', 'http://icom.to/s/\2/\3.\4', $a['content']);*/
		
		$a['content'] = preg_replace('~https?://([a-z0-9]*\.)?iload.(to|tv)~i', 'http://icom.to', $a['content']);
		
		db()->query("UPDATE {$t[0]} SET {$t[2]}='".es($a['content'])."' WHERE {$t[1]}='".es($a['id'])."' LIMIT 1");
		if(db()->affected_rows) echo '.';
		$i += db()->affected_rows;
	}
	echo "$i\n";
}

?>
