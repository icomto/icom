<?php

class m_forum_global {
	public static function get_group_querys() {
		return array(
			"MATCH (forum_sections.read_groups) AGAINST ('".implode(' ', user()->groups)."' IN BOOLEAN MODE)",
			"MATCH (forum_sections.write_groups) AGAINST ('".implode(' ', user()->groups)."' IN BOOLEAN MODE)");
	}

	public static function get_read_section_ids($read_groups, $namespace = '') {
		$sections = array();
		$temp = db()->query("SELECT section_id FROM forum_sections WHERE ".($namespace ? "namespace='$namespace' AND " : '')."$read_groups");
		while($s = $temp->fetch_assoc()) $sections[] = $s['section_id'];
		$sections = implode_arr_list($sections);
		return $sections ? $sections : '0';
	}

	public static function get_is_mod_query() {
		if(!IS_LOGGED_IN or !has_privilege('forum_mod')) return '0';
		elseif(has_privilege('forum_super_mod')) return '1';
		else return "(MATCH (forum_sections.mods) AGAINST (".USER_ID." IN BOOLEAN MODE) OR MATCH (forum_sections.mod_groups) AGAINST ('".implode(' ', user()->groups)."' IN BOOLEAN MODE))";
	}
	
	public static function fix_stats($section_id) {
		$a = db()->query("
			SELECT
				COUNT(DISTINCT a.thread_id) num_threads,
				COUNT(DISTINCT IF(a.lang_de, a.thread_id, NULL)) num_threads_de,
				COUNT(DISTINCT IF(a.lang_en, a.thread_id, NULL)) num_threads_en,
				COUNT(b.post_id) num_posts,
				COUNT(IF(a.lang_de, b.post_id, NULL)) num_posts_de,
				COUNT(IF(a.lang_en, b.post_id, NULL)) num_posts_en
			FROM forum_threads a
			LEFT JOIN forum_posts b USING (thread_id)
			WHERE section_id='".(int)$section_id."'
			GROUP BY a.section_id")->fetch_assoc();
	db()->query("
		UPDATE forum_sections
		SET
			num_threads='".$a['num_threads']."',
			num_threads_de='".$a['num_threads_de']."',
			num_threads_en='".$a['num_threads_en']."',
			num_posts='".$a['num_posts']."',
			num_posts_de='".$a['num_posts_de']."',
			num_posts_en='".$a['num_posts_en']."'
		WHERE section_id='".(int)$section_id."'
		LIMIT 1");
	}
}

?>
