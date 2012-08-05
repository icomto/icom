<?php

define('M_I_SET_IMAGES_PER_PAGE', 25);

trait m_i_set {
	public $set = NULL;
	public $set_chat;

	protected function TAB_set_CONSTRUCT() {
		$this->set_chat = $this->im_chat_add('set', [
			'INIT_function' => 'TAB_set_INIT',
			'table' => 'i_set_comments',
			'id_field' => 'comment_id',
			'subid_field' => 'set_id',
			'page_var' => 'scpage'
		]);
	}

	public function TAB_set_INIT(&$args) {
		$this->im_pages_get(@$args['page']);

		$this->set = new i__set($args['set']);
		if($this->set->ap->isBanned()) {
			throw new iexception('BANNED_FROM_AREA', $this);
		}
		if(!$this->set->ap->allowRead()) {
			throw new iexception('ACCESS_DENIED', $this);
		}

		$this->url = $this->set->getLink();

		$this->set_chat->url = $this->url;
		if($this->page > 1) $this->set_chat->url .= 'page/'.$this->page.'/';

		$this->set_chat->deny_post = !$this->set->ap->allowWrite();
		$this->set_chat->has_mod_rights = $this->set->ap->isAdmin();
		$this->set_chat->subid = $this->set->id;
		$this->set_chat->INIT($args);
	}

	protected function TAB_set_POST_chat_new(&$args) {
		return $this->set_chat->POST_chat_new($args);
	}
	protected function TAB_set_POST_chat_edit(&$args) {
		return $this->set_chat->POST_chat_edit($args);
	}
	protected function TAB_set_POST_chat_delete(&$args) {
		return $this->set_chat->POST_chat_delete($args);
	}
	protected function TAB_set_POST_chat_change_page(&$args) {
		return $this->set_chat->POST_chat_change_page($args);
	}

	protected function TAB_set_POST_edit(&$args) {
		if(!$this->set->ap->isMod()) throw new Exception('403');

		session::$s['m_i']['working_set'] = $this->set->id;

		if($args['name'] != $this->set->name) {
			$this->set->name = $args['name'];
		}
		if($args['content'] != $this->set->content) {
			$this->set->content = $args['content'];
		}
		if($args['tags']) {
			$this->set->removeAllTags();
			i__tag::insert_string($args['tags'], [[$this->set, [null]]]);
		}

		page_redir($this->set->getLink());
	}

	protected function TAB_set_POST_upload(&$args) {
		session::$s['m_i']['working_set'] = $this->set->id;

		$image = i__image::handleUpload($args);
		if(is_object($image)) {
			$this->set->addImage($image, $args['content']);
			page_redir($this->set->getLink());
		}
		$this->error = $image;
	}
	protected function TAB_set_POST_remove_image(&$args) {
		session::$s['m_i']['working_set'] = $this->set->id;
		$image = new i__image($args['image_id']);
		$this->set->removeImage($image);
		page_redir($this->set->getLink());
	}
	protected function TAB_set_POST_remove(&$args) {
		if(!$this->set->ap->isAdmin()) throw new iexception('403', $this);
		$this->set->remove();
		page_redir('/'.LANG.'/i/sets/');
	}

	protected function TAB_set_POST_add_tags(&$args) {
		if(!$this->set->ap->allowWrite()) throw new Exception('403');
		if($args['tags']) {
			session::$s['m_i']['working_set'] = $this->set->id;
			i__tag::insert_string($args['tags'], [[$this->set, [null]]]);
		}
		if(IS_AJAX) return $this->set->renderTags();
		else page_redir($this->url);
	}
	protected function TAB_set_POST_remove_tag(&$args) {
		if(!$this->set->ap->isMod()) throw new Exception('403');
		if($args['tag_id']) {
			session::$s['m_i']['working_set'] = $this->set->id;
			$tag = new i__tag($args['tag_id']);
			$this->set->removeTag($tag);
		}
		if(IS_AJAX) return $this->set->renderTags();
		else page_redir($this->url);
	}

	protected function TAB_set(&$args) {
		$this->way[] = [$this->set->name, $this->url, false];
		$this->im_pages_way(null, false);

		$this->images = db()->query("
			SELECT SQL_CALC_FOUND_ROWS b.*, a.content, a.user_id
			FROM i_set_images a
			JOIN i_images b USING (image_id)
			WHERE a.set_id='".$this->set->id."' AND ((".i__image::initAP()->query("b.image_id", "b.user_id").") & ".ACCESS_POLICY_READ.") = ".ACCESS_POLICY_READ."
			ORDER BY a.ctime DESC
			LIMIT ".(($this->page - 1)*M_I_SET_IMAGES_PER_PAGE).", ".M_I_SET_IMAGES_PER_PAGE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, M_I_SET_IMAGES_PER_PAGE);

		$this->im_way_title();
		return $this->ilphp_fetch('set.php.ilp');
	}
}

?>
