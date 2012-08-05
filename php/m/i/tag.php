<?php

define('M_I_TAG_ENTRIES_PER_PAGE', 21);

trait m_i_tag {
	public $tag = NULL;

	public function TAB_tag_INIT(&$args) {
		$this->im_pages_get(@$args['page']);

		$this->tag = new i__tag($args['tag']);

		$this->url = $this->tag->getLink();
	}

	protected function TAB_tag_POST_add_aliases(&$args) {
		if(has_privilege('forum_admin')) throw new iexception('403', $this);
		$tags = i__tag::insert_string($args['name']);
		foreach($tags as $tag) {
			$this->tag->addAlias($tag);
		}
		if(IS_AJAX) return $this->ilphp_fetch('tag.php.ilp|aliases');
		else page_redir($this->url);
	}
	protected function TAB_tag_POST_remove_alias(&$args) {
		if(has_privilege('forum_admin')) throw new iexception('403', $this);
		$tag = new i__tag($args['tag_id']);
		$tag->removeAlias();
		if(IS_AJAX) return $this->ilphp_fetch('tag.php.ilp|aliases');
		else page_redir($this->url);
	}

	protected function TAB_tag_POST_add_parents(&$args) {
		if(true) throw new iexception('403', $this);
		$tags = i__tag::insert_string($args['name']);
		foreach($tags as $tag) {
			$tag->addChild($this->tag);
		}
		if(IS_AJAX) return $this->ilphp_fetch('tag.php.ilp|parents');
		else page_redir($this->url);
	}
	protected function TAB_tag_POST_remove_parent(&$args) {
		if(true) throw new iexception('403', $this);
		$tag = new i__tag($args['tag_id']);
		$tag->removeChild($this->tag);
		if(IS_AJAX) return $this->ilphp_fetch('tag.php.ilp|parents');
		else page_redir($this->url);
	}

	protected function TAB_tag_POST_add_childs(&$args) {
		if(true) throw new iexception('403', $this);
		$tags = i__tag::insert_string($args['name']);
		foreach($tags as $tag) {
			$this->tag->addChild($tag);
		}
		if(IS_AJAX) return $this->ilphp_fetch('tag.php.ilp|childs');
		else page_redir($this->url);
	}
	protected function TAB_tag_POST_remove_child(&$args) {
		if(true) throw new iexception('403', $this);
		$tag = new i__tag($args['tag_id']);
		$this->tag->removeChild($tag);
		if(IS_AJAX) return $this->ilphp_fetch('tag.php.ilp|childs');
		else page_redir($this->url);
	}

	protected function TAB_tag_POST_rename(&$args) {
		if(has_privilege('forum_admin')) throw new iexception('403', $this);
		if($this->tag->rename($args['name']))
			page_redir($this->tag->getLink());
	}
	protected function TAB_tag_POST_merge(&$args) {
		if(has_privilege('forum_admin')) throw new iexception('403', $this);
		$tag = new i__tag($args['name']);
		$this->tag->mergeWith($tag);
		page_redir($tag->getLink());
	}

	protected function TAB_tag(&$args) {
		$this->way[] = [$this->tag->name, $this->url, false];
		$this->im_pages_way(null, false);

		$this->sets = db()->query("
			SELECT SQL_CALC_FOUND_ROWS c.*
			FROM i_set_tags a
			JOIN i_tags b USING (tag_id)
			JOIN i_sets c USING (set_id)
			WHERE b.alias_id='".$this->tag->alias_id."'
			ORDER BY c.ctime DESC
			LIMIT ".(($this->page - 1)*M_I_TAG_ENTRIES_PER_PAGE).", ".M_I_TAG_ENTRIES_PER_PAGE);
		$num_rows = db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num;
		
		$start = ($this->page - 1)*M_I_TAG_ENTRIES_PER_PAGE;
		$limit = M_I_TAG_ENTRIES_PER_PAGE;
		$start -= $num_rows;
		if($start < 0) $start = 0;
		$limit -= $this->sets->num_rows;
		if($limit < 1) {
			$limit = 1;
			$this->dummy_image = true;
		}
		else {
			$this->dummy_image = false;
		}
		/*$this->images = db()->query("
			SELECT SQL_CALC_FOUND_ROWS d.*
			FROM (
				SELECT aa.tag_id
				FROM i_tags aa
				WHERE aa.alias_id='".$this->tag->alias_id."'
				UNION
				SELECT ac.child_id tag_id
				FROM i_tags ab
				JOIN i_tag_childs ac USING (alias_id)
				WHERE ab.alias_id='".$this->tag->alias_id."'
			) a
			JOIN i_image_tags c USING (tag_id)
			JOIN i_images d USING (image_id)
			GROUP BY c.image_id
			ORDER BY d.ctime DESC
			LIMIT $start, $limit");*/
		$this->images = db()->query("
			SELECT SQL_CALC_FOUND_ROWS c.*
			FROM i_image_tags a
			JOIN i_tags b USING (tag_id)
			JOIN i_images c USING (image_id)
			WHERE b.alias_id='".$this->tag->alias_id."'
			ORDER BY c.ctime DESC
			LIMIT $start, $limit");
		$num_rows += db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num;

		$this->num_pages = calculate_pages($num_rows, M_I_TAG_ENTRIES_PER_PAGE);

		$this->im_way_title();
		return $this->ilphp_fetch('tag.php.ilp');
	}
}

?>
