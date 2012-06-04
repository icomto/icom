<?php

require "../config.inc.php";
require "../init_session.inc.php";

if(!has_privilege('user_warnings') or !has_privilege('forum_super_mod')) die;
#header("Content-Type: text/plain");
#db()->DEBUG = true;
echo "<pre>Das Teil hier geht nach den IPs der Benutzer. Es werden also auch zb. Familienmitglieder
oder Leute die den selben Proxy nutzen angezeigt.
Das hier ist also eher ein Anhaltspunkt als ein echter Beweis f&uuml;r nen Doppelaccount.
Die verschiedenen Zeiten k&ouml;nnen oft noch mehr hinweise auf nen Doppelaccount geben (zb. Account
A wurde um 20:20 gebannt, Account B wurde um 20:25 erstellt).

";

$user_id = (int)@$_GET['user_id'];
if(!$user_id) die('Usage: blubb <user_id>');

$x = db()->query("SELECT ip FROM user_sessions WHERE user_id='$user_id'")->fetch_assoc();
if(!$x) die('User not found?!');
$xx = db()->query("SELECT user_id FROM user_sessions WHERE ip='".$x['ip']."'");
echo sprintf('%19s / %19s / %19s / %s<br>', 'Registrierung', 'Aktion', 'Besuch', 'Benutzer');
while($x = $xx->fetch_assoc()) {
	#echo $x['ip']."\n";
	$a = db()->query("SELECT * FROM users WHERE user_id='".$x['user_id']."' LIMIT 1")->fetch_assoc();
	echo sprintf('%19s / %19s / %19s / %s<br>', $a['regtime'], $a['lastaction'], $a['lastvisit'], user($x['user_id'])->html(-1));
	echo "\n";
}

/*$reason = utf8_encode("Hallo,

Dein Account wurde aufgrund von Inaktivität gesperrt.
Wenn Du deinen Account wieder aktivieren möchtest schreib uns über das Kontaktformular auf iLoad eine Nachricht.
Um das zu tun musst du einen anderen Computer benutzen.

Mit freundlichen Grüßen
iLoad.to");

$users = db()->query("SELECT id, nick, groups, regtime, lastvisit FROM `users` where regtime<'2010-05-12' and not regtime<lastvisit");
while($user = $users->fetch_assoc()) {
	$points = db()->query("SELECT SUM(points) AS num FROM warnings WHERE uid='".$user['id']."' AND (NOT timeending OR timeending>CURRENT_TIMESTAMP)")->fetch_object()->num;
	if($points >= 100) continue;
	db()->query("INSERT INTO warnings SET warner=1, uid='".$user['id']."', points=100, reason='".db()->escape_string($reason)."'");
	check_warning_points($user['id'], $user['groups']);
	echo sprintf("%10s %10s\n", $user['id'], $points);
}*/

?>
