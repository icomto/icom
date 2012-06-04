<?php

require "init_session.inc.php";
#header("Content-Type: text/plain");
db()->DEBUG = true;

echo "<pre>";

echo date("Y-m-d H:i:s", time())."\n";
echo db()->query("SELECT COUNT(*) AS num FROM users WHERE UNIX_TIMESTAMP(lastvisit)>".(time() - 330))->fetch_object()->num."\n";
echo db()->query("SELECT COUNT(*) AS num FROM users WHERE lastvisit>'".date("Y-m-d H:i:s", time() - 330)."'")->fetch_object()->num."\n";

echo "<table border='1'>";
$aa = db()->query("SHOW PROCESSLIST");
while($a = $aa->fetch_assoc())
	echo "<tr><td>".$a['Id']."</td><td>".$a['Command']."</td><td>".$a['Time']."</td><td>".$a['State']."</td><td>".$a['Info']."</td></tr>";
echo "</table></pre>";

?>
