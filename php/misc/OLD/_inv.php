<?php

$BOT_ON_SET = true;
require "config.inc.php";
require "functions.inc.php";
header("Content-Type: text/plain");

for($i = 0; $i < 20; $i++) echo create_invite_code()." ";

?>
