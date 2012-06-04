<?php

require_once '../config.inc.php';

$aa = db()->query("
	select a.release_id, a.generation, count(a.id) as num
	from links a, releases b, link_hosts c
	where
		b.id=a.release_id and
		c.id=a.host_id and
		a.status='on' and
		b.reup=0 and
		c.type='Download'
	group by a.release_id, a.generation
	order by a.release_id, a.generation");
$cur_release_id = 0;
$best = 0;
while($a = $aa->fetch_assoc()) {
	if($cur_release_id != $a['release_id']) {
		if($cur_release_id) {
			if($best < 5) {
				db()->query("UPDATE releases SET reup=1 WHERE id='$cur_release_id' LIMIT 1");
				echo "FUCK $cur_release_id - $best\n";
			}
		}
		$cur_release_id = $a['release_id'];
		$best = 0;
	}
	if($best < $a['num']) $best = $a['num'];
}

?>
