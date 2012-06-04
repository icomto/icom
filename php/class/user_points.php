<?php

trait user_points {
	public function check_special_groups($do_sendpn = true, $do_remove = false) {
		$changed = array();
		self::check_special_groups_handler($changed, LEVEL2_GROUPID, LEVEL2_POINTS, 0, $do_sendpn, $do_remove);
		self::check_special_groups_handler($changed, NOAD_GROUPID, NOAD_POINTS, NOAD_TIMEONPAGE, $do_sendpn, $do_remove);
		return $changed;
	}
	
	private function check_special_groups_handler(&$changed, $group_id, $needed_points, $needed_time, $do_sendpn = true, $do_remove = false) {
		static $GROUPCHANGE_PNS = NULL;
		if($GROUPCHANGE_PNS === NULL) {
			$GROUPCHANGE_PNS = array(
				LEVEL2_GROUPID => array(
					'added' => 'Weil deine Punktzahl '.LEVEL2_POINTS.' ueberschritten hat hast Du ab jetzt Zugang zur [url=http://icom.to/forum/178-Level-2/]Level 2[/url] Forensektion.',
					'removed' => 'Weil deine Punktzahl unter '.LEVEL2_POINTS.' gefallen ist hast du ab jetzt keinen Zugang zum Level 2 Forum mehr.',
					'public_group' => true,
					'hidden_group_id' => LEVEL2_HIDDEN_GROUPID
				),
				NOAD_GROUPID => array(
					'added' => 'Weil du so ein netter Stammuser bist und die Seite vollspamst oder einen grossen Teil deines Lebens hier verbringst siehst du jetzt keine Werbung mehr :)',
					'public_group' => false
				)
			);
		}
		if($this->i['points'] >= $needed_points and ($needed_time == 0 or $needed_time <= $this->time_on_page) and !in_array($group_id, $this->i['groups'])) {
			$has_changed = $this->add_group($group_id);
			if($has_changed and $do_sendpn and isset($GROUPCHANGE_PNS[$group_id]) and isset($GROUPCHANGE_PNS[$group_id]['added']))
				$this->pn_system($GROUPCHANGE_PNS[$group_id]['added']);
			if($has_changed) $changed[] = sprintf('%5s %8s %3s added', $this->i['user_id'], round($this->i['points']), $group_id);
		}
		elseif($do_remove and $this->i['points'] < $needed_points and ($needed_time == 0 or $needed_time > $this->time_on_page) and in_array($group_id, $this->i['groups'])) {
			$has_changed = $this->del_group($group_id);
			if($do_sendpn and isset($GROUPCHANGE_PNS[$group_id]) and isset($GROUPCHANGE_PNS[$group_id]['removed']))
				$this->pn_system($GROUPCHANGE_PNS[$group_id]['removed']);
			if($has_changed) $changed[] = sprintf('%5s %8s %3s removed', $this->i['user_id'], round($this->i['points']), $group_id);
		}
	}
	
	public function update_points() {
		db()->query("
			UPDATE users
			SET
				points=
					COALESCE((SELECT COUNT(*)*0.01 AS points FROM shoutbox_de WHERE user_id=users.user_id), 0) +
					COALESCE((SELECT COUNT(*)*0.01 AS points FROM shoutbox_de_archive WHERE user_id=users.user_id), 0) +
					COALESCE((SELECT COUNT(*)*0.01 AS points FROM shoutbox_en WHERE user_id=users.user_id), 0) +
					COALESCE((SELECT COUNT(*)*0.01 AS points FROM shoutbox_en_archive WHERE user_id=users.user_id), 0) +
					COALESCE((SELECT COUNT(*)*0.01 AS points FROM user_chat_content WHERE user_id=users.user_id), 0) +

					COALESCE((
						SELECT SUM(c.points)*(1 + ((LENGTH(a.thanks)-LENGTH(REPLACE(a.thanks,',',''))) + 1)*0.05) AS points
						FROM forum_posts a
						JOIN forum_threads b USING (thread_id)
						JOIN forum_sections c USING (section_id)
						WHERE
							a.user_id=users.user_id AND
							a.thread_id=b.thread_id AND
							b.section_id=c.section_id
					), 0) +

					COALESCE((
						SELECT SUM(IF(namespace='bor', 0.75, 0.5)) AS points
						FROM forum_reported_posts
						WHERE user_id=users.user_id AND open=0 AND was_good_ticket=0
					), 0) +

					COALESCE((
						SELECT -SUM(IF(timeending<CURRENT_TIMESTAMP, points*0.5, points)) AS points
						FROM user_warnings
						WHERE user_id=users.user_id
					), 0),
				forum_posts=COALESCE((SELECT COUNT(post_id) FROM forum_posts WHERE user_id=users.user_id), 0)
			WHERE user_id='".$this->i['user_id']."'");
		if(db()->affected_rows) {
			$a = db()->query("SELECT points, forum_posts FROM users WHERE user_id='".$this->i['user_id']."' LIMIT 1")->fetch_assoc();
			$this->i['points'] = $a['points'];
			$this->i['forum_posts'] = $a['forum_posts'];
			return true;
		}
	}
}

?>
