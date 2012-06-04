<?php

require '../config.inc.php';
require '../functions.inc.php';

function create_graph($data, $legend = array(), $extra = '') {
	$args = array();
	for($i = 0, $num = count($data); $i < $num; $i++) {
		$args[] = 'gz'.$i.'='.str_replace('+', '_', base64_encode(gzcompress(implode(',', $data[$i]), 9)));
		if(@$legend[$i]) $args[] = 'legend'.$i.'='.urlencode($legend[$i]);
	}
	return '<img src="/tools/jpgraph/line.php?'.implode('&', $args).$extra.'">'."\n";
}


$data = array();
$aa = db()->query("SELECT i FROM _json ORDER BY t DESC LIMIT 1, 200");
while($a = $aa->fetch_assoc()) {
	$data[] = $a['i'];
}
$data[] = 0;
$data = array_reverse($data);
echo '<head><meta http-equiv="refresh" content="2; URL="></head><body>';
echo create_graph(array($data));

?>
