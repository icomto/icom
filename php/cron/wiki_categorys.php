<?php

require_once '../config.inc.php';
require_once '../functions.inc.php';
require_once '../class/wikicode.php';

$categorys = array();
$rv = db()->query("
	SELECT wiki_pages.name AS name, wiki_pages.lang AS lang, wiki_history.content AS content
	FROM wiki_pages
	LEFT JOIN wiki_history ON wiki_history.id=wiki_pages.history
	WHERE wiki_pages.history");
while($r = $rv->fetch_assoc()) {
	$ws = wiki_struct::init($r['name'], $r['content']);
	foreach($ws->categorys as $c) if(!in_array(trim($c), $categorys)) $categorys[] = array(trim($c), $r['lang']);
}

db()->query("TRUNCATE TABLE wiki_categorys");
foreach($categorys as $c) db()->query("INSERT IGNORE INTO wiki_categorys SET name='".db()->escape_string($c[0])."', lang='".db()->escape_string($c[1])."'");

?>
