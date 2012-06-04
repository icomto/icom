<?php

require_once '../config.inc.php';
require_once '../functions.inc.php';
header('Content-Type: text/plain');

function parse_comment_rating_sub($class, $i, $id, $user_id, $value, $time = NULL) {
	$value = round($value);
	if(!$id or !$user_id or $value < 1) return;
	if($value > 10) $value = 10;
	switch($class) {
	default:
		return;
	case 'title':
		$rating_table = 'title_ratings';
		$count_table = 'titles';
		$count_table_prefix = 'rating';
		break;
	case 'release':
		return;
		$rating_table = 'release_ratings'.$i;
		$count_table = 'releases';
		$count_table_prefix = 'rating'.$i;
		break;
	}
	$rating = db()->query("
		SELECT value
		FROM $rating_table
		WHERE rating_id='$id' AND user_id='$user_id'
		LIMIT 1")->fetch_assoc();
	if($rating) {
		db()->query("
			UPDATE $rating_table
			SET value=".$value.", timevoted=".($time ? "'$time'" : CURRENT_TIMESTAMP)."
			WHERE rating_id='$id' AND user_id='$user_id'
			LIMIT 1");
		db()->query("
			UPDATE $count_table
			SET ".$count_table_prefix."_value=(".$count_table_prefix."_value-".$rating['value'].")+".$value."
			WHERE id='$id'
			LIMIT 1");
	}
	else {
		db()->query("
			INSERT INTO $rating_table
			SET rating_id='$id', user_id='$user_id', value=".$value.", timevoted=".($time ? "'$time'" : CURRENT_TIMESTAMP));
		db()->query("
			UPDATE $count_table
			SET
				".$count_table_prefix."_num=".$count_table_prefix."_num+1,
				".$count_table_prefix."_value=".$count_table_prefix."_value+".$value."
			WHERE id='$id'
			LIMIT 1");
	}
}
function parse_comment_rating($release_id, $user_id, $timeadded, $comment) {
	if(preg_match('~(film|movie)[:\s]+(\d+([\.,]\d+)?)\s*[\-/]\s*(\d+([\.,]\d+)?)~i', $comment, $out)) {
		$v = str_replace(',', '.', $out[2]);
		$s = str_replace(',', '.', $out[4]);
		$r = @($v/$s)*10;
		$title = db()->query("SELECT title FROM releases WHERE id='$release_id' LIMIT 1")->fetch_assoc();
		if($title) parse_comment_rating_sub('title', 0, $title['title'], $user_id, $r, $timeadded);
		echo $out[1].': '.$r."\n";
	}
	if(preg_match('~(ton|audio|sound)[:\s]+(\d+([\.,]\d+)?)\s*[\-/]\s*(\d+([\.,]\d+)?)~i', $comment, $out)) {
		$v = str_replace(',', '.', $out[2]);
		$s = str_replace(',', '.', $out[4]);
		$r = @($v/$s)*10;
		parse_comment_rating_sub('release', 1, $release_id, $user_id, $r, $timeadded);
		echo $out[1].': '.$r."\n";
	}
	if(preg_match('~(bild|picture|image)[:\s]+(\d+([\.,]\d+)?)\s*[\-/]\s*(\d+([\.,]\d+)?)~i', $comment, $out)) {
		$v = str_replace(',', '.', $out[2]);
		$s = str_replace(',', '.', $out[4]);
		$r = @($v/$s)*10;
		parse_comment_rating_sub('release', 2, $release_id, $user_id, $r, $timeadded);
		echo $out[1].': '.$r."\n";
	}
}

$i = 0;
$aa = db()->query("SELECT release_id, uid, comment, timeadded FROM release_comments ORDER BY id");
while($a = $aa->fetch_assoc()) {
	parse_comment_rating($a['release_id'], $a['uid'], $a['timeadded'], $a['comment']);
	if(++$i % 100 == 0) echo $i.' / '.$aa->num_rows."\n";
}
echo $i.' / '.$aa->num_rows."\n";

?>
