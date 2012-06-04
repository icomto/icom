<?php

require_once '../config.inc.php';
require_once '../functions.inc.php';
header('Content-Type: text/plain');

$aa = db()->query("SELECT id, status, notes FROM links WHERE id>=2662000 ORDER BY id");
$i = 0;
$j = 8833625;

while($a = $aa->fetch_assoc()) {
	$pack_id = $a['id'];
	$notes = explode("\n", $a['notes']);
	foreach($notes as $l) {
		$l = explode('||', trim($l));
		if(!@$l[1]) continue;
		db()->query("
			INSERT DELAYED INTO link_files
			SET
				pack_id=$pack_id,
				name='".es($l[0])."',
				link='".es($l[1])."',
				extra='".es(@$l[2])."',
				status='".$a['status']."'");
	}
	if(++$i % 100 == 0) echo $i.' / '.$aa->num_rows." - $pack_id\n";
}
echo $i.' / '.$aa->num_rows." ($j)\n";

?>
