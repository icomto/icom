<?php

define('M_I_SETS_PER_PAGE', 20);
define('M_I_IMAGES_PER_PAGE', 36);
define('M_I_COMMENTS_PER_PAGE', 25);
define('M_I_TAGS_PER_PAGE', 100);

require_once __DIR__.'/set.php';
require_once __DIR__.'/image.php';
require_once __DIR__.'/tag.php';

class m_i extends im_tabs {
	use ilphp_trait;
	use im_pages;
	use im_chat;

	use m_i_set;
	use m_i_image;
	use m_i_tag;

	public $error = NULL;

	public $allow_new_image = false;
	public $allow_new_sets = false;

	public function __construct() {
		parent::__construct(__DIR__);

		$this->url = '/'.LANG.'/i/';
		$this->way[] = [LS('Bilder (#2)'), $this->url];

		$this->im_tabs_add('sets', LS('Sets'), TAB_SELF);
		$this->im_tabs_add('set', LS('Set'), TAB_HIDDEN, '', 'sets');
		$this->im_tabs_add('images', LS('Bilder'), TAB_SELF);
		$this->im_tabs_add('image', LS('Bild'), TAB_HIDDEN, '', 'images');
		$this->im_tabs_add('comments', LS('Kommentare'), TAB_SELF);
		$this->im_tabs_add('tags', LS('Tags'), TAB_SELF);
		$this->im_tabs_add('tag', LS('Tag'), TAB_HIDDEN, '', 'tags');
	}

	protected function INIT(&$args) {
		$this->allow_new_image = IS_LOGGED_IN;
		$this->allow_new_sets = IS_LOGGED_IN;

		parent::INIT($args);
	}

	protected function IDLE(&$idle) {
		$this->set_chat->IDLE($idle, 'set');
		$this->image_chat->IDLE($idle, 'image');
	}

	protected function TAB_sets_POST_new(&$args) {
		if($args['name']) {
			$set = i__set::insert($args['name'], $args['content']);
			if($args['tags']) {
				i__tag::insert_string($args['tags'], [[$set, [null, true]]]);
			}
			session::$s['m_i']['working_set'] = $set->id;
			page_redir($set->getLink());
		}
	}

	protected function TAB_sets(&$args) {
		$this->im_pages_get(@$args['page']);
		$this->im_pages_way(null, false);

		$this->sets = db()->query("
			SELECT SQL_CALC_FOUND_ROWS a.*
			FROM i_sets a
			WHERE ((".i__set::initAP()->query(ACCESS_POLICY_READ, "a.set_id", "a.user_id").") & ".ACCESS_POLICY_READ.") = ".ACCESS_POLICY_READ."
			ORDER BY ctime DESC
			LIMIT ".(($this->page - 1)*M_I_SETS_PER_PAGE).", ".M_I_SETS_PER_PAGE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, M_I_SETS_PER_PAGE);

		$this->im_way_title();
		return $this->ilphp_fetch('i.php.sets.ilp');
	}

	protected function TAB_images_POST_upload(&$args) {
		$image = i__image::handleUpload($args);
		if(is_object($image)) {
			page_redir($image->getLink());
		}
		$this->error = $image;
	}

	protected function TAB_images(&$args) {
		$this->im_pages_get(@$args['page']);
		$this->im_pages_way(null, false);

		$this->images = db()->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM i_images
			WHERE ((".i__image::initAP()->query(ACCESS_POLICY_READ, "image_id").") & ".ACCESS_POLICY_READ.") = ".ACCESS_POLICY_READ."
			ORDER BY ctime DESC
			LIMIT ".(($this->page - 1)*M_I_IMAGES_PER_PAGE).", ".M_I_IMAGES_PER_PAGE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, M_I_IMAGES_PER_PAGE);

		$this->im_way_title();
		return $this->ilphp_fetch('i.php.images.ilp');
	}

	protected function TAB_comments(&$args) {
		$this->im_pages_get(@$args['page']);
		$this->im_pages_way(null, false);

		$this->comments = db()->query("
			SELECT SQL_CALC_FOUND_ROWS 'image' type, comment_id, image_id sub_id, ctime, user_id, message
			FROM i_image_comments
			WHERE ((".i__image::initAP()->query(ACCESS_POLICY_READ, "image_id").") & ".ACCESS_POLICY_READ.") = ".ACCESS_POLICY_READ."
			UNION
			SELECT 'set' type, comment_id, set_id sub_id, ctime, user_id, message
			FROM i_set_comments
			WHERE ((".i__set::initAP()->query(ACCESS_POLICY_READ, "set_id").") & ".ACCESS_POLICY_READ.") = ".ACCESS_POLICY_READ."
			ORDER BY ctime DESC
			LIMIT ".(($this->page - 1)*M_I_COMMENTS_PER_PAGE).", ".M_I_COMMENTS_PER_PAGE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, M_I_COMMENTS_PER_PAGE);

		$this->im_way_title();
		return $this->ilphp_fetch('i.php.comments.ilp');
	}

	protected function TAB_tags(&$args) {
		$this->im_pages_get(@$args['page']);
		$this->im_pages_way(null, false);

		$this->tags = db()->query("
			SELECT SQL_CALC_FOUND_ROWS a.*, COUNT(DISTINCT b.image_id) num_images, COUNT(DISTINCT c.set_id) num_sets
			FROM i_tags a
			LEFT JOIN i_image_tags b USING (tag_id)
			LEFT JOIN i_set_tags c USING (tag_id)
			WHERE
				((".i__image::initAP()->query(ACCESS_POLICY_READ, "b.image_id").") & ".ACCESS_POLICY_READ.") = ".ACCESS_POLICY_READ." AND
				((".i__set::initAP()->query(ACCESS_POLICY_READ, "c.set_id", "c.user_id").") & ".ACCESS_POLICY_READ.") = ".ACCESS_POLICY_READ."
			GROUP BY a.tag_id
			HAVING num_images+num_sets>0
			ORDER BY a.name
			LIMIT ".(($this->page - 1)*M_I_TAGS_PER_PAGE).", ".M_I_TAGS_PER_PAGE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, M_I_TAGS_PER_PAGE);

		$this->im_way_title();
		return $this->ilphp_fetch('i.php.tags.ilp');
	}
}

?>
