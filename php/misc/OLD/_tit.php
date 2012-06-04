<?php

require "../php/config.inc.php";
require "../php/functions.inc.php";
header("Content-Type: text/plain");
db()->DEBUG = true;

$aa = db()->query("SELECT id, name FROM titles WHERE category='".es($_GET['cat'])."' ORDER BY name");
while($a = $aa->fetch_assoc()) {
	echo "http://iload.to/title/".$a['id']."-".urlenc($a['name'])."/\n";
}

?>
