<?php

require "../../tools/image_merge.inc.php";

header("Content-Type: text/plain");

$o = new optimize_css;
$o->run('_', 16, 10000);

?>
