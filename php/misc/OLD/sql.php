<?php

require_once '../config.inc.php';
require_once '../init_session.inc.php';

if(@USER_ID != 1) die('AD');
db()->DEBUG = true;

echo '<pre>';
echo '<style>td { vertical-align:top; }</style>';

function show_profile($id) {
	#$aa = db()->query("SHOW PROFILES");
	#echo '<table>';
	#while($a = $aa->fetch_assoc()) print_r($a);
	#while($a = $aa->fetch_assoc()) echo '<tr><td>'.$a['Status'].'</td><td>'.$a['Duration'].'</td></tr>';
	#echo '</table>';
	
	$aa = db()->query("
		SELECT
			MIN(seq) seq,state,count(*) numb_ops,
			ROUND(SUM(duration),5) sum_dur, ROUND(AVG(duration),5) avg_dur,
			ROUND(SUM(cpu_user),5) sum_cpu, ROUND(AVG(cpu_user),5) avg_cpu
		FROM information_schema.profiling
		WHERE query_id = $id
		GROUP BY state
		ORDER BY seq");
	echo '<table>';
	while($a = $aa->fetch_assoc()) echo '<tr><td>'.$a['seq'].'</td><td>'.$a['state'].'</td><td align="right">'.$a['numb_ops'].'</td><td>'.$a['sum_dur'].'</td><td>'.$a['sum_cpu'].'</td></tr>';
	echo '</table>';
}

if(isset($_POST['data'])) {
	$qq = array_map('trim', explode(';', $_POST['data']));
	$i = 1;
	$tt = 0;
	foreach($qq as $q) {
		if(!$q) continue;
		if(preg_match('~^select~i', $q)) {
			$aaa = db()->query("EXPLAIN ".$q);
			echo '<table border="1"><tr><td>id</td><td>select_type</td><td>table</td><td>type</td><td>possible_keys</td><td>key</td><td>key_len</td><td>ref</td><td>rows</td><td>extra</td></tr>';
			/*echo sprintf("%3s %-11s %-14s %-8s %-20s %-11s %5s %-20s %-8s %s\n",
				'id', 'select_type', 'table', 'type', 'possible_keys', 'key', 'key_len', 'ref', 'rows', 'extra');*/
			while($a = $aaa->fetch_assoc())
				#echo sprintf("%3s %-11s %-14s %-8s %-20s %-11s %5s %-20s %8s %s\n",
				echo sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n",
					$a['id'], $a['select_type'], $a['table'], $a['type'], str_replace(',', "\n", $a['possible_keys']), $a['key'], $a['key_len'], $a['ref'], $a['rows'], $a['Extra']);
		}
		echo htmlspecialchars($q)."\n";
		flush();
		db()->query("SET profiling=1");
		#$t = get_militime();
		$last_rv = db()->direct_query($q, MYSQLI_USE_RESULT);
		mysqli_free_result($last_rv);
		#$t = sub_militime($t, get_militime());
		echo "ROWS: ".($last_rv ? $last_rv->num_rows : db()->affected_rows)."\n";
		echo "AFFECTED ROWS: ".db()->affected_rows."\n";
		echo "FOUND ROWS: ".db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num."\n";
		db()->query("SET profiling=0");
		$t = db()->query("SELECT SUM(duration) AS t FROM information_schema.profiling WHERE query_id=$i")->fetch_object()->t;
		$tt += $t;
		show_profile($i);
		$i += 2;
		echo "DURATION: ".$t."\n";
		echo '<hr>';
		flush();
	}
	echo "ALL QUERY DURATION: ".$tt."\n";
}

?><form method="post"><textarea name="data" style="width:99%;height:90%;"><?=htmlspecialchars(@$_POST['data']);?></textarea>
<input type="submit"></form></pre>