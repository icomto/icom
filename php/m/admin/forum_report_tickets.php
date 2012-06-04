<?php

class m_admin_forum_report_tickets extends imodule {
	use ilphp_trait;
	use im_way;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$init) {
		if(!has_privilege('forum_admin') and !has_privilege('forum_mod') and !has_privilege('forum_super_mod'))
			throw new iexception('ACCESS_DENIED', $this);
	}
	
	protected function MODULE(&$args) {
		$this->way[] = array(LS('Admin'), '');
		$this->way[] = array(LS('Forum'), '/admin/forum/');
		$this->way[] = array(LS('Gemeldete Threads'), '/admin/forum_report_tickets/');
		
		$lang_query = get_lang_query();
		$this->is_multilang = ($lang_query ? false : true);
		$this->posts = db()->query("
			SELECT
				forum_posts.post_id AS id,
				forum_posts.name AS name,
				forum_posts.thread_id AS thread,
				forum_threads.lang AS lang
			FROM forum_reported_posts AS reported
			JOIN forum_posts ON forum_posts.post_id=reported.post_id
			JOIN forum_threads ON forum_threads.thread_id=forum_posts.thread_id
			WHERE reported.open=1".($lang_query ? " AND forum_threads.lang='$lang_query'" : "")."
			ORDER BY reported.id DESC");
		
		$this->im_way_title();
		return $this->ilphp_fetch('forum_report_tickets.php.ilp');
	}

	public function post_num($thread, $post) {
		return db()->query("
			SELECT COUNT(*) AS num
			FROM forum_posts
			WHERE thread_id='".es($thread)."' AND post_id<='".es($post)."'")->fetch_object()->num;
	}
}

?>
