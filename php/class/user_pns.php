<?php

trait user_pns {
	public function pn_system($message, $blink = true) {
		if(!$message) return true;
		$pn = db()->query("
			SELECT a.pn_id AS id
			FROM user_pns3_links a, user_pns3 b
			WHERE a.pn_id=b.pn_id AND a.user_id='".$this->i['user_id']."' AND b.name='%-SYSTEM%'
			LIMIT 1")->fetch_assoc();
		if($pn) $pn_id = $pn['id'];
		else {
			db()->query("
				INSERT INTO user_pns3
				SET
					name='%-SYSTEM%',
					creator='0',
					users=".$this->i['user_id'].",
					involved_users='".$this->i['user_id']."'");
			$pn_id = db()->insert_id;
			db()->query("INSERT IGNORE INTO user_pns3_links SET user_id=".$this->i['user_id'].", pn_id='$pn_id'");
		}
		db()->query("
			INSERT INTO user_pns3_content
			SET
				subid='".$pn_id."',
				user_id='0',
				message='".es($message)."'");
		if($blink) db()->query("UPDATE user_pns3_links SET has_new_message=1 WHERE pn_id='".$pn_id."'");
	}
	public function pn_message($pn_id, $message, $blink = true) {
		if(!$pn_id) return true;
		db()->query("
			INSERT INTO user_pns3_content
			SET
				subid='".$pn_id."',
				user_id='".$this->i['user_id']."',
				message='".es($message)."'");
		if($blink) db()->query("UPDATE user_pns3_links SET has_new_message=1 WHERE pn_id='".$pn_id."'");
	}
	public function pn_new($to = array(), $topic, $message, $blink = true) {
		if(!$to or !$topic or !$message) return;
		if(!is_array($to)) $to = explode_arr_list($to);
		if(!in_array($this->i['user_id'], $to)) $to[] = $this->i['user_id'];
		$users = es(implode_arr_list($to));
		db()->query("
			INSERT INTO user_pns3
			SET
				name='".es($topic)."',
				creator='".$this->i['user_id']."',
				users='".$users."',
				involved_users='".$users."'");
		$pn_id = db()->insert_id;
		db()->query("
			INSERT INTO user_pns3_content
			SET
				subid='".$pn_id."',
				user_id='".$this->i['user_id']."',
				message='".es($message)."'");
		if($blink)
			foreach($to as $user_id)
				db()->query("INSERT IGNORE INTO user_pns3_links SET user_id='".$user_id."', pn_id='$pn_id', has_new_message=1");
		return $pn_id;
	}
}

?>
