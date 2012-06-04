<?php

require_once "config.inc.php";
header("Content-Type: text/plain");

db()->DEBUG = true;
db()->close();

$q = "
SELECT links.id AS id, link_hosts.name AS host, link_hosts.type AS type, links.clicks AS clicks, links.status AS status, links.notes AS notes
FROM links
LEFT JOIN link_hosts ON link_hosts.id=links.host_id
WHERE links.code='acpsdftgfdxa' AND (link_hosts.type='Download' OR link_hosts.type='1part') AND generation='0' AND NOT status='off'
ORDER BY link_hosts.name, IF(link_hosts.type='Download',0,1)
";

$q1 = "
SELECT links.id AS id, link_hosts.name AS host, link_hosts.type AS type, links.clicks AS clicks, links.status AS status, links.notes AS notes
FROM links
LEFT JOIN link_hosts ON link_hosts.id=links.host_id
WHERE links.code='acpsdftgfdxa' AND (link_hosts.type='Download' OR link_hosts.type='1part') AND links.generation='0' AND NOT links.status='off'
ORDER BY link_hosts.name, IF(link_hosts.type='Download',0,1)
";

$q2 = "
SELECT links2.id AS id, link_hosts.name AS host, link_hosts.type AS type, links2.clicks AS clicks, links2.status AS status, links2.notes AS notes
FROM links2
LEFT JOIN link_hosts ON link_hosts.id=links2.host_id
WHERE links2.code='acpsdftgfdxa' AND (link_hosts.type='Download' OR link_hosts.type='1part') AND links2.generation='0' AND NOT links2.status='off'
ORDER BY link_hosts.name, IF(link_hosts.type='Download',0,1)
";

/*explain($q1);
explain($q2);
$a = 0;
$b = 0;
for($i = 0; $i < 5; $i++) {
	$a += single($q1, 100, "MyISAM: ");
	$b += single($q2, 100, "InnoDB: ");
}
echo "A: $a\n";
echo "B: $b\n";
echo "   ".($a + $b)."\n";*/

/*
select
	myisam
		0,72261
		0,715226
		0,745306
		0,698213
		3,500565
	innodb
		0,721749
		0,696569
		0,694748
		0,692025
		3,476707
join
	myisam
		4,12113
		4,256511
		4,123005
		4,449946
	innodb
		4,145771
		4,502506
		4,137127
		4,495521
order
	myisam
		15,447875
	innodb
		15,328085
*/

$q = "SELECT COUNT(*) AS num FROM users WHERE UNIX_TIMESTAMP(lastvisit)>'%s'";
function feed(&$q) { return str_replace("%s", time() - 330, $q); }
#0.64590525
#0.630328

#$q = "SELECT COUNT(*) AS num FROM users WHERE lastvisit>'%s'";
#function feed(&$q) { return str_replace("%s", date("Y-m-d H:i:s", time() - 330), $q); }
#0.8714982 

explain(feed($q));
$sum = 0;
for($i = 0; $i < 20; $i++) $sum += single(feed($q), 1000);
echo ($sum / $i)." <<\n";

function single($q, $iter, $pref = "") {
	$tt = 0;
	for($i = 0; $i < $iter; $i++) {
		#db()->connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB, MYSQL_PORT, MYSQL_SOCKET);
		db()->time_last_query = get_militime();
		$rv = db()->query($q);
		$t = sub_militime(db()->time_last_query, get_militime());
		$tt += $t;
		#db()->close();
	}
	echo sprintf("$pref%-8s\n", $tt);
	flush();
	return $tt;
}

function multi($q, $iter, $pref = "") {
	$tt = 0;
	for($i = 0; $i < $iter; $i++) {
		db()->connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB, MYSQL_PORT, MYSQL_SOCKET);
		db()->time_last_query = get_militime();
		$rv = db()->multi_query($q);
		do db()->store_result();
		while(db()->next_result());
		$t = sub_militime(db()->time_last_query, get_militime());
		$tt += $t;
		$x = array();
		#while($a = $rv->fetch_assoc()) $x[] = $a['thread_id'];
		db()->close();
		#echo sprintf("%8s: %-8s (%s)\n", $i + 1, $t, implode(",", $x));
	}
	echo sprintf("$pref%-8s\n", $tt);
	flush();
	return $tt;
}

function explain($q) {
	db()->connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB, MYSQL_PORT, MYSQL_SOCKET);
	$tq = "EXPLAIN ".str_replace(";", "; EXPLAIN ", $q);
	$tq = preg_replace("~EXPLAIN[ \r\n\t]*DELETE FROM [^;]+;?[ \r\n\t]*~s", "", $tq);
	$tq = preg_replace("~ EXPLAIN[ \r\n\t]*$~", "", $tq);
	$mrv = db()->multi_query($tq);
	do {
		$rv = db()->store_result();
		$a = $rv->fetch_assoc();
		$e = @$a['Extra'];
		unset($a['Extra']);
		unset($a['id']);
		$rv = array();
		foreach($a as $k=>$v) $rv[] = "$k: $v";
		echo join(", ", $rv)."\n$e\n";
	}
	while(db()->next_result());
	flush();
}

?>
