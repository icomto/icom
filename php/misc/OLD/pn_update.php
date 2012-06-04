<?php

require_once '../config.inc.php';
require_once '../functions.inc.php';
#require_once '../init_session.inc.php';
header('Content-Type: text/plain');

db()->DEBUG = true;

/******** fix wrong user_pns3_links
$aa = db()->query("select a.*, b.users as users from user_pns3_links a, user_pns3 b where a.pn_id=b.id");
while($a = $aa->fetch_assoc()) {
	$users = explode_arr_list($a['users']);
	if(!in_array($a['user_id'], $users)) {
		db()->query("DELETE FROM user_pns3_links WHERE pn_id='".$a['pn_id']."' AND user_id='".$a['user_id']."'");
		if(db()->affected_rows) echo 'x';
	}
	foreach($users as $u) {
		db()->query("INSERT IGNORE INTO user_pns3_links SET pn_id='".$a['pn_id']."' AND user_id='".$u."', has_new_message=1");
		if(db()->affected_rows) echo '.';
	}
}
echo "\n";*/

/******** fix involved_users user_pns3_links */
$aa = db()->query("select id, users from user_pns3");
while($a = $aa->fetch_assoc()) {
	$bb = db()->query("select uid from user_pns3_content where subid='".$a['id']."' group by uid");
	$involved_users = array();
	while($b = $bb->fetch_assoc())
		if($b['uid']) $involved_users[] = $b['uid'];
	$users = explode_arr_list($a['users']);
	foreach($users as $u)
		if($u and !in_array($u, $involved_users))
			$involved_users[] = $u;
	db()->query("update user_pns3 set involved_users='".implode_arr_list($involved_users)."' where id='".$a['id']."' limit 1");
}
echo "\n";


function commit(&$c) {
	db()->query("
		INSERT INTO user_pns3
		SET
			name='".db()->escape_string($c['name'])."',
			timecreated='".$c['timecreated']."',
			creator='".$c['creator']."',
			users='".implode_arr_list($c['users'])."'");
	$pn_id = db()->insert_id;
	echo $pn_id.' - C';
	foreach($c['messages'] as $msg) {
		db()->query("
			INSERT user_pns3_content
			SET
				subid='$pn_id',
				uid='".$msg['uid']."',
				timeadded='".$msg['timeadded']."',
				message='".es($msg['message'])."'");
		echo 'm';
	}
	foreach($c['users'] as $user_id) {
		db()->query("INSERT IGNORE INTO user_pns3_links SET pn_id='$pn_id', user_id='$user_id', has_new_message='".(in_array($user_id, $c['users_unread']) ? 1 : 0)."'");
		echo 'U';
	}
	echo " - ".implode_arr_list($c['users'])."\n";
	flush();
}






db()->query("truncate table user_pns3");
db()->query("truncate table user_pns3_content");
db()->query("truncate table user_pns3_polls");
db()->query("truncate table user_pns3_online_users");
db()->query("truncate table user_pns3_links");










$users = db()->query("SELECT id FROM users ORDER BY id");
while($user = $users->fetch_assoc()) {
	echo "---- user ".$user['id']."\n";
	mode1(db()->query("
		select *
		from pns
		where
			conversation_id!=0 and
			owner=".$user['id']." and
			(
				u_from=0 or
				u_to=0 or
				topic='Freundschaftsanfrage' or
				topic='Freundschaftsanfrage wurde storniert' or
				topic='Freundschaftsanfrage wurde abgelehnt' or
				topic='Freundschaftsanfrage wurde angenommen'
			) and
			(
				u_from=".$user['id']." or
				u_to=".$user['id']."
			)
		order by conversation_id, id"));
}

mode2(db()->query("
	select *
	from pns
	where
		conversation_id!=0 and
		(
			u_from!=0 and
			u_to!=0
		) and
		/*(
			owner=1 or
			u_from=1 or
			u_to=1
		) and*/
		not (
			u_from=0 or
			u_to=0 or
			topic='Freundschaftsanfrage' or
			topic='Freundschaftsanfrage wurde storniert' or
			topic='Freundschaftsanfrage wurde abgelehnt' or
			topic='Freundschaftsanfrage wurde angenommen'
		)
	order by conversation_id, id"));


function mode1($pns) {
	$current_user = 0;
	$c = NULL;
	$last_hash = NULL;
	$hash_users = array();
	while($pn = $pns->fetch_assoc()) {
		if(!$c) {
			if($c) {
				$c['users'] = $hash_users;
				commit($c);
			}
			$current_user = $pn['owner'];
			$c = array(
				'id'=>$pn['conversation_id'],
				'name'=>'%-SYSTEM%',
				'timecreated'=>$pn['timesent'],
				'creator'=>$pn['u_from'],
				'users'=>array(),
				'users_unread'=>array(),
				'messages'=>array());
		}
		#if($pn['u_from'] == 0 or $pn['u_to'] == 0) {
		#}
		$msg = array(
			'uid'=>$pn['u_from'],
			'timeadded'=>$pn['timesent'],
			'message'=>"[b][u]".$pn['topic']."[/u][/b]\n".$pn['message']);
		$hash = md5($pn['u_from'].'-'.$pn['u_to'].$pn['message']);
		if($hash != $last_hash) {
			$c['messages'][] = $msg;
			$hash_users = array();
			if($pn['owner']) {
				$hash_users[] = $pn['owner'];
				if(!$pn['u_read'] and !in_array($pn['owner'], $c['users_unread']))
					$c['users_unread'][] = $pn['owner'];
			}
			$last_hash = $hash;
		}
		elseif($pn['owner'] and !in_array($pn['owner'], $hash_users))
			$hash_users[] = $pn['owner'];
	}
	if($c) {
		$c['users'] = $hash_users;
		commit($c);
	}
}









function mode2($pns) {
	$c = NULL;
	$last_hash = NULL;
	$hash_users = array();
	while($pn = $pns->fetch_assoc()) {
		if(!$c or $c['id'] != $pn['conversation_id']) {
			if($c) {
				$c['users'] = $hash_users;
				commit($c);
			}
			$c = array(
				'id'=>$pn['conversation_id'],
				'name'=>$pn['topic'],
				'timecreated'=>$pn['timesent'],
				'creator'=>$pn['u_from'],
				'users'=>array(),
				'users_unread'=>array(),
				'messages'=>array());
		}
		$msg = array(
			'uid'=>$pn['u_from'],
			'timeadded'=>$pn['timesent'],
			'message'=>$pn['message']);
		$hash = md5($pn['u_from'].'-'.$pn['u_to'].$pn['message']);
		if($hash != $last_hash) {
			$c['messages'][] = $msg;
			$hash_users = array();
			if($pn['owner']) {
				$hash_users[] = $pn['owner'];
				if(!$pn['u_read'] and !in_array($pn['owner'], $c['users_unread']))
					$c['users_unread'][] = $pn['owner'];
			}
			$last_hash = $hash;
		}
		elseif($pn['owner'] and !in_array($pn['owner'], $hash_users))
			$hash_users[] = $pn['owner'];
	}
	if($c) {
		$c['users'] = $hash_users;
		commit($c);
	}
}

?>
