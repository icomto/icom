<?php

require_once 'config.inc.php';

$type = ($_GET['type'] == 'i' ? 'image' : 'thumb');
db()->query("UPDATE images SET hits_$type=hits_$type+1 WHERE id='".es($_GET['id'])."' LIMIT 1");
touch('../s/0/'.$_GET['prefix'].'/'.$_GET['id']);

switch($_GET['ext']) {
case 'png': header('Content-Type: image/png'); break;
case 'jpg':
case 'jpeg': header('Content-Type: image/jpeg'); break;
}

die(file_get_contents('../s/'.$_GET['type'].'/'.$_GET['prefix'].'/'.$_GET['id'].'.'.$_GET['ext']));

?>
