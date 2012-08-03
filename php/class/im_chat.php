<?php

class im_chat_settings extends ArrayClass {
	private static $defaults = [
		//this is only needed for IDLE. you can wrap an try block or someting around init it...
		//when this function returns 'IGNORE' IDLE will skip
		'INIT_function' => 'INIT',

		'deny_post' => false, //false, true or reason (string)
		'has_mod_rights' => false,

		'name' => 'im_chat',

		//sql settings
		'table' => 'chat',
		'id_field' => 'id',
		'subid_field' => 'subid',
		'subid' => 0,
		'time_field' => 'ctime',
		'order_by_field' => null, //defaults to id_field [D] ([D] = placeholder for direction)

		//page settings
		'page_var' => 'page',

		//place settings
		'places' => [
			'menu' => [
				'limit' => 5,
				'pages' => false,
				'ubb_width' => 152,
				'input_box' => 'textarea'
			],
			'module' => [
				'limit' => 25,
				'pages' => true,
				'ubb_width' => 622,
				'input_box' => 'textarea-ubb'
			]
		],

		'html_id' => '',
		'error' => '',

		'default_text' => false,
		'weight' => 1,//chat_weight for ajax update speed

		'place' => false,
		'update' => false
	];


	public function on_chat_new(&$args, $message) {
		db()->query("INSERT INTO ".$this->table." SET ".($this->subid === 0 ? "" : $this->subid_field."='".es($this->subid)."', ")."user_id='".USER_ID."', message='".es($message)."'");
		return db()->insert_id;
	}
	public function on_chat_edit(&$args, $post_id, $message) {
		db()->query("UPDATE ".$this->table." SET message='".es($message)."' WHERE ".$this->id_field."='".$post_id."'".($this->subid === 0 ? "" : " AND ".$this->subid_field."='".es($this->subid)."'")." LIMIT 1");
	}
	public function on_chat_query_single(&$args, $post_id) {
		return db()->query("
			SELECT *, ".$this->id_field." id, ".$this->time_field." ctime, user_id, message
			FROM ".$this->table."
			WHERE ".$this->id_field."='".$post_id."'".($this->subid === 0 ? "" : " AND ".$this->subid_field."='".es($this->subid)."'")."
			LIMIT 1")->fetch_assoc();
	}
	public function on_chat_delete(&$args, $post_id) {
		db()->query("DELETE FROM ".$this->table." WHERE ".$this->id_field."='".$post_id."'".($this->subid === 0 ? "" : " AND ".$this->subid_field."='".es($this->subid)."'")." LIMIT 1");
	}
	public function on_chat_query_last_id(&$args) {
		return db()->query("SELECT MAX(".$this->id_field.") id FROM ".$this->table.($this->subid === 0 ? "" : " WHERE ".$this->subid_field."='".es($this->subid)."'"))->fetch_object()->id;
	}
	public function on_chat_query_idle(&$args, &$query_id, &$limit) {
		return db()->query("
			SELECT ".$this->id_field." id, ".$this->time_field." ctime, user_id, message
			FROM ".$this->table."
			WHERE
				".$this->id_field.">".$query_id."
				".($this->subid === 0 ? "" : " AND ".$this->subid_field."='".es($this->subid)."'")."
			ORDER BY ".str_replace('[D]', 'ASC', $this->order_by_field)."
			LIMIT ".$limit);
	}
	public function on_chat_query_num_rows(&$args) {
		return db()->query("SELECT COUNT(*) AS num FROM ".$this->table."".($this->subid === 0 ? "" : " WHERE ".$this->subid_field."='".es($this->subid)."'"))->fetch_object()->num;
	}
	public function on_chat_query_posts(&$args, $start, $limit) {
		return db()->query("
			SELECT ".$this->id_field." id, ".$this->time_field." ctime, user_id, message
			FROM ".$this->table.($this->subid === 0 ? "" : "
			WHERE ".$this->subid_field."='".es($this->subid)."'")."
			ORDER BY ".str_replace('[D]', 'DESC', $this->order_by_field)." ".($limit ? "
			LIMIT $start, $limit" : ""));
	}


	public function __construct($settings) {
		$temp = self::$defaults;
		iengine::array_merge($temp, $settings);
		if(empty($temp['order_by_field'])) $temp['order_by_field'] = $temp['id_field'].' [D]';
		parent::set($temp);
	}


	public function INIT(&$args) {
		$this->html_id = $this->parent->imodule_name.'_'.$this->name.$this->subid;
		if($this->default_text === false) $this->default_text = '';

		$update = false;
		foreach($this->places as $place=>$opts) {
			$opts['name'] = $place;
			$opts->update = (empty($this->parent->idle[$this->name][$this->subid][$place]) ? false : true);
			if($opts->update) $update = true;
		}

		if($update) set_fast_ajax_update($this->weight);
	}


	public function IDLE(&$idle, $id_key = null, $pre_callback = null, $post_callback = null) {
		if(isset($idle[$this->name]) and is_array($idle[$this->name])) {
			foreach($idle[$this->name] as $subid=>$args) {
				$this->subid = $subid;
				$args[$id_key === null ? $this->parent->imodule_name : $id_key] = $subid;
				if($this->INIT_function) {
					$INIT = $this->INIT_function;
					$retval = $this->parent->$INIT($args);
				}
				else $retval = $this->INIT($args);
				if($retval === 'IGNORE') continue;
				if($pre_callback) $pre_callback($args);
				$this->IDLE_($args);
				if($post_callback) $post_callback($args);
			}
		}
	}
	private function IDLE_(&$args) {
		if(!isset($args['last_id']))
			return;

		$limit = 0;
		$update = false;
		foreach($this->places as $place=>$opts) {
			if($opts['update']) {
				if($opts['limit'] > $limit) $limit = $opts['limit'];
				$update = true;
			}
		}
		if(!$update) return;
		if($limit > 100) $limit = 100;

		if(($last_id = cache_L1::get($this->html_id.'_last_id')) === false) {
			$last_id = $this->on_chat_query_last_id($args);
			cache_L1::set($this->html_id.'_last_id', 60, $last_id);
		}

		$this->parent->imodule_args[$this->name][$this->subid]['last_id'] = ($last_id ? $last_id : 0);
		foreach($this->places as $place=>$opts) {
			if($opts['update']) $this->parent->imodule_args[$this->name][$this->subid][$place] = 1;
		}

		$query_id = es($args['last_id']);
		if($last_id <= $query_id) return;

		if(!$query_id or $query_id < $last_id - $limit) $query_id = $last_id - $limit;
		$chat_posts = $this->on_chat_query_idle($args, $query_id, $limit);

		foreach($this->places as $place=>$opts) {
			if($opts['update']) {
				G::$json_data['s'][] = "$('#".$this->html_id.$place." .chat-row:gt(".($opts['limit'] - 1).")').remove();";
				G::$json_data['e']['#'.$this->html_id.$place.' .chat-insert-point'] = array('fn' => 4, 'd' => array());
			}
		}

		$this->parent->im_chat =& $this;
		while($this->post = $chat_posts->fetch_assoc()) {
			foreach($this->places as $place=>$opts) {
				if($opts['update']) {
					$this->place = $opts;
					G::$json_data['e']['#'.$this->html_id.$place.' .chat-insert-point']['d'][] = $this->parent->ilphp_fetch('~/im_chat.php.ilp|row');
				}
			}
		}
	}


	public function POST_chat_new(&$args) {
		if($args['message'] == $this->default_text) $args['message'] = '';
		if(!$args['message']) $this->error = LS('Du musst eine Nachricht eingeben.');
		elseif($this->deny_post) $this->error = LS('Du hast keine Berechtigung etwas zu schreiben.');
		else {
			$last_id = $this->on_chat_new($args, $args['message']);
			cache_L1::del($this->html_id.'_last_id');
			cache_L1::set($this->html_id.'_last_id', 60, $last_id);
		}

		if(IS_AJAX) {
			foreach($this->places as $place=>$opts) {
				if($opts['update']) {
					G::$json_data['e']['#'.$this->html_id.$place.' .error'] = $this->error;
					G::$json_data['e']['#'.$this->html_id.$place.' .chat-message-form button[type=submit]'] = LS('Shout!');
					G::$json_data['s'][] =  "m=$('#".$this->html_id.$place." .chat-message-form [type=submit]:focus').parent('form').find('.chat-message-input');if(m.length)m.attr('value','').focus();";
				}
			}
		}
	}

	public function POST_chat_edit(&$args) {
		if(!$this->has_mod_rights) throw new iexception('403', $this);

		$post_id = (int)$args['post_id'];
		$bbinfos = db()->query("SELECT user_id FROM ".$this->table." WHERE ".$this->id_field."='".$post_id."'".($this->subid === 0 ? "" : " AND ".$this->subid_field."='".es($this->subid)."'")." LIMIT 1")->fetch_assoc();
		if(!$bbinfos) return;

		bigbrother($this->table.'_changed', array($post_id, @$bbinfos['user_id']));
		$this->on_chat_edit($args, $post_id, $args['message']);
		cache_L1::del($this->html_id.'_last_id');
		if(IS_AJAX) {
			foreach($this->places as $place=>$opts) {
				if($opts['update']) G::$json_data['e']['#'.$this->html_id.$place.' .error'] = $this->error;
			}

			$this->parent->im_chat =& $this;
			$this->post = $this->on_chat_query_single($args, $post_id);
			foreach($this->places as $place=>$opts) {
				if($opts['update']) {
					$this->place = $opts;
					G::$json_data['e']['#'.$this->html_id.$place.' .chat-row-'.$post_id] = $this->parent->ilphp_fetch('~/im_chat.php.ilp|row');
				}
			}
		}
	}

	public function POST_chat_delete(&$args) {
		if(!$this->has_mod_rights) throw new iexception('403', $this);

		$post_id = (int)$args['post_id'];
		$bbinfos = db()->query("SELECT user_id FROM ".$this->table." WHERE ".$this->id_field."='".$post_id."'".($this->subid === 0 ? "" : " AND ".$this->subid_field."='".es($this->subid)."'")." LIMIT 1")->fetch_assoc();
		if(!$bbinfos) return;

		bigbrother($this->table.'_deleted', array($post_id, @$bbinfos['user_id']));
		$this->on_chat_delete($args, $post_id);
		cache_L1::del($this->html_id.'_last_id');
		if(IS_AJAX) {
			foreach($this->places as $place=>$opts) {
				if($opts['update']) {
					G::$json_data['e']['#'.$this->html_id.$place.' .chat-row-'.$post_id] = '';
				}
			}
		}
	}

	public function POST_chat_change_page(&$args) {
		if(!IS_AJAX) page_redir($this->url.$this->page_var.'/'.(int)$args[$this->page_var].'/');

		$place = $args['place'];
		if(empty($this->places[$place])) return;
		if(!$this->places[$place]['pages']) return;

		return $this->RENDER($place);
	}


	public function RENDER($place) {
		$this->place = $this->places[$place];

		$this->posts = true;
		if($this->place['pages']) {
			$this->page = empty($this->parent->args[$this->page_var]) ? 1 : (int)$this->parent->args[$this->page_var];
			if($this->page < 1) $this->page = 1;

			$cache_id = 'im_chat_'.$this->table.'_'.$this->subid.'_num_rows';
			$num_rows = cache_L1::get($cache_id);
			if($num_rows === false) {
				$num_rows = $this->on_chat_query_num_rows($args);
				cache_L1::set($cache_id, 20, $num_rows);
			}
			if(!$num_rows or $num_rows < ($this->page - 1)*$this->place['limit']) {
				$this->num_pages = 0;
				$this->posts = false;
			}
			else {
				$this->num_pages = calculate_pages($num_rows, $this->place['limit']);
			}
		}
		else {
			$this->page = 1;
			$this->num_pages = 0;
		}

		if($this->posts) {
			$this->posts = $this->on_chat_query_posts($args, ($this->page - 1)*$this->place['limit'], $this->place['limit']);
		}

		$this->parent->im_chat =& $this;
		$data = $this->parent->ilphp_fetch('~/im_chat.php.ilp');

		if($this->page == 1) {
			$this->parent->imodule_args[$this->name][$this->subid]['last_id'] = $this->last_id;
			$this->parent->imodule_args[$this->name][$this->subid][$place] = 1;
		}

		return $data;
	}
}

trait im_chat {
	public $im_chats = [];

	public function im_chat_add($name, $settings) {
		$settings['name'] = 'im_chat_'.$name;
		$settings['parent'] =& $this;
		$chat = new im_chat_settings($settings);
		$this->im_chats[$chat->name] =& $chat;
		return $chat;
	}
	public function im_chat_get($name) {
		return $this->im_chats['im_chat_'.$name];
	}

	private function _POST_CALL(&$args, $cb) {
		if(isset($args['namespace']) and isset($this->im_chats[$args['namespace']])) {
			return $this->im_chats[$args['namespace']]->$cb($args);
		}
	}
	protected function POST_chat_new(&$args) {
		return $this->_POST_CALL($args, 'POST_chat_new');
	}
	protected function POST_chat_edit(&$args) {
		return $this->_POST_CALL($args, 'POST_chat_edit');
	}
	protected function POST_chat_delete(&$args) {
		return $this->_POST_CALL($args, 'POST_chat_delete');
	}
	protected function POST_chat_change_page(&$args) {
		return $this->_POST_CALL($args, 'POST_chat_change_page');
	}
}

?>
