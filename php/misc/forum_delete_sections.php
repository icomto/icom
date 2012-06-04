<?php

require_once '../config.inc.php';
require_once '../functions.inc.php';

//$ignore_sections = array(172, 52, 231, 184, 46, 61);
//$copy_sections = array(7, 196, 9, 197, 185, 178);
// ??? 197, 185, 178

function forum_delete_section($parent, $parents = array(), $id_column = 'parent') {
	if(!$parents) $parents[] = $parent;
	
	$aa = db()->query("SELECT section_id FROM forum_sections WHERE $id_column=$parent ORDER BY section_id");
	while($a = $aa->fetch_assoc()) {
		$section_id = $a['section_id'];
		
		forum_delete_section($section_id, array_merge($parents, array($section_id)));
		
		echo implode(' -> ', $parents)." -> $section_id | del reported_posts = ";
		db()->query("DELETE a FROM forum_reported_posts a, forum_threads b, forum_posts c WHERE b.section_id=$section_id AND b.thread_id=c.thread_id AND a.post_id=c.post_id");
		echo db()->affected_rows."\n";
		
		echo implode(' -> ', $parents)." -> $section_id | del visited_guests = ";
		db()->query("DELETE a FROM forum_threads_visited_guests a, forum_threads b WHERE b.section_id=$section_id AND a.thread_id=b.thread_id");
		echo db()->affected_rows."\n";
		
		echo implode(' -> ', $parents)." -> $section_id | del visited_users = ";
		db()->query("DELETE a FROM forum_threads_visited_users a, forum_threads b WHERE b.section_id=$section_id AND a.thread_id=b.thread_id");
		echo db()->affected_rows."\n";
		
		echo implode(' -> ', $parents)." -> $section_id | del user_bookmarks = ";
		db()->query("DELETE a FROM user_bookmarks a, forum_threads b WHERE b.section_id=$section_id AND a.thing='thread' AND a.thing_id=b.thread_id");
		echo db()->affected_rows."\n";
		
		echo implode(' -> ', $parents)." -> $section_id | del posts = ";
		db()->query("DELETE a FROM forum_posts a, forum_threads b WHERE b.section_id=$section_id AND a.thread_id=b.thread_id");
		echo db()->affected_rows."\n";
		
		echo implode(' -> ', $parents)." -> $section_id | del threads = ";
		db()->query("DELETE FROM forum_threads WHERE section_id=$section_id");
		echo db()->affected_rows."\n";
		
		echo implode(' -> ', $parents)." -> $section_id | del section = ";
		db()->query("DELETE FROM forum_sections WHERE section_id=$section_id");
		echo db()->affected_rows."\n";
		
		if(file_exists('/tmp/update-stop')) {
			unlink('/tmp/update-stop');
			die("STOP\n");
		}
		
		echo "\n";
	}
}

forum_delete_section(52, array(), 'section_id');
forum_delete_section(61, array(), 'section_id');
forum_delete_section(172, array(), 'section_id');
forum_delete_section(174, array(), 'section_id');
forum_delete_section(231, array(), 'section_id');
forum_delete_section(232, array(), 'section_id');
forum_delete_section(243, array(), 'section_id');

?>
