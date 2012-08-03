<?php

define('USER_BOOKMARKS_I_SETS_PER_PAGE', 20);
define('USER_BOOKMARKS_I_IMAGES_PER_PAGE', 18);

class m_users_bookmarks extends im_tabs {
	use ilphp_trait;
	use im_pages;

	protected $im_tabs_var = 'upbm';
	protected $im_tabs_template = 'sub';

	public $m_user = NULL;
	public $user = NULL;

	public function __construct() {
		parent::__construct(__DIR__);
	}

	protected function INIT(&$args) {
		$this->m_user = $args['parent'];
		unset($this->args['parent']);

		$this->user =& $this->m_user->user;

		$this->url = $this->m_user->url.'upbm/';
		$this->way[] = [LS('Lesezeichen'), $this->url];

		$this->im_tabs_add('threads', LS('Forum'), TAB_SELF);
		$this->im_tabs_add('news', LS('News'), TAB_SELF);
		$this->im_tabs_add('wiki', LS('Wiki'), TAB_SELF);
		$this->im_tabs_add('images', LS('Bilder'), TAB_SELF);
		$this->im_tabs_add('sets', LS('Bilder Sets'), TAB_SELF);

		parent::INIT($args);
	}

	protected function TAB_threads(&$args) {
		$this->threads = db()->query("
			SELECT b.thread_id, c.name
			FROM user_bookmarks a, forum_threads b, forum_posts c
			WHERE
				a.user_id='".$this->user['user_id']."' AND
				a.thing='thread' AND
				a.thing_id=b.thread_id AND
				b.firstpost=c.post_id
			ORDER BY c.name");
		return $this->ilphp_fetch('bookmarks.php.threads.ilp');
	}
	protected function TAB_news(&$args) {
		$this->news = db()->query("
			SELECT b.news_id, b.name
			FROM user_bookmarks a, news b
			WHERE
				a.user_id='".$this->user['user_id']."' AND
				a.thing='news' AND
				a.thing_id=b.news_id AND
				b.status='public'
			ORDER BY b.lastupdate");
		return $this->ilphp_fetch('bookmarks.php.news.ilp');
	}
	protected function TAB_wiki(&$args) {
		$this->wiki = db()->query("
			SELECT b.id, b.name, b.lang
			FROM user_bookmarks a, wiki_pages b
			WHERE
				a.user_id='".$this->user['user_id']."' AND
				a.thing='wiki' AND
				a.thing_id=b.id AND
				b.deleted=0 AND
				b.history!=0
			ORDER BY b.name");
		return $this->ilphp_fetch('bookmarks.php.wiki.ilp');
	}
	protected function TAB_images(&$args) {
		$this->im_pages_get(@$args['page']);
		$this->im_pages_way(null, false);
		
		$this->images = db()->query("
			SELECT SQL_CALC_FOUND_ROWS b.*
			FROM user_bookmarks a, i_images b
			WHERE
				a.user_id='".$this->user['user_id']."' AND
				a.thing='i_image' AND
				a.thing_id=b.image_id
			ORDER BY a.timeadded DESC
			LIMIT ".(($this->page - 1)*USER_BOOKMARKS_I_IMAGES_PER_PAGE).", ".USER_BOOKMARKS_I_IMAGES_PER_PAGE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, USER_BOOKMARKS_I_IMAGES_PER_PAGE);
		
		$this->im_way_title();
		return $this->ilphp_fetch('bookmarks.php.images.ilp');
	}
	protected function TAB_sets(&$args) {
		$this->im_pages_get(@$args['page']);
		$this->im_pages_way(null, false);
		
		$this->sets = db()->query("
			SELECT b.*
			FROM user_bookmarks a, i_sets b
			WHERE
				a.user_id='".$this->user['user_id']."' AND
				a.thing='i_set' AND
				a.thing_id=b.set_id
			ORDER BY a.timeadded DESC
			LIMIT ".(($this->page - 1)*USER_BOOKMARKS_I_IMAGES_PER_PAGE).", ".USER_BOOKMARKS_I_IMAGES_PER_PAGE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, USER_BOOKMARKS_I_IMAGES_PER_PAGE);
		
		$this->im_way_title();
		return $this->ilphp_fetch('bookmarks.php.sets.ilp');
	}
}

?>
