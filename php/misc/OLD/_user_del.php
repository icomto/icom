<?php

require "config.inc.php";
require "functions.inc.php";
require "user.inc.php";
db()->DEBUG = true;

echo '<pre>';

$users = db()->query("SELECT id, email, lastvisit, time_on_page, points FROM users ORDER BY id");
while($user = $users->fetch_assoc()) {
	if(!preg_match('~('.implode("|", array_map("preg_quote", $TRASH_MAILS)).')$~i' ,$user['email'])) continue;
	
	echo user($user['id'])->html(0)."\t  ".$user['email']." ".$user['lastvisit']." ".$user['time_on_page']." ".$user['points']."\n";
	/*send_pn(0, $user['id'], "WICHTIG! Sperrung deines Accounts!", "Hallo,

Du hat eine sogenannte \"Trash\" Email Addresse in deinem Profil angegeben.
Bitte aendere das bis zum 10. Mai, ansonsten wird dein Account gesperrt.

Mit freundlichen Gruessen
Dein iLoad.to-Team", false);*/
}

echo '</pre>';

?>
