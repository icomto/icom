<?php

require "config.inc.php";
require "functions.inc.php";
require "user.ban.inc.php";
#header("Content-Type: text/plain");
db()->DEBUG = true;
echo "<pre>";

$i = 0;
$xx = db()->query("SELECT ip FROM sessions_innodb WHERE ip GROUP BY ip");
while($x = $xx->fetch_assoc()) {
	$i++;
	if(!preg_match('~^\d+\.\d+\.\d+\.\d+$~', $x['ip'])) continue;
	$n = encrypt_ip($x['ip']);
	db()->query("UPDATE LOW_PRIORITY sessions_innodb SET ip='$n' WHERE ip='".$x['ip']."'");
	if(db()->affected_rows) {
		echo sprintf("%7s / %7s - %-16s => %s\n", $i, $xx->num_rows, $x['ip'], $n);
		flush();
	}
}

?>
