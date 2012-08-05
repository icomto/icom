<?php

class i__i {
	public static function hash($data) {
		$digest = sha1($data, true);
		$hash = 0;
		for($i = 0, $len = strlen($digest); $i < $len; $i++) {
			$c = ord(substr($digest, $i, 2));
			$hash ^= ($c & 0xFF) << (8*(($i/2) % 8));
		}
		return $hash;
	}
}

?>
