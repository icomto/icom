<?php

set_time_limit(0);
require "../config.inc.php";
header("Content-Type: text/plain");

db()->DEBUG = true;
$i = 0;

$aa = db()->query("SELECT code, notes FROM releases");
while($a = $aa->fetch_assoc()) {
	$update = array();
	if($a['notes']) $update[] = "notes=0x".bin2hex(gzcompress($a['notes']));
	$update = implode(',', $update);
	if($update) db()->query("INSERT INTO release_data SET code='".$a['code']."', $update ON DUPLICATE KEY UPDATE $update");
	db()->query("
		UPDATE releases
		SET has_notes='".($a['notes'] ? '1' : '0')."'
		WHERE code='".$a['code']."'");
	if(++$i % 1000 == 0) echo "$i / ".$aa->num_rows."\n";
}

?>
