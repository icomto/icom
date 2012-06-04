<?php

class sphinx {
	public static function compile_words($words, $fields = array()) {
		$words = strtolower($words);
		$words = preg_replace('~[\.\-_]~', ' ', $words);
		$words = preg_replace('~(")~', ' \1 ', $words);
		$words = preg_replace('~(:)~', '\1 ', $words);
		$words = explode(' ', $words);
		$words = array_map('trim', $words);
		$words = array_filter($words, function($v) {
			return $v or !preg_match('~^[^a-z0-9]*$~i', $v);
		});
		
		$w = '';
		$new = array();
		foreach($words as $word) {
			if($word == '"') {
				if(!$w) $w = ' ';
				else {
					$new[] = trim($w);
					$w = '';
				}
			}
			elseif($w) $w .= $word.' ';
			else $new[] = $word;
			#if
		}
		if($w) $new[] = $w;
		$words = $new;
		
		$block = array('*', array());
		$blocks = array();
		if($fields) $cmd_regex = '~^('.implode('|', array_map('preg_quote', array_keys($fields))).')\:$~';
		foreach($words as $word) {
			if($fields and preg_match($cmd_regex, $word, $out) or preg_match('~^(\*|@)$~', $word, $out)) {
				$blocks[] = $block;
				$block = array($fields[$out[1]], array());
				continue;
			}
			if(mb_strlen($word) > 1)
				$block[1][] = '"*'.es($word).'*" ';
		}
		if($block[1]) $blocks[] = $block;
		if(!$blocks) return;
		return implode(' ', array_map(function($block) {
			if(!$block[1]) return '';
			switch($block[0]) {
			default:
				return '@('.$block[0].') ('.implode(' ', $block[1]).')';
			case '*':
				return '@'.$block[0].' ('.implode(' ', $block[1]).')';
			case 'year':
				return '@('.$block[0].') ('.implode(' | ', $block[1]).')';
			}
		}, $blocks));
	}
}

?>
