<?php

define('TAB_DEFAULT', 1);
define('TAB_MODULE', 2);
define('TAB_SELF', 3);
define('TAB_BLANK', 4);

class il_tab {
	public $id;
	public $name;
	public $type;
	public $link;
	public function __construct($id, $name, $type, $link) {
		$this->id = $id;
		$this->name = $name;
		$this->type = $type;
		$this->link = $link;
	}
	public function html($group) {
		switch($this->type) {
		default:
		case TAB_DEFAULT:
			return '<a href="'.htmlspecialchars($this->link).'" target="_self" data-tab="'.$group.'" data-tab-name="'.$this->id.'">'.htmlspecialchars($this->name).'</a>';
		case TAB_MODULE:
			return '<a href="'.htmlspecialchars($this->link).'">'.htmlspecialchars($this->name).'</a>';
		case TAB_SELF:
			return '<a href="'.htmlspecialchars($this->link).'" target="_self" onclick="$(\'#'.$group.'Content\').html($(\'#AjaxElementLoader2\').html());">'.htmlspecialchars($this->name).'</a>';
		case TAB_BLANK:
			return '<a href="'.htmlspecialchars($this->link).'" target="_blank">{~$v.0}</a>';
		}
	}
}

class il_tabs {
	public $callback;
	public $group;
	public $class;
	public $tabs = array();
	public $active_tab = NULL;
	public function __construct($callback, $group, $class = 'module-tabs') {
		$this->callback = $callback;
		$this->group = $group;
		$this->class = $class;
	}
	public function add($id, $name, $type = TAB_DEFAULT, $link = '', $baselink = '') {
		if($link) $link = str_replace('%%', $this->group.'/'.urlencode($id), $link);
		else $link = $baselink.$this->group.'/'.urlencode($id).'/';
		$this->tabs[] = new il_tab($id, $name, $type, $link);
	}
	public function create() {
		$content = $this->call(isset($_GET[$this->group]) ? $_GET[$this->group] : '');
		if(count($this->tabs) == 1) $tabs = '';
		else {
			$num = 0;
			foreach($this->tabs as $tab) if($tab->type) $num++;
			if($num == 1) $tabs = '';
			else {
				$tpl = new ilphp('il_tabs.ilp');
				$tpl->group =& $this->group;
				$tpl->class =& $this->class;
				$tpl->tabs =& $this->tabs;
				$tpl->active_tab =& $this->active_tab;
				$tabs = $tpl->ilphp_fetch();
			}
		}
		return $tabs.'<div id="'.$this->group.'Content">'.$content.'</div>';
	}
	public function call($id) {
		if(!$this->tabs) throw new iexception('NO_TABS_DEFINED', $this);
		foreach($this->tabs as $tab) {
			if($tab->id == $id) {
				if(!$tab->type) throw new iexception('ACCESS_DENIED', $this);
				$this->active_tab =& $tab;
				break;
			}
			elseif(!$this->active_tab and $tab->type)
				$this->active_tab = $tab;
		}
		if(!$this->active_tab) throw new iexception('ACCESS_DENIED', $this);
		list($a, $b) = $this->callback;
		return $a->$b($this->active_tab->id);
	}
}

?>
