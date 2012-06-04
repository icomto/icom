<?php

class m_chat_global {
	public static function is_low_admin() {
		return self::is_admin() or USER_ID == 18403;
	}
	public static function is_admin() {
		return self::is_ultra_admin() or (IS_LOGGED_IN and user()->has_group(175)) or USER_ID == 4082;
	}
	public static function is_ultra_admin() {
		return IS_LOGGED_IN and (USER_ID == 1 or USER_ID == 5451 or USER_ID == 5561 or USER_ID == 6451 or USER_ID == 4664 or user()->has_group(175));
	}
	
	public static function build_where($needed_status = NULL) {
		if(self::is_ultra_admin()) return $needed_status ? "status='$needed_status'" : "status!='deleted'";
		if(self::is_admin()) return $needed_status ? "status='$needed_status'" : "status!='deleted'";
		$points = round(user()->points);
		return
				(IS_LOGGED_IN ? ($needed_status ? "status='$needed_status'" : "
				(
					status='open' OR
					(
						status='closed' AND
						MATCH (a.admins) AGAINST ('+".USER_ID."' IN BOOLEAN MODE)
					)
				)") : "status='open'")." AND
				
				".(IS_LOGGED_IN ? "NOT MATCH (banned_users) AGAINST ('+".USER_ID."' IN BOOLEAN MODE) AND" : "")."
				(".(IS_LOGGED_IN ? "
					MATCH (admins) AGAINST ('+".USER_ID."' IN BOOLEAN MODE) OR
					MATCH (users) AGAINST ('+".USER_ID."' IN BOOLEAN MODE) OR" : "")."
					MATCH (a.groups) AGAINST ('".implode(' ', user()->groups)."' IN BOOLEAN MODE)
				) AND
				(
					(
						points_from=0 AND
						points_to=0
					) OR
					(
						points_from>0 AND
						points_to>0 AND
						$points BETWEEN points_from AND points_to
					) OR
					(
						points_from>0 AND
						points_to=0 AND
						$points>=points_from
					) OR
					(
						points_from=0 AND
						points_to>0 AND
						$points<=points_to
					)
				)";
	}
}

?>