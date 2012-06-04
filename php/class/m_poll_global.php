<?php

class m_poll_global {
	public static function ultra_admin() {
		return IS_LOGGED_IN and (USER_ID == 1 or USER_ID == 5451);
	}
	public static function build_where($needed_status = false) {
		if(self::ultra_admin()) return $needed_status ? "status='$needed_status'" : "1";
		$points = round(user()->points);
		return
			"status!='deleted' AND
			(
				MATCH (groups) AGAINST ('".implode(' ', user()->groups)."' IN BOOLEAN MODE)".(IS_LOGGED_IN ? " OR
				creator='".USER_ID."'" : "")."
			)";
	}
}

?>
