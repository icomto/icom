<?php

trait user_warnings {
	public function add_warning($warner_id, $warning_points, $days, $reason) {
		if(!has_privilege('user_warnings')) return;
		if($warning_points < 1) return 'NOT_ENOUGH_WARNING_POINTS';
		$timeending = ($days ? "CURRENT_TIMESTAMP+INTERVAL $days DAY" : "'0000-00-00 00:00:00'");
		db()->query("INSERT INTO user_warnings SET user_id='".$this->i['user_id']."', warner_id='".(int)$warner_id."', points='".(float)$warning_points."', timeending=$timeending, reason='".es($reason)."'");
		bigbrother('warning_created', array(db()->insert_id, $this->i['user_id'], $warning_points, $days));
		
		$this->check_warning_points();
		
		$this->update_points();
		$this->check_special_groups(true, false);
		
		cache_L1::del('init_session_user_'.$this->i['user_id'].'_'.$this->i['salt']);
		$this->pn_system('Du hast eine Verwarnung erhalten.');
		$this->flush_cache();
	}
	public function del_warning($warner_id, $warning_id) {
		$warning = db()->query("SELECT points, IF(timeending<CURRENT_TIMESTAMP,1,0) AS ended FROM user_warnings WHERE warning_id='".(int)$warning_id."' AND user_id='".$this->i['user_id']."' LIMIT 1")->fetch_assoc();
		if(!$warning) return;
		db()->query("DELETE FROM user_warnings WHERE warning_id='".(int)$warning_id."' AND user_id='".$this->i['user_id']."' LIMIT 1");
		bigbrother('warning_deleted', array($warning_id, $this->i['user_id']));
		
		$this->check_warning_points();
		
		$this->update_points();
		$this->check_special_groups(true, false);
		
		cache_L1::del('init_session_user_'.$this->i['user_id'].'_'.$this->i['salt']);
		$this->pn_system(utf8_encode('Eine Verwarnung von Dir wurde gelöscht.'));
		$this->flush_cache();
	}
	
	
	
	public function check_warning_points() {
		$warning_points = db()->query("
			SELECT SUM(points) points
			FROM user_warnings
			WHERE
				user_id='".$this->i['user_id']."' AND
				(
					timeending='0000-00-00 00:00:00' OR
					timeending>=CURRENT_TIMESTAMP
				)")->fetch_assoc()['points'];
		if($warning_points > MAX_WARNING_POINTS) $warning_points = MAX_WARNING_POINTS;
		if($this->i['open_warnings'] != $warning_points) $this->update(['open_warnings' => $warning_points]);
		if($warning_points == MAX_WARNING_POINTS and !in_array(BANNED_GROUPID, $this->i['groups'])) {
			$this->add_group(BANNED_GROUPID);
			return 'banned';
		}
		elseif($warning_points < MAX_WARNING_POINTS and in_array(BANNED_GROUPID, $this->i['groups'])) {
			$this->del_group(BANNED_GROUPID);
			return 'unbanned';
		}
	}
}

?>
