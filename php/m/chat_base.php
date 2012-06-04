<?php

class m_chat_base extends imodule {
	use ilphp_trait;
	use im_way;
	use im_pages;
	
	public $error = '';
	public $allow_post = true;
	public $reason = '';
	public $has_mod_rights = false;
	public $table = 'shoutbox';
	public $subid = 0;
	public $title = 'Shoutbox';
	public $html_id = 'Shoutbox';
	public $module_id = 'Module';
	
	public $update_menu = false; //(DISPLAY_COMMUNITY_ELEMENTS and session::$s['_ucs']['shoutbox']);
	public $update_module = NULL;
	
	public $limit_menu = 5;
	public $limit_module = 25;
	
	public $has_archive = true;
	public $default_text = NULL;
	public $module_head_data = '';
	public $chat_input_box = 'textarea';
	public $weight = 1;//weight for ajax update speed
	public $im_way_title = false;
	public $ubb_width = 622;
	
	public $type = '';
	
	protected $_init = false;
	
	protected function on_new_message(&$args) {
	}
	
	protected function INIT(&$args) {
		if($this->_init) return;
		$this->_init = true;
		
		$this->id = $this->table.$this->subid;
		if($this->default_text === NULL) $this->default_text = LS('Keine Support-Fragen in der Shoutbox posten!');
		
		$this->update_menu = (empty($this->idle[$this->subid]['menu']) ? false : true);
		$this->update_module = (empty($this->idle[$this->subid]['module']) ? false : true);
		
		if($this->update_menu or $this->update_module) set_fast_ajax_update($this->weight);
	}
	
	
	protected function POST_new(&$args) {
		if($args['message'] == $this->default_text) $args['message'] = '';
		if(!$args['message']) $this->error = LS('Du musst eine Nachricht eingeben.');
		elseif(!$this->allow_post) $this->error = LS('Du hast keine Berechtigung etwas zu schreiben.');
		else {
			db()->query("INSERT INTO ".$this->table." SET ".($this->subid === 0 ? "" : "subid='".es($this->subid)."', ")."user_id='".USER_ID."', message='".es($args['message'])."'");
			cache_L1::del('chat_base_'.$this->table.$this->subid.'_last_id');
			$this->on_new_message($args);
			#if($this->on_message_post_callback)
			#	G::$json_data['e'][$this->module_id] = call_user_func($this->on_message_post_callback, $this);
		}
		
		if(IS_AJAX) {
			$input_box = ($this->chat_input_box == 'textarea' ? 'textarea' : 'input[type=text]');
			if($this->update_menu) G::$json_data['e']['Menu'.$this->html_id.'Error'] = $this->error;
			if($this->update_module) G::$json_data['e']['Module'.$this->html_id.'Error'] = $this->error;
			#G::$json_data['s'][] = "$('.".$this->imodule_name."_form ".$input_box."').attr('value','".$this->default_text."');";
			G::$json_data['s'][] = "$('.".$this->imodule_name."_form button[type=submit]').html('".LS('Shout!')."');";
			G::$json_data['s'][] =  "m=$('.".$this->imodule_name."_form [type=submit]:focus').parent('.".$this->imodule_name."_form').find('".$input_box."');if(m.length)m.attr('value','').focus();";
			#if($this->update_menu or $this->update_module) G::$json_data['s'][] = "m=$('.".$this->imodule_name."_form button[type=submit]:focus').parent('.".$this->imodule_name."_form');if(m.length)m.find('".$input_box."').attr('value','').focus();";
		}
		#unset($args['new']);
		#unset($args['message']);
	}
	
	protected function POST_edit(&$args) {
		if(!$this->has_mod_rights) throw new iexception('403', $this);
		
		$content_id = (int)$args['content_id'];
		$bbinfos = db()->query("SELECT user_id FROM ".$this->table." WHERE id='".$content_id."'".($this->subid === 0 ? "" : " AND subid='".es($this->subid)."'")." LIMIT 1")->fetch_assoc();
		bigbrother($this->table.'_changed', array($content_id, @$bbinfos['user_id']));
		db()->query("UPDATE ".$this->table." SET message='".es($args['message'])."' WHERE id='".$content_id."'".($this->subid === 0 ? "" : " AND subid='".es($this->subid)."'")." LIMIT 1");
		cache_L1::del('chat_base_'.$this->table.$this->subid.'_last_id');
		if(IS_AJAX) {
			if($this->update_menu) G::$json_data['e']['IM_MENU_'.$this->imodule_name.($this->subid ? '_'.$this->subid : '')] = $this->MENU($args);
			if($this->update_module) G::$json_data['e']['Module'.$this->html_id] = $this->MODULE($args);
		}
	}
	
	protected function POST_delete(&$args) {
		if(!$this->has_mod_rights) throw new iexception('403', $this);
		
		$content_id = (int)$args['content_id'];
		$bbinfos = db()->query("SELECT user_id FROM ".$this->table." WHERE id='".$content_id."'".($this->subid === 0 ? "" : " AND subid='".es($this->subid)."'")." LIMIT 1")->fetch_assoc();
		bigbrother($this->table.'_deleted', array($content_id, @$bbinfos['user_id']));
		db()->query("DELETE FROM ".$this->table." WHERE id='".$content_id."'".($this->subid === 0 ? "" : " AND subid='".es($this->subid)."'")." LIMIT 1");
		cache_L1::del('chat_base_'.$this->table.$this->subid.'_last_id');
		if(IS_AJAX) {
			if($this->update_menu) G::$json_data['e']['IM_MENU_'.$this->imodule_name.($this->subid ? '_'.$this->subid : '')] = $this->MENU($args);
			if($this->update_menu) G::$json_data['e']['Module'.$this->html_id] = $this->MODULE($args);
		}
	}
	
	
	protected function IDLE(&$idle) {
		foreach($idle as $k=>$args) {
			$this->subid = $k;
			$this->_init = false;
			$this->INIT($args);
			$this->IDLE_RUN($args);
		}
	}
	
	protected function IDLE_RUN(&$args) {
		if(!isset($args['last_id']) or (!$this->update_menu and !$this->update_module))
			return;
		
		if(($last_id = cache_L1::get('chat_base_'.$this->table.$this->subid.'_last_id')) === false) {
			$last_id = db()->query("SELECT MAX(id) AS id FROM ".$this->table.($this->subid === 0 ? "" : " WHERE subid='".es($this->subid)."'"))->fetch_object()->id;
			cache_L1::set('chat_base_'.$this->table.$this->subid.'_last_id', 60, $last_id);
		}
		
		$this->imodule_args[$this->subid]['last_id'] = $last_id;
		if($this->update_menu) $this->imodule_args[$this->subid]['menu'] = 1;
		if($this->update_module) $this->imodule_args[$this->subid]['module'] = 1;
		
		if($last_id <= $args['last_id']) return;
		
		$query_id = es($args['last_id']);
		if(!$query_id or $query_id < $last_id - 20) $query_id = $last_id - 20;
		$shouts = db()->query("
			SELECT id, timeadded, user_id, message
			FROM ".$this->table."
			WHERE
				id>".$query_id."
				".($this->subid === 0 ? "" : " AND subid='".es($this->subid)."'")."
			ORDER BY id
			LIMIT 20");
		if($this->update_menu) {
			G::$json_data['s'][] = "$('#Menu".$this->html_id."Content .chat-row:gt(".($this->limit_menu - 1).")').remove();";
			G::$json_data['e']['Menu'.$this->html_id.'Top'] = array('fn' => 4, 'd' => array());
		}
		if($this->update_module) {
			G::$json_data['s'][] = "$('#Module".$this->html_id." .chat-row:gt(".($this->limit_module - 1).")').remove();";
			G::$json_data['e']['Module'.$this->html_id.'Top'] = array('fn' => 4, 'd' => array());
		}
		while($this->i = $shouts->fetch_assoc()) {
			if($this->update_menu) {
				$this->ubb_width = 152;
				G::$json_data['e']['Menu'.$this->html_id.'Top']['d'][] = $this->ilphp_fetch('chat_base.php.row.ilp');
			}
			if($this->update_module) {
				$this->ubb_width = 622;
				G::$json_data['e']['Module'.$this->html_id.'Top']['d'][] = $this->ilphp_fetch('chat_base.php.row.ilp');
			}
		}
	}
	
	protected function MENU(&$args) {
		$this->ubb_width = 152;
		$this->INIT($args);
		
		$this->shouts = db()->query("
			SELECT id, timeadded, user_id, message
			FROM ".$this->table."
			".($this->subid === 0 ? "" : "WHERE subid='".es($this->subid)."'")."
			ORDER BY id DESC
			LIMIT ".$this->limit_menu);
		$data = $this->ilphp_fetch('chat_base.php.menu.ilp');
		$this->imodule_args[$this->subid]['last_id'] = $this->last_id;
		$this->imodule_args[$this->subid]['menu'] = 1;
		return $data;
	}
	protected function MODULE(&$args) {
		$this->ubb_width = 622;
		
		$this->type = (($this->has_archive and isset($_GET['archive'])) ? '_archive' : '');
		$this->im_pages_get(@$_GET['page']);
		
		if($this->type == '_archive' and $this->has_archive) $this->way[] = array(LS('Archiv'), $this->url.'page/1/archive/');
		if($this->page > 1) $this->way[] = array(LS('Seite %1%', $this->page), $this->url.'page/'.$this->page.'/'.($this->type == '_archive' ? 'archive/' : ''));
		if($this->im_way_title) $this->im_way_title();
		
		$num_rows = cache_L1::get('chat_base-'.$this->table.$this->type.'-'.$this->subid);
		if($num_rows === false) {
			$num_rows = db()->query("SELECT COUNT(*) AS num FROM ".$this->table.$this->type."".($this->subid === 0 ? "" : " WHERE subid='".es($this->subid)."'"))->fetch_object()->num;
			cache_L1::set('chat_base-'.$this->table.$this->type.'-'.$this->subid, 20, $num_rows);
		}
		if(!$num_rows or $num_rows < ($this->page - 1)*$this->limit_module) $this->shouts = false;
		else
			$this->shouts = db()->query("
				SELECT id, timeadded, user_id, message
				FROM ".$this->table.$this->type.($this->subid === 0 ? "" : "
				WHERE subid='".es($this->subid)."'")."
				ORDER BY id DESC
				LIMIT ".(($this->page - 1)*$this->limit_module).", ".$this->limit_module);
		$this->num_pages = calculate_pages($num_rows, $this->limit_module);
		$data = $this->ilphp_fetch('chat_base.php.module.ilp');
		if($this->page == 1 and !$this->type) {
			$this->imodule_args[$this->subid]['last_id'] = $this->last_id;
			$this->imodule_args[$this->subid]['module'] = 1;
		}
		return $data;
	}
}

?>
