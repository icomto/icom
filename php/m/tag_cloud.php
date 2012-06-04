<?php

class m_tag_cloud extends imodule {
	use ilphp_trait;
	use im_way;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	protected function MODULE(&$args) {
		$this->url = '/'.LANG.'/tag_cloud/';
		$this->way[] = [LS('Tag-Cloud'), $this->url];
		$this->im_way_title();
		$this->ilphp_init('tag_cloud.php.ilp', 6*60*60);
		if(($data = $this->ilphp_cache_load()) !== false) return $data;;
		$this->max = db()->query("SELECT MAX(num) AS max FROM searches WHERE NOT str=''")->fetch_object()->max;
		$aa = db()->query("SELECT str, num FROM searches WHERE NOT str='' ORDER BY num DESC LIMIT 500");
		$this->rows = array();
		while($a = $aa->fetch_assoc()) $this->rows[] = $a;
		shuffle($this->rows);
		return $this->ilphp_fetch();
	}
}


?>
