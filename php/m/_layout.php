<?php

class m__layout extends imodule {
	use ilphp_trait;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function POST_change(&$args) {
		if(in_array($args['_layout'], array(1, 2)))
			session::$s['layout'] = $args['_layout'];
		page_redir(rebuild_location());
	}
	
	protected function MODULE(&$args) {
		return $this->ilphp_fetch('_theme.php.ilp');
	}
}

?>
