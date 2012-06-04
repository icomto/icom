<?php

require '../config.inc.php';

db()->query('UPDATE forum_sections SET lft=0, rgt=0');
db()->query('UPDATE forum_sections SET lft=1, rgt=2 WHERE name_de="root"');

function shit_to_good($parent1, $parent2) {
	$rv = db()->query('SELECT section_id FROM forum_sections WHERE '.$parent1.' ORDER BY position');
	while($r = $rv->fetch_assoc()) {
		$rgt = db()->query('SELECT rgt FROM forum_sections WHERE '.$parent2)->fetch_assoc();
		$rgt = ($rgt ? $rgt['rgt'] : 1);
		db()->query('UPDATE forum_sections SET rgt=rgt+2 WHERE rgt>='.$rgt);
		db()->query('UPDATE forum_sections SET lft=lft+2 WHERE lft>'.$rgt);
		db()->query('UPDATE forum_sections SET lft='.$rgt.', rgt='.($rgt+1).' WHERE section_id='.$r['section_id']);
		shit_to_good('parent='.$r['section_id'], 'section_id='.$r['section_id']);
	}
}
shit_to_good('parent=0', 'name_de="root"');

die('OK');

?>
