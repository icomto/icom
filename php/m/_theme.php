<?php

class m__theme extends imodule {
	use ilphp_trait;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function POST_change(&$args) {
		if(!defined('THEME_STYLE_DIRECTORY')) theme::init($this);
		if(theme::change($args[$this->imodule_name], @$args['_preset'], @$args['_userset'])) {
			if(session::$s['theme_preset']) session::$s['theme_preset'] = DEFAULT_THEME_PRESET;
			unset(session::$s['theme_userset']);
		}
		page_redir(rebuild_location());
	}
	
	protected function MODULE(&$args) {
		return $this->ilphp_fetch('_theme.php.ilp');
	}
	
	private function change(&$args) {
	}
}

?>
