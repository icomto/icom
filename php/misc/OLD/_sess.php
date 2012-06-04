<?php

require_once "config.inc.php";
require_once "functions.inc.php";
header("Content-Type: text/plain");

$s = db()->query("SELECT * FROM sessions_innodb WHERE id='526c10c5c9de4e31aba0f503b3e703e3' ORDER BY lasttime DESC LIMIT 10");
while($t = $s->fetch_assoc()) {
	$t['data'] = unserialize($t['data']);
	print_r($t);
}

?>
