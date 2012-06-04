<?php

trait im_way {
	public $way = array();
	
	public function im_way_title() {
		set_site_title_i($this->way, 0);
	}
	public function im_way_html() {
		$i = 0;
		$out = '';
		$num = count($this->way);
		foreach($this->way as $w) {
			$out .= ($w[1] ? '<a href="'.htmlspecialchars($w[1]).'">'.htmlspecialchars($w[0]).'</a>' : htmlspecialchars($w[0]));
			if(++$i < $num) $out .= ' &raquo; ';
		}
		return $out;
	}
}

?>
