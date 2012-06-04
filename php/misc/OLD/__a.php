<?php

$BOT_ON_SET = true;
require_once "config.inc.php";
require_once "functions.inc.php";

echo '<pre>';
$s = db()->query("select uid as a from warnings where points<=4 group by uid");
while($t = $s->fetch_assoc()) {
	echo user($t['a'])->html()."\n";
}

?>
