<?php

$BOT_ON_SET = true;
require_once "../config.inc.php";
require_once "../functions.inc.php";
require_once "../update.inc.php";
header("Content-Type: text/plain");

$categorys = array();
$languages = array();
$aa = db()->query("
	SELECT id, languages
	FROM releases");
while($a = $aa->fetch_assoc()) {
	$langs = explode_arr_list($a['languages']);
	$new = array();
	foreach($langs as $l) {
		if(!$l) continue;
		switch($l) {
		case 'DE': $l = 'GER'; break;
		case 'EN': $l = 'ENG'; break;
		}
		if(!in_array($l, $new)) $new[] = $l;
	}
	sort($new);
	$langs = implode_arr_list($new);
	if($langs != $a['languages']) {
		echo $a['id'], ' - ', $a['languages'], ' - ', $langs, "\n";
		db()->query("UPDATE releases SET languages='$langs' WHERE id='".$a['id']."' LIMIT 1");
		#update_title($t['title']);
	}
}

?>
