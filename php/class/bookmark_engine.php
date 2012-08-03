<?php

class bookmark_engine {
	public static function icon($url, $thing, $thing_id) {
		return self::_icon($url, $thing, $thing_id);
	}
	public static function _icon($url, &$thing, &$thing_id) {
		if(!IS_LOGGED_IN) return;
		/*elseif(isset($_POST['bmea']) and @$_POST['bmet'] == $thing and @$_POST['bmei'] == $thing_id) {
			$bookmarked = ((int)$_POST['bmea'] ? true : false);
			if($bookmarked) db()->query("INSERT IGNORE INTO user_bookmarks SET user_id='".USER_ID."', thing='$thing', thing_id='$thing_id'");
			else db()->query("DELETE FROM user_bookmarks WHERE user_id='".USER_ID."' AND thing='$thing' AND thing_id='$thing_id'");
		}*/
		$bookmarked = (db()->query("SELECT 1 FROM user_bookmarks WHERE user_id='".USER_ID."' AND thing='$thing' AND thing_id='$thing_id' LIMIT 1")->num_rows ? true : false);
		return '<form method="POST" class="bookmark-icon '.($bookmarked ? 'bookmarked' : 'normal').'" onsubmit="return iC(this);">'.
			'<input type="hidden" name="imodules/bookmark_engine/action" value="'.($bookmarked ? 'remove' : 'add').'">'.
			'<input type="hidden" name="imodules/bookmark_engine/thing" value="'.$thing.'">'.
			'<input type="hidden" name="imodules/bookmark_engine/id" value="'.$thing_id.'">'.
			'<button type="submit" title="'.($bookmarked ? LS('Aus den Lesezeichen rausnehmen') : LS('In meine Lesezei:chen aufnehmen')).'"></button>'.
			'</form>';
	}
}

?>
