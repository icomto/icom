<?php

require_once '../config.inc.php';
require_once '../functions.inc.php';

$aa = db()->query("SELECT * FROM users ".(@$argv[1] ? " WHERE user_id='".$argv[1]."'" : ""));
while($a = $aa->fetch_assoc()) {
	$salt = md5($a['nick'].$a['email'].mt_rand().mt_rand().mt_rand().mt_rand().mt_rand());
	$plain = (@$argv[2] ? $argv[2] : random_string(10, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqurstuvwxyz'));
	$pass = md5($plain.$salt);
	echo $a['user_id']." - ".$a['nick']." - $plain\n";
	db()->query("UPDATE users SET salt='$salt', pass='$pass' WHERE user_id='".$a['user_id']."' LIMIT 1");
	imail::mail($a['email'], 'Neues Password fuer icom.to', 'Hallo,

Du kannst Dich ab sofort mit diesem Passwort einloggen: '.$plain.'

Mit freundlcihen Gruessen
Das iCom.to-Team',
'From: iCom.to <'.NOREPLY_EMAIL.">\r\n");
}
