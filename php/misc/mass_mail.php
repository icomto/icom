<?php

require_once '../config.inc.php';

$aa = db()->query("
	SELECT user_id, nick, email, MD5(CONCAT(user_id, nick, email, pass, salt, lastvisit)) hash
	FROM users
	WHERE
		emails_allowed=1 AND
		email_sent=0 AND
		validated=1 AND
		email NOT LIKE '-%' AND
		email NOT LIKE '%@aol.com' AND
		email NOT LIKE '%@web.de' AND
		lastvisit<'2012-04-23 00:00:00' AND
		FIND_IN_SET('de', languages)
	ORDER BY user_id");
while($a = $aa->fetch_assoc()) {
	echo $a['user_id'].' ... '.$a['hash'].' ... ';
	if(!filter_var($a['email'], FILTER_VALIDATE_EMAIL)) {
		echo "skipped\n";
		continue;
	}
	imail::mail(
		$a['email'],
		html_entity_decode('iLoad.to wird zu iCom.to', ENT_COMPAT, 'UTF-8'),
		html_entity_decode('Hallo (ex) iLoader '.$a['nick'].',

leider ist die iLoad.to &Auml;ra zuende gegangen, wir w&uuml;rden uns aber freuen Dich auf der neuen Seite, iCom.to begr&uuml;&szlig;en zu d&uuml;rfen.

iCom.to bietet keine Links zu Downloads oder Streams an, sondern ist eine reine Communityseite.
Du kannst Dich mit Deinen alten iLoad Login-Daten bei iCom.to einloggen.

Hier ist ein direkter Link zu iCom.to, der Dich direkt in Deinen Account einloggt.
http://icom.to/email_notification/login/i/'.$a['user_id'].'/h/'.$a['hash'].'/

Wenn Du keine E-Mails mehr von uns bekommen m&ouml;chtest besuche bitte den unten stehenden Link:
http://icom.to/email_notification/disallow/i/'.$a['user_id'].'/h/'.$a['hash'].'/

Zu Deiner Information: Die zwei oben stehenden Links sind nur bis zu deinem n&auml;chsten Login g&uuml;ltig.

Beste Gr&uuml;&szlig;e,
Dein iCom.to-Team', ENT_COMPAT, 'UTF-8'),
		'From: '.SITE_NAME.' <'.NOREPLY_EMAIL.">\n");
	db()->query("UPDATE users SET email_sent=1 WHERE user_id='".$a['user_id']."' LIMIT 1");
	echo "done\n";
}

?>
