<?php

lang::$LANGUAGE_PRIORITY = array('en', 'de');

G::$LANG_NAMES = array(
	'de' => 'German',
	'en' => 'English');
G::$LANG_TIME = array(
	'ONE'				=> array('one', 'one', 'one', 'one', 'one', 'one', 'one'),
	'SINGULAR'			=> array('second', 'minute', 'hour', 'day', 'week', 'month', 'year'),
	'PLURAL'			=> array('seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years'),
	'NOW'				=> array('gerade eben', 'jetzt'),
	'MORE_THAN_X_YEARS'	=> 'more than %s years',
	'MODE_1'			=> array('just now', '%s ago', 'in %s'),
	'MODE_2'			=> array('now', 'since %s', 'in %s')
);
G::$LANG_JUST_NOW = array('0 seconds ago', 'now');

?>
