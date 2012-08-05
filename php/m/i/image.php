<?php

trait m_i_image {
	public $image;
	public $image_chat;

	protected function TAB_image_CONSTRUCT() {
		if(!isset(session::$s['m_i'])) session::$s['m_i'] = [];
		if(!isset(session::$s['m_i']['working_set'])) session::$s['m_i']['working_set'] = 0;
		
		$this->image_chat = $this->im_chat_add('image', [
			'INIT_function' => 'TAB_image_INIT',
			'table' => 'i_image_comments',
			'id_field' => 'comment_id',
			'subid_field' => 'image_id',
			'page_var' => 'icpage'
		]);

		$this->image_chat->places->inline = clone $this->image_chat->places->module;
		$this->image_chat->places->inline->limit = 10;
	}

	public function TAB_image_INIT(&$args) {
		$this->image = new i__image($args['image']);
		if($this->image->ap->isBanned()) {
			throw new iexception('BANNED_FROM_AREA', $this);
		}
		if(!$this->image->ap->allowRead()) {
			throw new iexception('ACCESS_DENIED', $this);
		}

		$this->url = $this->image->getLink();

		$this->image_chat->url = $this->url;

		$this->set_chat->deny_post = !$this->image->ap->allowWrite();
		$this->set_chat->has_mod_rights = $this->image->ap->isAdmin();
		$this->image_chat->subid = $this->image->id;
		$this->image_chat->INIT($args);
	}

	protected function TAB_image_POST_chat_new(&$args) {
		return $this->image_chat->POST_chat_new($args);
	}
	protected function TAB_image_POST_chat_edit(&$args) {
		return $this->image_chat->POST_chat_edit($args);
	}
	protected function TAB_image_POST_chat_delete(&$args) {
		return $this->image_chat->POST_chat_delete($args);
	}
	protected function TAB_image_POST_chat_change_page(&$args) {
		return $this->image_chat->POST_chat_change_page($args);
	}

	protected function TAB_image_POST_add_tags(&$args) {
		if(!$this->image->ap->allowWrite()) throw new Exception('403');
		if($args['tags']) {
			i__tag::insert_string($args['tags'], [[$this->image, [null]]]);
		}
		if(IS_AJAX) return $this->image->renderTags();
		else page_redir($this->url);
	}
	protected function TAB_image_POST_remove_tag(&$args) {
		if(!$this->image->ap->isMod()) throw new Exception('403');
		if($args['tag_id']) {
			$tag = new i__tag($args['tag_id']);
			$this->image->removeTag($tag);
		}
		if(IS_AJAX) return $this->image->renderTags();
		else page_redir($this->url);
	}

	protected function TAB_image_POST_display_comments(&$args) {
		if(!$this->image->ap->allowRead()) throw new Exception('403');
		if(IS_AJAX) return $this->image_chat->RENDER($args['place']);
		else page_redir($this->url);
	}

	protected function TAB_image_POST_associate_to_set(&$args) {
		if(!$this->image->ap->allowWrite()) throw new Exception('403');
		if($args['set_id']) {
			$set = new i__set($args['set_id']);
			if(!$set->ap->isMod()) throw new Exception('403');
			session::$s['m_i']['working_set'] = $set->id;
			$set->addImage($this->image, $args['content']);
		}
		page_redir($set->getLink());
	}

	protected function TAB_image(&$args) {
		$this->way[] = [$this->image->id, $this->url, false];

		if($this->image->ap->allowWrite()) {
			$this->current_working_set_id = session::$s['m_i']['working_set'];
		}

		$this->im_way_title();
		return $this->ilphp_fetch('image.php.ilp');
	}
}

?>
