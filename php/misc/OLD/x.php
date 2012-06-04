<?php

require_once '../config.inc.php';
header('Content-Type: text/plain');

$num = 43;
$i = 0;

$time_window = 1000;
$user_ms = $time_window/$num;

function get_ms_timestamp() {
	$t = explode(' ', microtime());
	return round((($t[0] + $t[1]) - 1295565000)*1000);
}

function get_next_time($T, $t, $N, $n) {
	$t = get_ms_timestamp() % $T;
	return round($T + (($n*($T/$N)) - $t));
}

calc_update_val(1, 1000, "SELECT COUNT(*) AS num FROM users WHERE UNIX_TIMESTAMP(lastvisit)>".(time() - 60));
function calc_update_val($id, $time_window, $calc_num_query) {
	$TW = 1000;
	$t = get_ms_timestamp();
	$ajax = db()->query("SELECT * FROM ajax_update LIMIT 1")->fetch_assoc();
	if(!$ajax) {
		db()->query("INSERT IGNORE INTO ajax_update SET id=1");
		$ajax = db()->query("SELECT * FROM ajax_update LIMIT 1")->fetch_assoc();
	}
	if($t - $ajax['Tc'] > $time_window) {
		if(db()->query("SELECT IS_USED_LOCK('calc_update_val_$id') AS used")->fetch_object()->used)
			return $time_window;
		db()->query("SELECT GET_LOCK('calc_update_val_$id',120)");
		$ajax['N'] = round(db()->query($calc_num_query)->fetch_object()->num);
		$ajax['Tc'] = $t;
		$ajax['i'] = 1;
		db()->query("UPDATE ajax_update SET Tc='".$ajax['Tc']."', N='".$ajax['N']."', i=1 WHERE id=1");
		db()->query("SELECT RELEASE_LOCK('calc_update_val_$id',120)");
	}
	else {
		$ajax['i']++;
		db()->query("UPDATE ajax_update SET i=i+1 WHERE id=1");
	}
	return round($time_window + (($ajax['i']*($time_window/$ajax['N'])) - $t));
}

for(; $i < $num; $i++) {
	static $first = 0;
	$t = get_ms_timestamp();
	if(!$first) $first = $t;
	$t -= $first;
	
	$tw = $t % $time_window;
	
	$user_togo = $num - $i;
	$time_left = $time_window - $tw;
	
	echo $tw.' - ';
	echo $user_ms.' - ';
	echo $user_togo.' - ';
	echo $time_left.' - ';
	$next = $time_window + (($i*$user_ms) - $t);
	echo $next.' - ';
	echo get_next_time($time_window, $num, $i).' - ';
	
	echo "\n";
	flush();
	usleep(17000);
}

?>
