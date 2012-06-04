<?php

require_once "../config.inc.php";
header("Content-Type: text/plain");

$i = 0;
echo "query ... ";
$s = db()->query("SELECT code FROM links_down GROUP BY code");
echo "done\n";
while($t = $s->fetch_assoc()) {
	$release = db()->query("SELECT id FROM releases WHERE code='".$t['code']."' LIMIT 1")->fetch_assoc();
	db()->query("UPDATE links_down SET release_id='".$release['id']."' WHERE code='".$t['code']."'");
	if(++$i % 100 == 0) echo $i.' / '.$s->num_rows.' - '.$t['code']."\n";
}
echo $i.' / '.$s->num_rows.' - '.$t['code']."\n";

?> 
