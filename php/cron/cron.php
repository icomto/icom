<?php

function _exec($cmd) {
	echo $cmd."\n";
	#echo '-- START --> '.$cmd."\n";
	system($cmd);
	#echo '-- STOP --> '.$cmd."\n";
}

for(;;) {
	$t = time();
	if($t % 10 == 0) _exec('nice php update_radio.php');
	if($t % (15*60) == 0) _exec('nice php move_shoutbox.php');
	if($t % (1*60*60) == 0) _exec('nice php wiki_categorys.php');
#	if($t % (2*60*60) == 0) _exec('nice php autoban.php');
	sleep(1);
}

?>
