<?php

define('M_I_TAG_ENTRIES_PER_PAGE', 21);

trait m_i_tag {
	public $tag = NULL;

	public function TAB_tag_INIT(&$args) {
		$this->im_pages_get(@$args['page']);

		$this->tag = new i__tag($args['tag']);

		$this->url = $this->tag->getLink();
	}

	protected function TAB_tag(&$args) {
		$this->way[] = [$this->tag->name, $this->url, false];
		$this->im_pages_way(null, false);

		$this->sets = db()->query("
			SELECT SQL_CALC_FOUND_ROWS b.*
			FROM i_set_tags a
			JOIN i_sets b USING (set_id)
			WHERE a.tag_id='".$this->tag->id."'
			ORDER BY b.ctime DESC
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
		$this->images = db()->query("
			SELECT SQL_CALC_FOUND_ROWS b.*
			FROM i_image_tags a
			JOIN i_images b USING (image_id)
			WHERE a.tag_id='".$this->tag->id."'
			ORDER BY b.ctime DESC
			LIMIT $start, $limit");
		echo "LIMIT $start, $limit";
		$num_rows += db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num;

		$this->num_pages = calculate_pages($num_rows, M_I_TAG_ENTRIES_PER_PAGE);

		$this->im_way_title();
		return $this->ilphp_fetch('tag.php.ilp');
	}
}

?>
