<?php

require "../php/config.inc.php";
header("Content-Type: text/plain");

db()->DEBUG = true;
$i = 0;
$aa = db()->query("SELECT * FROM sessions");
while($a = $aa->fetch_assoc()) {
	$f = array();
	foreach($a as $k=>$v) $f[] = "$k='".db()->escape_string($v)."'";
	db()->query("INSERT LOW_PRIORITY IGNORE INTO sessions_innodb SET ".join(",", $f));
	if(++$i % 1000 == 0) echo "$i / ".$aa->num_rows."\n";
}

?>
