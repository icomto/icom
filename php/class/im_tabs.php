<?php

define('TAB_DEFAULT', 1);
define('TAB_MODULE', 2);
define('TAB_SELF', 3);
define('TAB_BLANK', 4);

class im_tabs extends imodule {
	use im_way;
	
	protected $im_tabs = [];
	protected $im_tabs_var = NULL; //variable used for getting current tab
	protected $im_tabs_current = '';
	protected $im_tabs_template = 'default';
	
	protected $im_tabs_class = 'module-tabs';
	
	protected $im_tabs_sub = NULL;
	
	public function __construct($dir = 'templates') {
		parent::__construct($dir);
	}
	
	
	protected function INIT(&$args) {
		if($this->im_tabs_var === NULL) $this->im_tabs_var = $this->imodule_name;
		if(!empty($args[$this->im_tabs_var]) and isset($this->im_tabs[$args[$this->im_tabs_var]]))
			$this->im_tabs_current = $args[$this->im_tabs_var];
		$this->url .= $this->im_tabs_current.'/';
		
		if($this->im_tabs_current) {
			$fn = 'tab_'.$this->im_tabs_current.'_INIT';
			if(method_exists($this, $fn)) $this->$fn($args);
		}
	}
	
	protected function MODULE(&$args) {
		$fn = 'im_tab_template_'.$this->im_tabs_template;
		return $this->$fn($args);
		
	}
	protected function POST(&$args) {
		switch($args['action']) {
		default:
			if(!$args['action']) return;
			$fn = 'tab_'.$this->im_tabs_current.'_POST';
			if(method_exists($this, $fn)) return $this->$fn($args);
			$fn .= '_'.preg_replace('~[^a-z0-9_]~i', '', $args['action']);
			if(!method_exists($this, $fn)) throw new imodule_exception('NOT_IMPLEMENTED ('.$this->imodule_name.' -> '.$fn.')');
			return $this->$fn($args);
		
		case 'TAB_SELECT':
			if(!$this->im_tabs_current or !isset($this->im_tabs[$this->im_tabs_current]))
				throw new iexception('ACCESS_DENIED', $this);
			if(empty($_POST['html5_history'])) page_redir($this->url);
			return $this->im_tabs_html_content($args);
		}
	}
	
	protected function im_tabs_add($id, $name, $type = TAB_DEFAULT, $link = '') {
		if($type === false) return;
		if(!$this->im_tabs_current) $this->im_tabs_current = $id;
		$this->im_tabs[$id] = [
			'id' => $id,
			'name' => $name,
			'type' => $type,
			'link' => $link ? $link : $this->url.$id.'/'];
	}
	
	
	protected function im_tabs_init_sub(&$args, $module) {
		$name = implode('__', $module);
		
		if(iengine::$engine and iengine::$engine->imodule_name == $name)
			$this->im_tabs_sub =& iengine::$engine;
		if(iengine::$get and iengine::$get->imodule_name == $name)
			$this->im_tabs_sub =& iengine::$get;
		if(iengine::$post and iengine::$post->imodule_name == $name)
			$this->im_tabs_sub =& iengine::$post;
		
		if($this->im_tabs_sub) {
			foreach($args as $k=>$v)
				if(!isset($this->im_tabs_sub->args[$k]))
					$this->im_tabs_sub->args[$k] = $v;
			$this->im_tabs_sub->args['parent'] = $this;
		}
		else {
			$new = $args;
			$new['parent'] = $this;
			$this->im_tabs_sub = iengine::GET($module, $new);
			$this->im_tabs_sub->RUN('INIT');
		}
	}
	
	
	public function im_tabs_html(&$args) {
		if(!$this->im_tabs_current or !isset($this->im_tabs[$this->im_tabs_current]))
			throw new iexception('ACCESS_DENIED', $this);
		
		if(count($this->im_tabs) <= 1)
			return $this->im_tabs_html_content($args);
		
		$content = '<div class="'.$this->im_tabs_class.'"><div class="tab-content">';
		foreach($this->im_tabs as $tab) {
			$content .= '<div id="'.$this->im_tabs_var.'__'.$tab['id'].'__tab" class="tab '.($tab['id'] == $this->im_tabs_current ? 'active' : 'normal').'"><h2>';
			switch($tab['type']) {
			default:
			case TAB_DEFAULT:
				$content .= '<a href="'.htmlspecialchars($tab['link']).'" target="_self" data-tab="'.$this->im_tabs_var.'" data-tab-name="'.$tab['id'].'">'.htmlspecialchars($tab['name']).'</a>';
				break;
			case TAB_MODULE:
				$content .= '<a href="'.htmlspecialchars($tab['link']).'">'.htmlspecialchars($tab['name']).'</a>';
				break;
			case TAB_SELF:
				$content .= '<a href="'.htmlspecialchars($tab['link']).'" name="'.$this->IMODULE_POST_VAR('action').'" value="TAB_SELECT" onclick="return im_tabs_switch(this, \''.$this->im_tabs_var.'\');">'.htmlspecialchars($tab['name']).'</a>';
				break;
			case TAB_BLANK:
				$content .= '<a href="'.htmlspecialchars($tab['link']).'" target="_blank">'.htmlspecialchars($tab['name']).'</a>';
				break;
			}
			$content .= '</h2></div>';
		}
		$content .= '</div></div>';
		$content .= '<div style="clear:both;"></div>';
		
		return $content.$this->im_tabs_html_content($args);
	}
	private function im_tabs_html_content(&$args) {
		$fn = 'TAB_'.$this->im_tabs_current;
		if(!method_exists($this, $fn)) throw new imodule_exception('NOT_IMPLEMENTED ('.$this->imodule_name.' -> '.$fn.')');
		$content = '<div id="'.$this->im_tabs_var.'__content">';
		$content .= $this->$fn($args);
		$content .= '</div>';
		return $content;
	}
	
	protected function im_tab_template_default(&$args, $class = 'module-content') {
		return 
			'<div class="module-item">'.
				'<h1>'.$this->im_way_html().'</h1>'.
				'<div class="'.htmlspecialchars($class).'">'.$this->im_tabs_html($args).'</div><div class="module-footer"></div>'.
			'</div>';
	}
	protected function im_tab_template_sub(&$args, $class = 'module-sub-content') {
		return
			(count($this->im_tabs) <= 1 ? '' : '<div class="SEP"><div><div></div><h3>'.$this->im_way_html().'</h3><div></div></div></div>').
			'<div class="'.htmlspecialchars($class).'">'.$this->im_tabs_html($args).'</div>';
	}
}

?>
