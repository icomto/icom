<?php

require '../config.inc.php';
require '../functions.inc.php';

$remove = array(LEVEL2_GROUPID, LEVEL2_HIDDEN_GROUPID);

$where = array();
foreach($remove as $id) $where[] = "FIND_IN_SET($id,groups)";
$xx = db()->query("SELECT user_id id, groups FROM users WHERE ".implode(" OR ", $where));
while($x = $xx->fetch_assoc()) {
	$old = $x['groups'];
	$x['groups'] = explode_arr_list($x['groups']);
	$new = array();
	foreach($x['groups'] as $g) if(!in_array($g, $remove)) $new[] = $g;
	$x['groups'] = $new;
	$has_public_groups = (db()->query("SELECT * FROM groups WHERE id IN (".implode_arr_list($x['groups']).") AND public=1")->num_rows > 0);
	if(!$has_public_groups) $x['groups'][] = USER_GROUPID;
	$x['groups'] = implode_arr_list($x['groups']);
	echo $x['id'].'   '.$old.' => '.$x['groups']."\n";
	db()->query("UPDATE users SET groups='".$x['groups']."' WHERE user_id='".$x['id']."' LIMIT 1");
}

?>
