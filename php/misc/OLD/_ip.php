<?php

$BOT_ON_SET = true;
require "config.inc.php";
header("Content-Type: text/plain");

$ips = db()->query("SELECT ip, type, link_id FROM links_download_ips");
$rv1 = array();
$rv2 = array();
$rv3 = array();
while($ip = $ips->fetch_assoc()) {
	if(!isset($rv1[$ip['ip']])) {
		$rv1[$ip['ip']] = 1;
		$rv2[$ip['ip']] = array($ip['link_id']);
	}
	else {
		$rv1[$ip['ip']]++;
		$rv2[$ip['ip']][] = $ip['link_id'];
	}
	$rv3[$ip['link_id']] = $ip['type'];
}

arsort($rv1);
$i = 0;
foreach($rv1 as $k=>$v) {
	echo "$k ($v)\n";
	$ll = db()->query("SELECT code, links.id AS id, type, name FROM links LEFT JOIN link_hosts ON link_hosts.id=links.host_id WHERE links.id IN(".join(",", $rv2[$k]).") ORDER BY code");
	while($l = $ll->fetch_assoc()) {
		echo $l['code']."/".$rv3[$l['id']]."-".$l['id']."/".$l['type']."/".$l['name']."\n";
	}
	echo "\n";
	if(++$i > 100) break;
}

?>
