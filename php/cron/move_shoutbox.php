<?php

require_once '../config.inc.php';
header('Content-Type: text/plain');

$shouts = db()->query("
	SELECT *
	FROM shoutbox_de
	ORDER BY id DESC
	LIMIT 400, 999999");
echo "Shouts found: ".$shouts->num_rows."\n";
while($shout = $shouts->fetch_assoc()) {
	$ins = array();
	foreach($shout as $k=>$v) $ins[] = "$k='".db()->escape_string($v)."'";
	echo "Moving shout DE ".$shout['id']." form ".$shout['timeadded']." ... ";
	db()->query("INSERT INTO shoutbox_de_archive SET ".join(",", $ins));
	db()->query("DELETE FROM shoutbox_de WHERE id='".$shout['id']."' LIMIT 1");
	echo "done\n";
}

$shouts = db()->query("
	SELECT *
	FROM shoutbox_en
	ORDER BY id DESC
	LIMIT 400, 999999");
echo "Shouts found: ".$shouts->num_rows."\n";
while($shout = $shouts->fetch_assoc()) {
	$ins = array();
	foreach($shout as $k=>$v) $ins[] = "$k='".db()->escape_string($v)."'";
	echo "Moving shout EN ".$shout['id']." form ".$shout['timeadded']." ... ";
	db()->query("INSERT INTO shoutbox_en_archive SET ".join(",", $ins));
	db()->query("DELETE FROM shoutbox_en	WHERE id='".$shout['id']."' LIMIT 1");
	echo "done\n";
}

?>
