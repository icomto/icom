<?php

require "config.inc.php";
db()->DEBUG = true;

$i = 0;
$aa = db()->query("SELECT code, nfoname, nfo, nfoimage FROM releases");
while($a = $aa->fetch_assoc()) {
	if($a['nfoname'] or $a['nfo']) {
		db()->query("
			INSERT IGNORE INTO release_nfos
			SET
				code='".$a['code']."',
				nfoname='".es($a['nfoname'])."',
				nfo='".es($a['nfo'])."',
				nfoimage='".es($a['nfoimage'])."'");
		db()->query("UPDATE releases SET has_nfo=1 WHERE code='".$a['code']."' LIMIT 1");
	}
	if(++$i % 1000 == 0) echo "$i / ".$aa->num_rows."\n";
}

?>
