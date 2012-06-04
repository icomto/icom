<?php

class m_admin_forum extends imodule {
	use ilphp_trait;
	use im_way;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$init) {
		if(!has_privilege('forum_admin')) throw new iexception('ACCESS_DENIED', $this);
		$this->url = '/'.LANG.'/admin/forum/';
		$this->way[] = array(LS('Admin'), '');
		$this->way[] = array(LS('Forum'), '/admin/forum/');
	}
	
	protected function MODULE(&$args) {
		$this->parent = (int)@$args[$this->imodule_name];
		
		if($this->parent) {
			$chain = array();
			$parent_ = $this->parent;
			$init = NULL;
			while($row = db()->query("SELECT section_id, ".LQ('name_LL')." AS name, parent FROM forum_sections WHERE section_id='".$parent_."' LIMIT 1")->fetch_assoc()) {
				if($init === NULL) $init = $row;
				$chain[] = $row;
				$parent_ = $row['parent'];
			}
			for($i = count($chain) - 1; $i >= 0; $i--) {
				$this->way[] = array($chain[$i]['name'], $this->url.$chain[$i]['section_id'].'-'.urlenc($chain[$i]['name']).'/');
			}
			if($init) {
				$this->url .= $init['section_id'].'-'.urlenc($init['name']).'/';
			}
		}
		
		$this->im_way_title();
		
		$this->sections = db()->query("SELECT *, ".LQ('name_LL')." AS name FROM forum_sections WHERE parent='".$this->parent."' ORDER BY position, ".LQ('name_LL'));
		$this->positions = array();
		return $this->ilphp_fetch('forum.php.ilp');
	}
	
	public function _afr_get_group($id) {
		return db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE id='$id' LIMIT 1")->fetch_assoc();;
	}
	public function row(&$i) {
		$this->i =& $i;
		
		$this->available_read_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE NOT FIND_IN_SET(id, '".$this->i['read_groups']."') AND NOT id='".BANNED_GROUPID."' ORDER BY ".LQ('name_LL')." LIKE '\\_%', ".LQ('name_LL'));
		#$this->available_read_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE ".LQ('name_LL')." NOT LIKE '\\_%' AND NOT FIND_IN_SET(id, '".$this->i['read_groups']."') AND NOT id='".BANNED_GROUPID."' ORDER BY ".LQ('name_LL'));
		$this->i['read_groups'] = explode_arr_list($this->i['read_groups']);
		
		$this->available_write_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE NOT FIND_IN_SET(id, '".$this->i['write_groups']."') AND NOT id='".BANNED_GROUPID."' ORDER BY ".LQ('name_LL')." LIKE '\\_%', ".LQ('name_LL'));
		#$this->available_write_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE ".LQ('name_LL')." NOT LIKE '\\_%' AND NOT FIND_IN_SET(id, '".$this->i['write_groups']."') AND NOT id='".BANNED_GROUPID."' ORDER BY ".LQ('name_LL'));
		$this->i['write_groups'] = explode_arr_list($this->i['write_groups']);
			
		$this->possible_mods = array();
		$rv = db()->query("SELECT id FROM groups WHERE forum_mod");
		$mod_groups = array();
		while($g = $rv->fetch_object()) $mod_groups[] = "FIND_IN_SET('".$g->id."', groups)";
		$rv2 = db()->query("SELECT user_id, nick FROM users WHERE NOT FIND_IN_SET(user_id, '".$this->i['mods']."') AND ".join(" OR ", $mod_groups)." ORDER BY nick");
		while($user = $rv2->fetch_assoc()) if(!in_array($user['user_id'], $this->possible_mods)) $this->possible_mods[] = $user;
		if($this->i['mods']) $this->mods = db()->query("SELECT user_id, nick FROM users WHERE user_id IN (".preg_replace("~,,+~", ",", $this->i['mods']).") ORDER BY nick");
		else $this->mods = false;
		
		$this->available_mod_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE NOT FIND_IN_SET(id, '".$this->i['mod_groups']."') AND NOT id='".BANNED_GROUPID."' ORDER BY ".LQ('name_LL')." LIKE '\\_%', ".LQ('name_LL'));
		#$this->available_mod_groups = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE ".LQ('name_LL')." NOT LIKE '\\_%' AND NOT FIND_IN_SET(id, '".$this->i['mod_groups']."') AND NOT id='".BANNED_GROUPID."' ORDER BY ".LQ('name_LL'));
		$this->i['mod_groups'] = explode_arr_list($this->i['mod_groups']);
			
		return $this->ilphp_fetch('forum.php.row.ilp');
	}

	private function add_to_childs($type, &$sections, $add_id) {
		while($section = $sections->fetch_assoc()) {
			$new = array();
			$arr = explode_arr_list($section[$type]);
			if(!in_array($add_id, $arr)) $arr[] = $add_id;
			$child_sections = db()->query("SELECT section_id, $type FROM forum_sections WHERE parent='".$section['section_id']."'");
			if($child_sections->num_rows) $this->add_to_childs($type, $child_sections, $add_id);
			db()->query("UPDATE forum_sections SET has_childs='".($child_sections->num_rows ? 1 : 0)."', $type='".implode_arr_list($arr)."' WHERE section_id='".$section['section_id']."' LIMIT 1");
		}
	}
	private function del_from_childs($type, &$sections, $del_id, &$child_arr = array()) {
		while($section = $sections->fetch_assoc()) {
			$new = array();
			$arr = explode_arr_list($section[$type]);
			foreach($arr as $i) if($i != $del_id) $new[] = $i;
			$my_childs = array();
			$child_sections = db()->query("SELECT section_id, $type FROM forum_sections WHERE parent='".$section['section_id']."'");
			if($child_sections->num_rows) {
				$this->del_from_childs($type, $child_sections, $del_id, $my_childs);
				$arr = array();
				foreach($new as $g) {
					if(!in_array($g, $my_childs)) {
						$arr[] = $g;
						if(!in_array($g, $child_arr)) $child_arr[] = $g;
					}
				}
			}
			else $arr = $my_childs = $new;
			db()->query("UPDATE forum_sections SET has_childs='".($child_sections->num_rows ? 1 : 0)."', $type='".implode_arr_list($arr)."' WHERE section_id='".$section['section_id']."' LIMIT 1");
		}
	}
	private function update_parents($type, $section_id, &$child_arr = array()) {
		$arr = array();
		$sections = db()->query("SELECT section_id, $type FROM forum_sections WHERE parent='$section_id'");
		while($section = $sections->fetch_assoc()) {
			$new = explode_arr_list($section[$type]);
			foreach($new as $i) if(!in_array($i, $arr)) $arr[] = $i;
		}
		db()->query("UPDATE forum_sections SET $type='".trim(join(",", $arr), ",")."' WHERE section_id='$section_id' LIMIT 1");
		$parent = db()->query("SELECT parent FROM forum_sections WHERE section_id='$section_id' LIMIT 1");
		if($parent->num_rows) $this->update_parents($type, $parent->fetch_object()->parent);
	}
	private function update_namespace($section_id, $namespace) {
		db()->query("UPDATE forum_sections SET namespace='$namespace' WHERE section_id='$section_id' LIMIT 1");
		$sections = db()->query("SELECT section_id FROM forum_sections WHERE parent='$section_id'");
		while($section = $sections->fetch_assoc()) $this->update_namespace($section['section_id'], $namespace);
	}

	private function add_XXX($section_id, $add_id, $type) {
		$sections = db()->query("SELECT section_id, $type FROM forum_sections WHERE section_id='$section_id' LIMIT 1");
		$this->add_to_childs($type, $sections, $add_id);
		
		$parent = db()->query("SELECT parent FROM forum_sections WHERE section_id='$section_id' LIMIT 1")->fetch_object()->parent;
		$this->update_parents($type, $parent);
		
		if(!IS_AJAX) page_redir('/'.LANG.'/admin/forum/'.$section_id.'/');
		$i = db()->query("SELECT * FROM forum_sections WHERE section_id='$section_id' LIMIT 1")->fetch_assoc();
		return $this->row($i);
	}
	private function del_XXX($section_id, $del_id, $type) {
		$sections = db()->query("SELECT section_id, $type FROM forum_sections WHERE section_id='$section_id' LIMIT 1");
		$this->del_from_childs($type, $sections, $del_id);
		
		$parent = db()->query("SELECT parent FROM forum_sections WHERE section_id='$section_id' LIMIT 1")->fetch_object()->parent;
		$this->update_parents($type, $parent);
		
		if(!IS_AJAX) page_redir('/'.LANG.'/admin/forum/'.$section_id.'/');
		$i = db()->query("SELECT * FROM forum_sections WHERE section_id='$section_id' LIMIT 1")->fetch_assoc();
		return $this->row($i);
	}
	
	protected function POST(&$args) {
		$section_id = (int)@$args['section_id'];
		if(!$section_id) return;
		$this->parent = $section_id;
		switch(@$args['action']) {
		case 'add_read_group':
			return $this->add_XXX($section_id, $args['group_id'], 'read_groups');
		case 'add_write_group':
			return $this->add_XXX($section_id, $args['group_id'], 'write_groups');
		case 'add_mod':
			return $this->add_XXX($section_id, $args['user_id'], 'mods');
		case 'add_mod_group':
			return $this->add_XXX($section_id, $args['group_id'], 'mod_groups');
		
		case 'del_read_group':
			return $this->del_XXX($section_id, $args['group_id'], 'read_groups');
		case 'del_write_group':
			return $this->del_XXX($section_id, $args['group_id'], 'write_groups');
		case 'del_mod':
			return $this->del_XXX($section_id, $args['user_id'], 'mods');
		case 'del_mod_group':
			return $this->del_XXX($section_id, $args['group_id'], 'mod_groups');
		
		case 'delete':
			if(!IS_AJAX) page_redir('/'.LANG.'/admin/forum/'.$section_id.'/');
			return LS('Das Forum mit der ID $section_id wurde NICHT gel&ouml;scht. MUHAHAHAHAHA');
		
		case 'new':
			$position = db()->query("SELECT MAX(position) AS mp FROM forum_sections WHERE parent='".(int)$args['new']."'")->fetch_object()->mp + 1;
			db()->query("INSERT INTO forum_sections SET parent='".(int)$args['new']."', position='$position'");
			self::rebuild_nested_sets();
			page_redir('/'.LANG.'/admin/forum/'.$section_id.'/');
		
		case 'positions':
			for($i = 1; $i <= $args['num_positions']; $i++) {
				$id = (int)@$args['positions'][$i];
				if($id) db()->query("UPDATE forum_sections SET position='$i' WHERE section_id='".$id."' LIMIT 1");
			}
			self::rebuild_nested_sets();
			page_redir('/'.LANG.'/admin/forum/'.$section_id.'/');
		
		case 'save':
			$name_de = es($args['name_de']);
			$name_en = es($args['name_en']);
			$description_de = es($args['description_de']);
			$description_en = es($args['description_en']);
			$allow_content = (empty($args['allow_content']) ? 0 : 1);
			$allow_threads = (empty($args['allow_threads']) ? 0 : 1);
			$points = (float)$args['points'];
			$has_childs = (db()->query("SELECT 1 FROM forum_sections WHERE parent='$section_id' LIMIT 1")->num_rows ? 1 : 0);
			db()->query("
				UPDATE forum_sections
				SET
					name_de=".NULLval($name_de).",
					name_en=".NULLval($name_en).",
					description_de=".NULLval($description_de).",
					description_en=".NULLval($description_en).",
					allow_content='$allow_content',
					allow_threads='$allow_threads',
					points='$points',
					has_childs='$has_childs'
				WHERE section_id='$section_id'
				LIMIT 1");
			$this->update_namespace($section_id, es($args['namespace']));
			$parent = db()->query("SELECT parent FROM forum_sections WHERE section_id='$section_id' LIMIT 1")->fetch_object()->parent;
			$this->update_parents('read_groups', $parent);
			$this->update_parents('write_groups', $parent);
			$this->update_parents('mods', $parent);
			$i = db()->query("SELECT * FROM forum_sections WHERE section_id='$section_id' LIMIT 1")->fetch_assoc();
			return $this->row($i);
		}
	}
	
	
	private static function rebuild_nested_sets() {
		db()->query('UPDATE forum_sections SET lft=0, rgt=0');
		db()->query('UPDATE forum_sections SET lft=1, rgt=2 WHERE name_de="root"');
		self::rebuild_nested_sets_handler('parent=0', 'name_de="root"');
	}
	private static function rebuild_nested_sets_handler($parent1, $parent2) {
		$rv = db()->query('SELECT section_id FROM forum_sections WHERE '.$parent1.' ORDER BY position');
		while($r = $rv->fetch_assoc()) {
			$rgt = db()->query('SELECT rgt FROM forum_sections WHERE '.$parent2)->fetch_assoc();
			$rgt = ($rgt ? $rgt['rgt'] : 1);
			db()->query('UPDATE forum_sections SET rgt=rgt+2 WHERE rgt>='.$rgt);
			db()->query('UPDATE forum_sections SET lft=lft+2 WHERE lft>'.$rgt);
			db()->query('UPDATE forum_sections SET lft='.$rgt.', rgt='.($rgt+1).' WHERE section_id='.$r['section_id']);
			self::rebuild_nested_sets_handler('parent='.$r['section_id'], 'section_id='.$r['section_id']);
		}
	}
}

?>
