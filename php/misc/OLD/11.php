<?php

#user_chat_content.id = 49980

require_once "config.inc.php";
header("Content-Type: text/plain");

$aa = db()->query("delete from user_chat_content where subid=140");
$aa = db()->query("select * from shoutbox_de_archive");
$i = 0;
while($a = $aa->fetch_assoc()) {
	db()->query("insert into user_chat_content set subid=140, uid='".$a['uid']."', timeadded='".$a['timeadded']."', message='".db()->escape_string($a['message'])."'");
	if(++$i % 100) echo $i.' / '.$aa->num_rows."\n";
}
echo $i.' / '.$aa->num_rows."\n";

?>
