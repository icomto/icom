<?php

require_once "config.inc.php";
set_time_limit(0);
header("Content-Type: text/plain");

$p1 = db()->query("SELECT id, topic, u_from, u_to, owner, conversation_id FROM pns WHERE owner=u_from AND conversation_id=0 ORDER BY id ASC");
while($r1 = $p1->fetch_assoc()) {
	if(preg_match('~^Re: ~', $r1['topic'])) {
		echo "Skpped weil ein arsch - ".$r1['topic']."\n";
		continue;
	}
	$conversation_id = db()->query("SELECT MAX(conversation_id) AS num FROM pns")->fetch_object()->num + 1;
	$r2 = db()->query("SELECT id, topic, u_from, u_to, owner, conversation_id FROM pns WHERE owner=u_to AND u_from='".$r1['owner']."' AND id IN (".($r1['id'] - 1).",".($r1['id'] + 1).") AND topic='".es($r1['topic'])."' LIMIT 1")->fetch_assoc();
	echo sprintf("a: %5s - %4s : %4s : %4s - %s\n",  $r1['id'], $r1['owner'], $r1['u_from'], $r1['u_to'], $r1['topic']);
	if($r2) echo sprintf("a: %5s - %4s : %4s : %4s - %s\n",  $r2['id'], $r2['owner'], $r2['u_from'], $r2['u_to'], $r2['topic']);
	db()->query("UPDATE pns SET conversation_id='$conversation_id' WHERE id='".$r1['id']."' LIMIT 1");
	if($r2) db()->query("UPDATE pns SET conversation_id='$conversation_id' WHERE id='".$r2['id']."' LIMIT 1");
	$p2 = db()->query("SELECT id, topic, u_from, u_to, owner, conversation_id FROM pns WHERE id>'".$r1['id']."' AND topic='Re: ".es($r1['topic'])."' AND ((u_from='".$r1['u_from']."' AND u_to='".$r1['u_to']."') OR (u_from='".$r1['u_to']."' AND u_to='".$r1['u_from']."')) ORDER BY id ASC");
	while($r3 = $p2->fetch_assoc()) {
		echo sprintf("a: --> %5s - %4s : %4s : %4s - %s\n",  $r3['id'], $r3['owner'], $r3['u_from'], $r3['u_to'], $r3['topic']);
		db()->query("UPDATE pns SET conversation_id='$conversation_id' WHERE id='".$r3['id']."' LIMIT 1");
	}
	echo "\n";
}

$p1 = db()->query("SELECT id, topic, u_from, u_to, owner, conversation_id FROM pns WHERE owner=u_to AND conversation_id=0 ORDER BY id ASC");
while($r1 = $p1->fetch_assoc()) {
	if(preg_match('~^Re: ~', $r1['topic'])) {
		echo "Skpped weil ein aRsCh - ".$r1['topic']."\n";
		continue;
	}
	$conversation_id = db()->query("SELECT MAX(conversation_id) AS num FROM pns")->fetch_object()->num + 1;
	$r2 = db()->query("SELECT id, topic, u_from, u_to, owner, conversation_id FROM pns WHERE owner=u_from AND u_to='".$r1['owner']."' AND id IN (".($r1['id'] - 1).",".($r1['id'] + 1).") AND topic='".es($r1['topic'])."' LIMIT 1")->fetch_assoc();
	echo sprintf("b: %5s - %4s : %4s : %4s - %s\n",  $r1['id'], $r1['owner'], $r1['u_from'], $r1['u_to'], $r1['topic']);
	if($r2) echo sprintf("b: %5s - %4s : %4s : %4s - %s\n",  $r2['id'], $r2['owner'], $r2['u_from'], $r2['u_to'], $r2['topic']);
	db()->query("UPDATE pns SET conversation_id='$conversation_id' WHERE id='".$r1['id']."' LIMIT 1");
	if($r2) db()->query("UPDATE pns SET conversation_id='$conversation_id' WHERE id='".$r2['id']."' LIMIT 1");
	$p2 = db()->query("SELECT id, topic, u_from, u_to, owner, conversation_id FROM pns WHERE id>'".$r1['id']."' AND topic='Re: ".es($r1['topic'])."' AND ((u_from='".$r1['u_from']."' AND u_to='".$r1['u_to']."') OR (u_from='".$r1['u_to']."' AND u_to='".$r1['u_from']."')) ORDER BY id ASC");
	while($r3 = $p2->fetch_assoc()) {
		echo sprintf("b: --> %5s - %4s : %4s : %4s - %s\n",  $r3['id'], $r3['owner'], $r3['u_from'], $r3['u_to'], $r3['topic']);
		db()->query("UPDATE pns SET conversation_id='$conversation_id' WHERE id='".$r3['id']."' LIMIT 1");
	}
	echo "\n";
}

do {
	$p1 = db()->query("SELECT id, topic, u_from, u_to, owner, conversation_id FROM pns WHERE conversation_id=0 AND NOT (owner=0 AND u_from=1 AND u_to=1) ORDER BY id ASC");
	echo "ROWS: ".$p1->num_rows."\n";
	while($r1 = $p1->fetch_assoc()) {
		if(!preg_match('~^Re: ~', $r1['topic'])) {
			echo sprintf("c: SKIPPED %5s - %4s : %4s : %4s - %s\n",  $r1['id'], $r1['owner'], $r1['u_from'], $r1['u_to'], $r1['topic']);
			continue;
		}
		$conversation_id = db()->query("SELECT MAX(conversation_id) AS num FROM pns")->fetch_object()->num + 1;
		$r2 = db()->query("SELECT id, topic, u_from, u_to, owner, conversation_id FROM pns WHERE owner=u_to AND u_from='".$r1['owner']."' AND id IN (".($r1['id'] - 1).",".($r1['id'] + 1).") AND topic='".es($r1['topic'])."' LIMIT 1")->fetch_assoc();
		if(!$r2) $r2 = db()->query("SELECT id, topic, u_from, u_to, owner, conversation_id FROM pns WHERE owner=u_from AND u_to='".$r1['owner']."' AND id IN (".($r1['id'] - 1).",".($r1['id'] + 1).") AND topic='".es($r1['topic'])."' LIMIT 1")->fetch_assoc();
		
		echo sprintf("c: %5s - %4s : %4s : %4s - %s\n",  $r1['id'], $r1['owner'], $r1['u_from'], $r1['u_to'], $r1['topic']);
		if($r2) echo sprintf("c: %5s - %4s : %4s : %4s - %s\n",  $r2['id'], $r2['owner'], $r2['u_from'], $r2['u_to'], $r2['topic']);
		db()->query("UPDATE pns SET conversation_id='$conversation_id' WHERE id='".$r1['id']."' LIMIT 1");
		if($r2) db()->query("UPDATE pns SET conversation_id='$conversation_id' WHERE id='".$r2['id']."' LIMIT 1");
		$p2 = db()->query("SELECT id, topic, u_from, u_to, owner, conversation_id FROM pns WHERE id>'".$r1['id']."' AND topic='".es($r1['topic'])."' AND ((u_from='".$r1['u_from']."' AND u_to='".$r1['u_to']."') OR (u_from='".$r1['u_to']."' AND u_to='".$r1['u_from']."')) ORDER BY id ASC");
		while($r3 = $p2->fetch_assoc()) {
			echo sprintf("c: --> %5s - %4s : %4s : %4s - %s\n",  $r3['id'], $r3['owner'], $r3['u_from'], $r3['u_to'], $r3['topic']);
			db()->query("UPDATE pns SET conversation_id='$conversation_id' WHERE id='".$r3['id']."' LIMIT 1");
		}
		echo "\n";
		break;
	}
}
while($p1->num_rows);

//DELETE FROM pns WHERE conversation_id=0

?>
