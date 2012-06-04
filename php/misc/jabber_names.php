<?php

require_once '../config.inc.php';
require_once '../functions.inc.php';

$aa = db()->query("SELECT user_id, nick FROM users");
while($a = $aa->fetch_assoc()) create_jabber_id($a['user_id'], $a['nick']);

?>
