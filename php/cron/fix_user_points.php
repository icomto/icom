<?php

/*
 * DEPRECATED SCRIPT!!!
 */

require '../config.inc.php';
header("Content-Type: text/plain");


/*
SELECT COUNT(*)*0.1 AS points FROM shoutbox_de WHERE user_id='".$this->i['user_id']."' UNION
SELECT COUNT(*)*0.1 AS points FROM shoutbox_de_archive WHERE user_id='".$this->i['user_id']."' UNION
SELECT COUNT(*)*0.1 AS points FROM shoutbox_en WHERE user_id='".$this->i['user_id']."' UNION
SELECT COUNT(*)*0.1 AS points FROM shoutbox_en_archive WHERE user_id='".$this->i['user_id']."' UNION
SELECT COUNT(*)*0.1 AS points FROM user_chat_content WHERE user_id='".$this->i['user_id']."' UNION

SELECT SUM(c.points)*(1 + ((LENGTH(a.thanks)-LENGTH(REPLACE(a.thanks,',','')))+1)*0.05) AS points
FROM forum_posts a
JOIN forum_threads b USING (thread_id)
JOIN forum_sections c USING (section_id)
WHERE
	a.user_id='".$this->i['user_id']."' AND
	a.thread_id=b.thread_id AND
	b.section_id=c.section_id
*/


echo sprintf("%5s %10s %10s %10s %10s %10s %10s\n", "ID", "OLD", "NEW", "DIFF", "OLD", "NEW", "DIFF");
$users = db()->query("SELECT user_id, points, forum_posts FROM users ORDER BY user_id");
while($user = $users->fetch_assoc()) {
	$points = 0;
	$points += db()->query("SELECT COUNT(*) AS num FROM shoutbox_de WHERE user_id='".$user['user_id']."'")->fetch_object()->num*0.01;
	$points += db()->query("SELECT COUNT(*) AS num FROM shoutbox_de_archive WHERE user_id='".$user['user_id']."'")->fetch_object()->num*0.01;
	
	$points += db()->query("SELECT COUNT(*) AS num FROM shoutbox_en WHERE user_id='".$user['user_id']."'")->fetch_object()->num*0.01;
	$points += db()->query("SELECT COUNT(*) AS num FROM shoutbox_en_archive WHERE user_id='".$user['user_id']."'")->fetch_object()->num*0.01;
	
	$points += db()->query("SELECT COUNT(*) AS num FROM user_chat_content WHERE user_id='".$user['user_id']."'")->fetch_object()->num*0.01;
	
	$points += db()->query("
		SELECT SUM(s.points)*(1 + ((LENGTH(thanks)-LENGTH(REPLACE(thanks,',','')))+1)*0.05) AS points
		FROM forum_posts p, forum_threads t, forum_sections s
		WHERE
			p.user_id='".$user['user_id']."' AND
			p.thread_id=t.thread_id AND
			t.section_id=s.section_id")->fetch_object()->points;
	
	$forum_posts = db()->query("
		SELECT COUNT(*) AS num
		FROM forum_posts
		WHERE user_id='".$user['user_id']."'")->fetch_object()->num;
	
	$points += db()->query("
		SELECT SUM(IF(menu='bor', 0.75, 0.5)) AS points
		FROM forum_reported_posts
		WHERE user_id='".$user['user_id']."' AND open=0 AND was_good_ticket=0")->fetch_object()->points;
	
	$points -= db()->query("SELECT SUM(IF(timeending<CURRENT_TIMESTAMP,points*0.5,points)) AS num FROM user_warnings WHERE user_id='".$user['user_id']."'")->fetch_object()->num;
	if($points < 0) $points = 0;
	
	db()->query("UPDATE users SET points='$points', forum_posts='".$forum_posts."' WHERE user_id='".$user['user_id']."' LIMIT 1");
	if(db()->affected_rows)
		echo sprintf("%5s %10s %10s %10s %10s %10s %10s\n",
			$user['user_id'],
			round($user['points'], 2), round($points, 2), round($points - $user['points'], 2),
			$user['forum_posts'], $forum_posts, $forum_posts - $user['forum_posts']);
}

?>
