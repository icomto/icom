<?php

trait im_pages {
	public $page = 1;
	public $num_pages = 1;
	
	public function im_pages_get($page) {
		$this->page = ((int)$page > 1 ? (int)$page : 1);
	}
	public function im_pages_calc_sql($step) {
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, $step);
	}
	public function im_pages_way($url = NULL) {
		if($this->page > 1) {
			if($url) $url .= $this->page.'/';
			else $url = $this->url.'page/'.$this->page.'/';
			$this->way[] = array(LS('Seite %1%', $this->page), $url);
		}
	}
	
	public function im_pages_html($link_format = NULL, $url_format = NULL) {
		if(!$link_format) $link_format = '<a href="%s">%s</a>';
		if(!$url_format) $url_format = $this->url.'page/%s/';
		return create_pages($this->page, $this->num_pages - 1, $url_format, true, ' &nbsp;', '%02s', $link_format);
	}
	public function im_tab_pages_html($link_format = NULL) {
		if(!$link_format) {
			$tab = clone $this->im_tabs->active_tab;
			$tab->name = '%s';
			$tab->link = '%s';
			$link_format = $tab->html($this->im_tabs->group);
		}
		return $this->im_pages($link_format);
	}
}

?>
