<?php

$BOT_ON_SET = true;
require_once "config.inc.php";
header("Content-Type: text/plain");

$s = db()->query("SELECT code FROM releases");
while($t = $s->fetch_assoc()) {
	$num = db()->query("SELECT SUM(clicks) AS num FROM links_down WHERE code='".$t['code']."'")->fetch_object()->num;
	db()->query("UPDATE releases SET num_dead_clicks='$num' WHERE code='".$t['code']."' LIMIT 1");
	if(db()->affected_rows) echo sprintf("%-20s %6s\n", $t['code'], $num);
}

?>
