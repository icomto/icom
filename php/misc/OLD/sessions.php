<?php

require '../config.inc.php';
require '../functions.inc.php';

header('Content-Type: text/plain');

$ASDF = explode_arr_list('denie_entrance,adminmode,developermode,scat,viewed_posts,translate,next_title_ratings_data_update_1,next_title_ratings_data_update_33,next_title_ratings_data_update_7,next_title_ratings_data_update_0,i_am_jesus_-1,i_am_jesus_0,i_am_jesus_1,i_am_jesus_2,nick,has_shop_access,modmode,search,sdesc,linkmode,guest_uid,user_points,languages,display_signatures,groups,lastusersettingsload,privileges');


$i = 0;
$j = 0;
$k = 0;
$aa = db()->query("SELECT user_id, data FROM user_sessions ORDER BY RAND()");
while($a = $aa->fetch_assoc()) {
	$len = strlen($a['data']);
	$a['data'] = @unserialize(gzuncompress($a['data']));
	print_r($a['data']);
	die;
	if(!$a['data']) {
		echo 'Error loading data from '.$a['user_id']."\n";
		continue;
	}
	if(@$a['data']['viewed_posts']) {
		foreach($a['data']['viewed_posts'] as $id) {
			db()->query("INSERT LOW_PRIORITY IGNORE INTO forum_threads_visited_users SET thread_id='".$id."', user_id='".$a['user_id']."'");
			$j += db()->affected_rows;
		}
	}
	$changed = 0;
	foreach($ASDF as $A) {
		if(isset($a['data'][$A])) {
			unset($a['data'][$A]);
			echo 'Removed '.$A."\n";
			$changed++;
		}
	}
	if($changed) {
		$k++;
		#$k += $changed;
		$a['data'] = bin2hex(gzcompress(serialize($a['data'])));
		db()->query("UPDATE LOW_PRIORITY user_sessions SET data=0x".$a['data']." WHERE user_id='".$a['user_id']."' LIMIT 1");
	}
	if(++$i % 100 == 0) echo $i.' / '.$aa->num_rows.' ('.$j.' - '.$k.")\n";
}
echo $i.' / '.$aa->num_rows.' ('.$j.' - '.$k.")\n";


$i = 0;
$j = 0;
$k = 0;
$aa = db()->query("SELECT id, data FROM guest_sessions");
while($a = $aa->fetch_assoc()) {
	$len = strlen($a['data']);
	$a['data'] = @unserialize(gzuncompress($a['data']));
	if(!$a['data']) {
		echo 'Error loading data from '.$a['id']."\n";
		continue;
	}
	if(@$a['data']['viewed_posts']) {
		foreach($a['data']['viewed_posts'] as $id) {
			db()->query("INSERT LOW_PRIORITY IGNORE INTO forum_threads_visited_guests SET thread_id='".$id."', guest_id='".db()->escape_string($a['id'])."'");
			$j += db()->affected_rows;
		}
	}
	$changed = 0;
	foreach($ASDF as $A) {
		if(isset($a['data'][$A])) {
			unset($a['data'][$A]);
			echo 'Removed '.$A."\n";
			$changed++;
		}
	}
	if($changed) {
		$k++;
		#$k += $changed;
		$a['data'] = bin2hex(gzcompress(serialize($a['data'])));
		db()->query("UPDATE LOW_PRIORITY guest_sessions SET data=0x".$a['data']." WHERE id='".db()->escape_string($a['id'])."' LIMIT 1");
	}
	if(++$i % 100 == 0) echo $i.' / '.$aa->num_rows.' ('.$j.' - '.$k.")\n";
}
echo $i.' / '.$aa->num_rows.' ('.$j.' - '.$k.")\n";

?>
