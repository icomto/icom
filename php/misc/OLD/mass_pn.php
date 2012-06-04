<?php

require '../config.inc.php';
require '../functions.inc.php';

$FROM_USER_ID = 6551;
$TOPIC = 'Probleme mit Deiner Spende';
$MESSAGE = utf8_encode('(hi)

Leider hatte ich einige Probleme mit meinem PayPal Account so das ich jetzt nicht mehr an die Spenden rankomme.
Es wäre sehr nett von Dir wenn Du bei PayPal den Käuferschutz benutzt und das Geld zurückforderst. Dann hast Du wenigstens noch etwas von dem Geld und es ist nicht einfach nur wie im Klo runtergespült.
Der VIP Status und die Werbefreiheit bleiben Dir natürlich erhalten.

Hier eine kleine Anleitung wie du den Käuferschutz benutzt:
[list=1]
[*] Logge Dich in Deinen Account ein, suche die Buchung der Spende und klicke auf "Details".
[*] Dort findest Du einen ähnlichen Text wie diesen:
[quote]Ein Transaktionsproblem können Sie innerhalb von 45 Tagen nach der Zahlung unter "Konfliktlösungen" melden. Gibt es ein Problem mit dieser Transaktion? Hier klicken, um die Angelegenheit zu klären[/quote]
[*] Klicke auf "Hier klicken, um die Angelegenheit zu klären" (oder einem ähnlichen Text).
[*] Wähle auf der nächsten Seite "Käuferschutz" aus und klicke auf "Weiter".
[*] Wähle "Ich habe den erworbenen Artikel nicht erhalten." aus und auf "Weiter".
[*] Auf der nächsten Seite wähle aus dem Menü "Welcher Kategorie entspricht der gekaufte Artikel?" "Dienstleistung" aus.
Im Feld "Nachricht an Verkäufer verfassen" trägst Du in etwa folgendes ein:
[quote]Leider habe ich die versprochenen Dienstleistungen nicht erhalten und möchte deshalb meine Spende zurückziehen.[/quote]
[*] Fertig :)
[/list]

Wir hoffen Du nimmst Dir die Zeit das Geld zurückzufordern und entschuldigen uns vielmals für diese Unannehmlichkeiten.

Liebe Grüße Memento_Mori & Team');

$aa = db()->query("SELECT user_id FROM donations WHERE admin_id=$FROM_USER_ID");
while($a = $aa->fetch_assoc())
	user($FROM_USER_ID)->pn_new(array($a['user_id']), $TOPIC, $MESSAGE);

?>
