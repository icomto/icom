<?php

require_once '../config.inc.php';
require_once '../functions.inc.php';
header('Content-Type: text/plain');

$aa = db()->query("SELECT id, vote_ips FROM releases WHERE vote_ips!='' AND vote_ips REGEXP '[^0-9,]'");
while($a = $aa->fetch_assoc()) {
	echo $a['id'].' - '.$a['vote_ips']."\n";
	$old = explode_arr_list($a['vote_ips']);
	$new = array();
	foreach($old as $o) if(preg_match('~^\d+$~', $o)) $new[] = $o;
	$new = implode_arr_list($new);
	echo $a['id'].' - '.$new."\n";
	db()->query("UPDATE releases SET vote_ips='$new' WHERE id='".$a['id']."' LIMIT 1");
	echo "\n";
}

?> 