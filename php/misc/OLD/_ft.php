<?php

require "config.inc.php";
require "functions.inc.php";
db()->DEBUG = true;

$i = 0;
$aa = db()->query("SELECT id, hosts FROM titles");
while($a = $aa->fetch_assoc()) {
	$y = implode_arr_list_ft(explode_arr_list($a['hosts']));
	db()->query("UPDATE titles SET hosts='".db()->escape_string($y)."' WHERE id='".$a['id']."' LIMIT 1");
	if(++$i % 100 == 0) echo "$i / ".$aa->num_rows." items done - $y\n";
}
echo "$i / ".$aa->num_rows." items done\n";

?>


releases
	languages
	hosts			ID
	link_types
	
titles
	release_types
	hosts			ID
	link_types
	languages
	
categorys
	release_types
	hosts			ID
	link_types
	languages

