<?php

require_once "config.inc.php";

$rv = db()->query("SELECT id, email FROM users WHERE id>11 ORDER BY id");
while($r = $rv->fetch_assoc()) {
echo sprintf("%5s %-30s ... ", $r['id'], $r['email']);
flush();
mail($r['email'], "Rapidshare.com Accounts guenstig wie nie!",
"Hallo,

ab jetzt gibt es bei uns 30-Tage Rapidshare.com Accounts fuer nur 2,80 EUR bis 2,50 EUR.

2,50 EUR kostet ein Account wenn wenn Du mit PaySafe Card (http://www.paysafecard.de) bezahlst,
2,80 EUR wenn Du mit PayPal bezahlst.
Dieses Angebot gilt nur bis zum 5. Juli - also schnell zugreifen!

Naehere Informationen findest Du unter http://iload.to/shop/
PayPal ist nur verfuegbar wenn Du Dich in Deinen Account einloggst.

Mit freundlichen Gruessen
Dein iLoad.to-Team",
"From: noreply@iload.to");
echo "done\n";
}

?>
