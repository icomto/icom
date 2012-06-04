<?php

require '../config.inc.php';

$aa = db()->query("SELECT section_id FROM forum_sections ORDER BY section_id");
while($a = $aa->fetch_assoc())
	m_forum_global::fix_stats($a['section_id']);

?>
