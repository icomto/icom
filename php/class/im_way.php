<?php

trait im_way {
	public $way = array();

	public function im_way_title() {
		set_site_title_i($this->way, 0);
	}
	public function im_way_html() {
		$out = [];
		foreach($this->way as $w) {
			if(!isset($w[2]) or $w[2] == true) {
				$out[] = ($w[1] ? '<a href="'.htmlspecialchars($w[1]).'">'.htmlspecialchars($w[0]).'</a>' : htmlspecialchars($w[0]));
			}
		}
		return implode(' &raquo; ', $out);
	}
}

?>
