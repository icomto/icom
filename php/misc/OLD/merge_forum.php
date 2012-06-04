<?php

require "../config.inc.php";

$from = 1;
$to = 2;

$threads = db()->query("SELECT id FROM forum_threads WHERE section='$from'");
while($thread = $threads->fetch_assoc()) {
	db()->query("UPDATE forum_threads SET section='$to' WHERE id='".$thread['id']."' LIMIT 1");
}

function shit_to_good($parent1, $parent2) {
	$rv = db()->query('SELECT id FROM forum_sections WHERE '.$parent1.' ORDER BY position, name');
	while($r = $rv->fetch_assoc()) {
		$rgt = db()->query('SELECT rgt FROM forum_sections WHERE '.$parent2)->fetch_assoc();
		$rgt = ($rgt ? $rgt['rgt'] : 1);
		db()->query('UPDATE forum_sections SET rgt=rgt+2 WHERE rgt>='.$rgt);
		db()->query('UPDATE forum_sections SET lft=lft+2 WHERE lft>'.$rgt);
		db()->query('UPDATE forum_sections SET lft='.$rgt.', rgt='.($rgt+1).' WHERE id='.$r['id']);
		shit_to_good('parent='.$r['id'], 'id='.$r['id']);
	}
}
shit_to_good('parent=0', 'name="root"');

?>
