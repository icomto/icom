<?php

function ilphp_cache_delete($file, $all = false, $cache_time = -1, $cache_id = '', $cache_method = 'L2') {
	$tpl = new ilphp($file, $cache_time, $cache_id, $cache_method);
	$tpl->cache_delete($all);
}
function ilphp_cache_delete2($file, $cache_id = '', $cache_method = 'L2') {
	$tpl = new ilphp($file, 0, $cache_id, $cache_method);
	$cache_file = $tpl->_cache_file;
	$from = '%LANG'.LANG.'LANG%';
	$to = array(
		$from,
		'%LANG'.'de'.'1'.'LANG%',
		'%LANG'.'en'.'1'.'LANG%',
		'%LANG'.'de'.''.'LANG%',
		'%LANG'.'en'.''.'LANG%');
	foreach($to as $t) {
		$tpl->_cache_file = str_replace($from, $t, $cache_file);
		$tpl->cache_delete(false);
	}
}

class ilphp {
	use ilphp_trait;
	public function __construct($file = '', $cache_time = -1, $cache_id = '', $cache_method = 'L2') {
		$this->ilphp_construct($file, $cache_time, $cache_id, $cache_method);
	}
}

?>
