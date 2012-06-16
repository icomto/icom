<?php
/*
while true
do
	rm -f '/tmp/pinba.request'
	rm -f '/tmp/pinba.request.min'
	rm -f '/tmp/pinba.request.max'
	mysql -ppinba -Dpinba <<EOF
select concat('INSERT INTO __pinba_script_name SET script_name="',script_name,'", req_count=',count(id),', req_time=',avg(req_time),';') from request where id not between `cat /tmp/pinba.request.min 2>/dev/null||echo 0` and `cat /tmp/pinba.request.min 2>/dev/null||echo 0` group by script_name into outfile '/tmp/pinba.request';
select min(id) from request into outfile '/tmp/pinba.request.min';
select id from request order by id limit 1 into outfile '/tmp/pinba.request.max';
EOF
	mysql -hmysql -Diload -ub1 -pasfjlagvjdklafaa545awfs -A </tmp/pinba.request
	sleep 30
done



select t, name n, if(avg(web_get)>0.8,0.8,avg(web_get)) v from _balancer where t>'2011-04-08 00:00:00' and name not in ('a1','b1') group by name, round(unix_timestamp(t)/(10*60)) order by t;
select t, script_name n, req_time v from __pinba_script_name where script_name in ('module.title','module.release','json.idle');

select script_name, req_count, req_time_total/req_count rt from report_by_script_name where req_count>100 order by rt;
*/

#define('MYSQL_HOST', 'localhost'); define('MYSQL_USER', 'root'); define('MYSQL_PASS', 'pinba'); define('MYSQL_DB', 'pinba'); define('MYSQL_PORT', 3306); define('MYSQL_SOCKET', '/var/run/mysqld/mysqld.sock');
require_once '../config.inc.php';
#require_once '../init.inc.php';
#require_once '../init_session.inc.php';

#if(@USER_ID != 1) die('AD');
db()->DEBUG = true;

set_time_limit(0);

define('TEMPDIR', '/tmp');

if(isset($_GET['get'])) {
	$tempid = preg_replace('~[^0-9]~', '', $_GET['get']);
	$srl = cache_L2::get('sql-chart-'.$tempid);
	#$srl = file_get_contents($tempfile.'.srl');
	#unlink($tempfile.'.srl');
	switch($_GET['type']) {
	case 'xml':
		header('Content-Type: text/xml');
		echo $xml;
	case 'srl':
		$srl = unserialize($srl);
		require_once ('../../tools/jpgraph/src/jpgraph.php');
		require_once ('../../tools/jpgraph/src/jpgraph_bar.php');
		require_once ('../../tools/jpgraph/src/jpgraph_line.php');
		$graph = new Graph((int)@$_GET['width'] ? (int)$_GET['width'] : 1000, (int)@$_GET['height'] ? (int)$_GET['height'] : 550);
		$graph->SetScale('intlin');
		#$graph->SetScale('intint');
		$graph->xaxis->SetTextLabelInterval(4);
		$graph->xaxis->SetTickLabels($srl['t']);
		$graph->xaxis->SetLabelAngle(45);
		unset($srl['t']);
		ksort($srl);
		$colors = array(
			'red', 'darkgreen', 'blue', 'orange', 'aquamarine', 'lightgreen');
		foreach($srl as $k=>$v) {
			#$v = array_reverse($v);
			$line = new LinePlot($v);
			$graph->add($line);
			#$line->SetStepStyle();
			$line->SetLegend($k);
			$line->SetColor(current($colors));
			next($colors);
		}
		$graph->Stroke();
	}
	die;
}

?><pre>
<style>td { vertical-align:top; }</style><?php

if(isset($_POST['data'])) {
	//create table _sql_query_data ( name varchar(100) not null primary key, data longtext not null );
	db()->query("INSERT INTO _sql_query_data SET name='".es($_POST['name'])."', data='".es($_POST['data'])."' ON DUPLICATE KEY UPDATE data='".es($_POST['data'])."'");
	
	db()->multi_query($_POST['data']);
	$temp = array();
	do $temp[] = db()->store_result();
	while(db()->next_result());
	$aa = array_pop($temp);
	foreach($temp as $a) if(is_object($a)) $a->free();
	
	$rows = array('t'=>array());
	$last = array();
	$keys = NULL;
	while($a = $aa->fetch_assoc()) {
		if(!$keys) {
			$keys = array_keys($a);
			print_r($keys);
		}
		$t = $a['t'];
		$n = $a['n'];
		#$rows['t'][] = $t;
		if(!isset($rows[$n])) $rows[$n] = array();
		$rows[$n][$t] = $a['v'];
	}
	unset($rows['t']);
	$keys = array_keys($rows);
	$times = array();
	foreach($keys as $n) {
		foreach($rows[$n] as $t=>$v) {
			foreach($keys as $m) {
				if($n == $m) continue;
				if(!isset($rows[$m][$t])) $rows[$m][$t] = PHP_INT_MAX;
			}
			$times[$t] = 1;
		}
	}
	foreach($keys as $n) ksort($rows[$n]);
	$new = array();
	foreach($keys as $n) {
		$new[$n] = array();
		foreach($rows[$n] as $v)
			$new[$n][] = $v;
	}
	$rows = array('t'=>array_keys($times));
	sort($rows['t']);
	foreach($keys as $n) {
		$last = 0;
		$next = PHP_INT_MAX;
		$rows[$n] = array();
		for($i = 0, $num = count($new[$n]); $i < $num; $i++) {
			if($new[$n][$i] == PHP_INT_MAX) {
				for($j = $i + 1; $j < $num; $j++) {
					if($new[$n][$j] != PHP_INT_MAX) {
						$next = $new[$n][$j];
						break;
					}
				}
				if($next == PHP_INT_MAX) {
					if($last == PHP_INT_MAX) $next = 0;
					else $next = $last;
				}
				if($last == PHP_INT_MAX) $last = $next;
				$v = ($last + $next)/2;
			}
			else {
				$v = $new[$n][$i];
				$last = $v;
				$next = PHP_INT_MAX;
			}
			$rows[$n][] = $v;
		}
		unset($new[$n]);
	}
	/*$new = array();
	foreach($keys as $n) $new[$n] = array()
	foreach($keys as $n) {
		if($n == 't' or $n == 'n') continue;
		
	}*/
	echo '<button onclick="document.getElementById(\'DATA\').style.display=\'block\';">Show data</button><br>';
	echo '<div id="DATA" style="display:none;">';
	print_r($rows);
	echo '</div>';
	/*echo '<table id="DATA" style="*display:none;" border="1">';
	echo '<tr>';
	echo '<th>t</th>';
	$keys = array_keys($rows);
	foreach($keys as $k) echo '<th>'.$k.'</th>';
	echo '</tr>';
	for($i = 0, $num = count($rows[$keys[0]]); $i < $num; $i++)
	foreach($rows as $k=>$v) {
		echo '<tr>';
		echo '<td>'.$k.'</td>';
		echo '<td>'.$v.'</td>';
		echo '</tr>';
	}
	echo '</table>';*/
	
	
	/*echo '<button onclick="document.getElementById(\'DATA\').style.display=\'block\';">Show data</button><br>';
	echo '<table id="DATA" style="display:none;" border="1">';
	$first = true;
	while($a = $aa->fetch_assoc()) {
		echo '<tr>';
		if($first) {
			foreach($a as $k=>$v) echo '<th>'.$k.'</th>';
			echo '</tr><tr>';
			$first = false;
		}
		foreach($a as $k=>$v) {
			if(!isset($rows[$k])) $rows[$k] = array();
			$rows[$k][] = $v;
			echo '<td>'.$v.'</td>';
		}
		echo '</tr>';
	}
	echo '</table>';*/
	#print_r($rows);
	
	$tempid = time();
	cache_L2::set('sql-chart-'.$tempid, 60, serialize($rows));
	echo '<img src="sql-chart.php?get='.$tempid.'&amp;type=srl" width="1000" height="550">';
}

$a = db()->query("SELECT * FROM _sql_query_data WHERE name='".es(@$_POST['name'] ? $_POST['name'] : @$_GET['name'])."' LIMIT 1")->fetch_assoc();
?><form method="post" action="?name=<?=urlencode(@$a['name'])?>"><input type="text" size="80" name="name" value="<?=htmlspecialchars(@$a['name'])?>">
<textarea name="data" style="width:99%;height:90%;"><?=htmlspecialchars(@$a['data']);?></textarea>
<input type="submit"></form>
<?
$aa = db()->query("SELECT name FROM _sql_query_data ORDER BY name");
while($a = $aa->fetch_assoc())
	echo '<a href="?name='.urlencode($a['name']).'">Query: '.htmlspecialchars($a['name']).'</a><br>';
?></pre>
