<?php

require_once '../config.inc.php';
require_once '../init_session.inc.php';

if(!has_privilege('groupmanager')) die;

$user = db()->query("SELECT user_id, MD5(CONCAT(user_id, nick, email, pass, salt, lastlogin)) hash FROM users WHERE user_id='".(int)@$_GET['user_id']."' LIMIT 1")->fetch_assoc();
if(!$user) die('USER_NOT_FOUND');

echo '<pre>'.LS('Wenn Du den folgenden Link besuchst wirst Du automatisch bei %1% eingeloggt. Dieser Link ist bis zu Deinem 1. Login g&uuml;ltig.

http://%2%/email_notification/login/i/%3%/h/%4%/

&Auml;ndere Dein Passwort am besten sofort nachdem Du Dich eingeloggt hast.

Dein %5%-Team', SITE_NAME, SITE_DOMAIN, $user['user_id'], $user['hash'], SITE_NAME).'</pre>';

?>
