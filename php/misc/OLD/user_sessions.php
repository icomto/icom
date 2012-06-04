<?php

require_once "../config.inc.php";
header("Content-Type: text/plain");

$s = db()->query("SELECT id, data FROM user_sessions");
while($t = $s->fetch_assoc()) {
	echo $t['id']."\n";
	db()->query("UPDATE user_sessions SET data_bin=0x".bin2hex(gzcompress($t['data']))." WHERE id='".$t['id']."' LIMIT 1");
}

?> 