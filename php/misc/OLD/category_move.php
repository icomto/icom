<?php

require_once "../config.inc.php";
require_once "../init_session.inc.php";
require_once "../update.inc.php";

switch(@$_POST['a']) {
default:
	die('<pre><form method="post">
		<input type="hidden" name="a" value="validate">
		<input type="text" style="width:70%" name="from">
		<input type="text" style="width:70%" name="to">
		<button type="submit">OK</button>
		</form></pre>');
case 'validate':
	if(preg_match('~/category/(\d+)~', $_POST['from'], $out)) $from = $out[1];
	else $from = (int)$_POST['from'];
	if(preg_match('~/category/(\d+)~', $_POST['to'], $out)) $to = $out[1];
	else $to = (int)$_POST['to'];
	if(!$from or !$to) die('ERROR');
	function my_chain($id) {
		return implode(' -&gt; ', array_map(function($i) { return sprintf('%-45s', $i); }, category_generate_namechain($id)));
	}
	die('<pre><form method="post">
		<input type="hidden" name="a" value="execute">
		<input type="hidden" name="from" value='.$from.'">'.my_chain($from).'
		<input type="hidden" name="to" value='.$to.'">'.my_chain($to).'
		<button type="submit">OK</button>
		</form></pre>');
case 'execute':
	header("Content-Type: text/plain");
	$from = (int)$_POST['from'];
	$to = (int)$_POST['to'];
	if(!$from or !$to) die('ERROR');
	$s = db()->query("SELECT id, name_de, name_en FROM titles WHERE category='$from'");
	while($t = $s->fetch_assoc()) {
		echo $t['id']." ".$t['name_de']." / ".$t['name_en']."\n";
		db()->query("
			UPDATE titles
			SET ".get_title_category_chain_update_query($to)."
			WHERE id='".$t['id']."'
			LIMIT 1");
		update_title($t['id']);
	}
	update_category($from);
	update_category($to);
	die;
}

?>
