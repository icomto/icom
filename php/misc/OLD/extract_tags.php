<?php

require '../config.inc.php';
require '../functions.inc.php';

$category_id = 1;

db()->query("DELETE FROM category_genres WHERE id=$category_id");
$aa = db()->query("SELECT genrelist FROM titles WHERE category_p1=$category_id AND genrelist!=''");
$i = 0;
while($a = $aa->fetch_assoc()) {
	$list = explode_arr_list($a['genrelist']);
	foreach($list as $l) {
		if(!$l) continue;
		db()->query("
			INSERT INTO category_genres
			SET
				id=$category_id,
				name='".db()->escape_string($l)."'
			ON DUPLICATE KEY UPDATE
				num=num+1");
	}
	if(++$i % 100 == 0) echo $i.' / '.$aa->num_rows."\n";
}
echo $i.' / '.$aa->num_rows."\n";

?>
