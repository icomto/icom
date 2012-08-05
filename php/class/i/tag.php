<?php

/*
CREATE TABLE i_tag_childs (
	alias_id BIGINT NOT NULL,
	child_id BIGINT NOT NULL,
	PRIMARY KEY (alias_id, child_id)
);
 */

class i__tag extends ArrayClass2 {
	protected $table = 'i_tags';
	protected $id_field = 'tag_id';

	private $aliases = null;
	private $parents = null;
	private $childs = null;

	public function __construct($data = NULL) {
		$this->sql_set_fields = ['alias_id'];
		parent::__construct($data);
		if($this->name == 'fsk18' and !session::$s['verified_fsk18']) {
			throw new iexception('FSK18_BLOCKED');
		}

		#print_r($this->getAliases());
	}

	public static function insert($name) {
		$id = i__i::hash(strtolower($name));
		db()->query("INSERT IGNORE INTO i_tags SET tag_id='".$id."', alias_id='".$id."', name='".es($name)."'");
		return new self($id);
	}

	public static function insert_string($str, $addTo = []) {
		$out = [];
		$tags = array_filter(array_map('trim', explode(' ', preg_replace('~[\s,;]+~', ' ', $str))));
		foreach($tags as $tag) {
			$tag = self::insert($tag);
			$out[] = $tag;
			foreach($addTo as $add) {
				$add[1][0] = $tag;
				call_user_func_array([$add[0], 'addTag'], $add[1]);
			}
		}
		return $out;
	}

	public static function renderTagList($tags, $imodule, $url, $allow_add, $allow_remove) {
		$out = [];
		$rand = mt_rand();
		foreach($tags as $tag) {
			$str = $tag->getLinkHtml();
			if($allow_remove) {
				$str .=
					'<form style="display:inline;" action="'.htmlspecialchars($url).'" onsubmit="return iC(this, \'~.tagList'.$rand.'\');">'.
						'<input type="hidden" name="'.imodule::IMODULE_POST_VAR_STATIC($imodule, 'action').'" value="remove_tag">'.
						'<input type="hidden" name="'.imodule::IMODULE_POST_VAR_STATIC($imodule, 'tag_id').'" value="'.$tag->id.'">'.
						'<button style="border:0;color:red;" type="submit">X</button>'.
					'</form>';
			}
			$out[] = $str;
		}
		$out = implode(', ', $out);
		if($allow_add) {
			if($out) $out .= '<br />';
			$out .=
				'<button type="button" class="button" onclick="$(this).hide().next().show().find(\'input[type=text]\').focus().select();">'.LS('Tags hinzuf&uuml;gen').'</button>'.
				'<form method="post" action="'.htmlspecialchars($url).'" onsubmit="return iC(this, \'~.tagList'.$rand.'\');" style="display:none">'.
					'<input type="hidden" name="'.imodule::IMODULE_POST_VAR_STATIC($imodule, 'action').'" value="add_tags">'.
					LS('Tags:').' <input type="text" name="'.imodule::IMODULE_POST_VAR_STATIC($imodule, 'tags').'">'.
					'<button type="submit" class="button">'.LS('Hinzuf&uuml;gen').'</button>'.
				'</form>';
		}
		return $out ? '<span class="tagList'.$rand.'">'.$out.'</span>' : '';
	}

	public function getLink() {
		return '/'.LANG.'/i/tag/'.urlencode($this->name).'/';
	}
	public function getLinkHtml() {
		return '<a href="'.htmlspecialchars($this->getLink()).'">'.htmlspecialchars($this->name).'</a>';
	}


	public function addAlias($tag) {
		if($this->alias_id) $alias_id = $this->alias_id;
		elseif($tag->alias_id) $alias_id = $tag->alias_id;
		else $alias_id = $this->id;

		foreach($this->getAliases() as $alias)
			$alias->alias_id = $alias_id;
		$this->alias_id = $alias_id;

		foreach($tag->getAliases() as $alias)
			$alias->alias_id = $alias_id;
		$tag->alias_id = $alias_id;

		$this->aliases = null;
	}
	public function removeAlias() {
		if($this->alias_id == $this->id and $this->countAliases() > 1) {
			$aliases = $this->getAliases();
			foreach($aliases as $alias) {
				$alias->alias_id = $aliases[0]->alias_id;
			}
		}
		$this->alias_id = $this->tag_id;
	}
	public function getAliases() {
		if($this->aliases === null) {
			$this->aliases = [];
			$aa = db()->query("SELECT * FROM i_tags WHERE tag_id!='".$this->id."' AND alias_id='".$this->alias_id."'");
			while($a = $aa->fetch_assoc()) {
				$this->aliases[] = new self($a);
			}
		}
		return $this->aliases;
	}
	public function countAliases() {
		return count($this->getAliases());
	}

	public function addChild($tag) {
		if($this->alias_id != $tag->alias_id) {
			db()->query("INSERT IGNORE INTO i_tag_childs SET alias_id='".$this->alias_id."', child_id='".$tag->alias_id."'");
			return true;
		}
	}
	public function removeChild($tag) {
		if($this->alias_id != $tag->alias_id) {
			db()->query("DELETE FROM i_tag_childs WHERE alias_id='".$this->alias_id."' AND child_id='".$tag->alias_id."'");
			return true;
		}
	}
	public function getChilds() {
		if($this->childs === null) {
			$this->childs = [];
			$aa = db()->query("
				SELECT b.*
				FROM i_tag_childs a
				JOIN i_tags b ON a.child_id=b.alias_id
				WHERE a.alias_id='".$this->alias_id."'
				ORDER BY b.name");
			while($a = $aa->fetch_assoc()) {
				$this->childs[] = new self($a);
			}
		}
		return $this->childs;
	}
	public function countChilds() {
		return count($this->getChilds());
	}

	public function getParents() {
		if($this->parents === null) {
			$this->parents = [];
			$aa = db()->query("
				SELECT b.*
				FROM i_tag_childs a
				JOIN i_tags b
				USING (alias_id)
				WHERE a.child_id='".$this->alias_id."'
				ORDER BY b.name");
			while($a = $aa->fetch_assoc()) {
				$this->parents[] = new self($a);
			}
		}
		return $this->parents;
	}
	public function countParents() {
		return count($this->getParents());
	}

	public function rename($name) {
		$name = trim($name);
		if(!$name or preg_match('~\s~', $name)) return;
		try {
			$tag = new self($name);
			if($tag->name != $name) {
				throw new NotFoundException();
			}
		}
		catch(NotFoundException $e) {
			$id = i__i::hash(strtolower($name));
			if($this->id == $this->alias_id) {
				db()->query("UPDATE i_tags SET alias_id='".$id."' WHERE alias_id='".$this->alias_id."'");
				//UPDATE IGNORE THEN DELETE?!
				db()->query("UPDATE i_tag_childs SET alias_id='".$id."' WHERE alias_id='".$this->alias_id."'");
				db()->query("UPDATE i_tag_childs SET child_id='".$id."' WHERE child_id='".$this->alias_id."'");
			}
			db()->query("UPDATE i_set_tags SET tag_id='".$id."' WHERE tag_id='".$this->id."'");
			db()->query("UPDATE i_image_tags SET tag_id='".$id."' WHERE tag_id='".$this->id."'");
			db()->query("UPDATE i_tags SET tag_id='".$id."', name='".es($name)."' WHERE tag_id='".$this->id."' LIMIT 1");
			$this->getById($id);
			return true;
		}
	}
	public function mergeWith($tag) {
		db()->query("UPDATE i_tags SET alias_id='".$tag->alias_id."' WHERE alias_id='".$this->alias_id."'");
		//UPDATE IGNORE THEN DELETE?!
		db()->query("UPDATE i_tag_childs SET alias_id='".$tag->alias_id."' WHERE alias_id='".$this->alias_id."'");
		db()->query("UPDATE i_tag_childs SET child_id='".$tag->alias_id."' WHERE child_id='".$this->alias_id."'");
		db()->query("UPDATE i_set_tags SET tag_id='".$tag->id."' WHERE tag_id='".$this->id."'");
		db()->query("UPDATE i_image_tags SET tag_id='".$tag->id."' WHERE tag_id='".$this->id."'");
		db()->query("DELETE FROM i_tags WHERE tag_id='".$this->id."' LIMIT 1");
	}
}

?>
