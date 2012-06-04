<?php

require "config.inc.php";
require "cache.inc.php";
require "ilphp4.inc.php";

$_COOKIE['lang'] = "de";
define("TEMPLATE_DIRECTORY", ".");

for(;;) {
	echo "fetching ... ";
	flush();
	$tpl = new ilphp("_ilphp.ilp", 10);
	$tpl->cache_with_fallback = true;
	if(!$tpl->cache_load()) $tpl->ilphp_fetch();
	unset($GLOBALS['tpl']);
	unset($tpl);
	echo "done\n";
	sleep(1);
}

?>
