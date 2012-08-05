<?php

class i__tag extends ArrayClass2 {
	protected $table = 'i_tags';
	protected $id_field = 'tag_id';

	public function __construct($data = NULL) {
		parent::__construct($data);
		if($this->name == 'fsk18' and !session::$s['verified_fsk18']) {
			throw new iexception('FSK18_BLOCKED');
		}
	}

	public static function insert($name) {
		$id = i__i::hash(strtolower($name));
		db()->query("INSERT IGNORE INTO i_tags SET tag_id='".$id."', name='".es($name)."'");
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
				'<button type="button" onclick="$(this).hide().next().show().find(\'input[type=text]\').focus().select();">'.LS('Tags hinzuf&uuml;gen').'</button>'.
				'<form method="post" action="'.htmlspecialchars($url).'" onsubmit="return iC(this, \'~.tagList'.$rand.'\');" style="display:none">'.
					'<input type="hidden" name="'.imodule::IMODULE_POST_VAR_STATIC($imodule, 'action').'" value="add_tags">'.
					LS('Tags:').' <input type="text" name="'.imodule::IMODULE_POST_VAR_STATIC($imodule, 'tags').'">'.
					'<button type="submit">'.LS('Hinzuf&uuml;gen').'</button>'.
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
}

?>
