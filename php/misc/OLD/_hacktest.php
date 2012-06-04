<?php

require 'config.inc.php';
header("Content-Type: text/plain");

set_time_limit(0);
require 'functions.inc.php';
require 'update.inc.php';
update_category(0);
die;



function hacktest($v) {
	$s = '[/\*\+ \t\n]*';
	$e = '~["\']?'.$s.'and'.$s.'[01]'.$s.'='.$s.'[01]["\']?~i';
	if(preg_match($e, $v))
		return true;
}

$i = 0;
$aa = db()->query("SELECT content AS a FROM forum_posts");
while($a = $aa->fetch_assoc()) {
	if(++$i % 100 == 0) {
		echo "$i\n";
		flush();
	}
	if(hacktest($a['a'])) echo "$v = FUUUUUU\n";
}

#$test = array("asdfjklsajf 'and/**/1/**/=+0");
#foreach($test as $v) if(hacktest($v)) echo "$v = FUUUUUU\n";

?>
