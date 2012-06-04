<?php

require '../config.inc.php';
require '../functions.inc.php';

function channel_offline(&$radio, $status) {
	db()->query("UPDATE radio SET online='0' WHERE channel='".es($radio['channel'])."' LIMIT 1");
	if($radio['online']) echo 'Checking channel '.$radio['channel'].' ... '.$status."\n";
}

$radios = db()->query("SELECT channel, host, port, online FROM radio");
while($radio = $radios->fetch_assoc()) {
	$data = @my_file_get_contents('http://'.$radio['host'].':'.$radio['port'].'/7.html');
	$data = mb_convert_encoding($data, 'UTF-8', 'WINDOWS-1252');
	if(!$data) {
		channel_offline($radio, 'no data returned');
		continue;
	}
	$data = explode(',', trim(preg_replace('~\s*</body>.*$~', ',', preg_replace('~^.*<body>\s*~', '', $data))));
	if(count($data) < 7) {
		channel_offline($radio, 'invalid data');
		continue;
	}
	if(!$data[1]) {
		channel_offline($radio, 'offline');
		continue;
	}
	
	db()->query("
		UPDATE radio
		SET
			online='1',
			lastonline=CURRENT_TIMESTAMP,
			listeners='".es($data[4])."',
			peaklisteners='".es($data[2])."',
			maxlisteners='".es($data[3])."',
			bitrate='".es($data[5])."',
			currentsong='".es($data[6])."'
		WHERE channel='".es($radio['channel'])."'
		LIMIT 1");
	if(!$radio['online']) echo 'Checking channel '.$radio['channel']." ... online\n";
}

?>
