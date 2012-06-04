<?php

class m_admin_groups extends imodule {
	use ilphp_trait;
	
	public $display = 0;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$init) {
		if(!has_privilege('groupmanager')) throw new iexception('ACCESS_DENIED', $this);
		$this->url = '/'.LANG.'/admin/groups/';
	}
	
	protected function POST(&$args) {
		switch(@$args['action']) {
		case 'delete':
			$group_id = (int)$args['delete'];
			db()->query("DELETE FROM groups WHERE id='".$group_id."' LIMIT 1");
			return IS_AJAX ?
				LS('Die Gruppe mit der ID %1% wurde gel&ouml;scht', $group_id) :
				page_redir($this->url);
		case 'save':
			$group_id = (int)$args['id'];
			$query = array();
			$query[] = "name_de=".NULLval($args['name_de']);
			$query[] = "name_en=".NULLval($args['name_en']);
			$query[] = "public='".(empty($args['public']) ? 0 : 1)."'";
			foreach(G::$DEFAULT_PRIVILEGES as $k=>$trash) $query[] = "$k='".(empty($args['priv'][$k]) ? 0 : 1)."'";
			db()->query("UPDATE groups SET ".implode(", ", $query)." WHERE id='".$group_id."' LIMIT 1");
			$this->display = $group_id;
			return IS_AJAX ?
				$this->row(db()->query("SELECT * FROM groups WHERE id='".$group_id."' LIMIT 1")->fetch_assoc()) :
				page_redir($this->url);
		case 'new':
			db()->query("INSERT IGNORE INTO groups SET name_de='".es($args['name_de'])."', name_en='".es($args['name_en'])."'");
			$this->display = db()->insert_id;
			return IS_AJAX ?
				$this->RUN('MODULE') :
				page_redir($this->url);
		}
	}
	
	protected function MODULE(&$args) {
		set_site_title(array(LS('Admin'), LS('Gruppen')));
		$this->groups = db()->query("SELECT * FROM groups ORDER BY ".LQ('name_LL'));
		return $this->ilphp_fetch('groups.php.ilp');
	}

	public function row($i) {
		$this->i =& $i;
		$this->DEFAULT_PRIVILEGES =& G::$DEFAULT_PRIVILEGES;
		return $this->ilphp_fetch('groups.php.ilp|row');
	}

	protected function IDLE(&$args) {
		$this->display = true;
		foreach($args as $group_id=>$v) {
		}
	}
}

?>
