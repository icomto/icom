<?php

include "config.inc.php";
header("Content-Type: text/plain");
db()->DEBUG = true;





function backup() {
	db()->multi_query(utf8_encode("-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 07. April 2010 um 06:46
-- Server Version: 5.1.30
-- PHP-Version: 5.2.8

SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";

--
-- Datenbank: `iload09`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tree`
--
DROP TABLE IF EXISTS tree;
CREATE TABLE IF NOT EXISTS `tree` (
  `ID` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `lft` int(12) unsigned NOT NULL,
  `rgt` int(12) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=29 ;

--
-- Daten für Tabelle `tree`
--

INSERT INTO `tree` (`ID`, `name`, `lft`, `rgt`) VALUES
(1, '1 Lebewesen', 1, 56),
(2, '1 Säugetiere', 2, 11),
(3, '1 Halbaffen', 3, 4),
(4, '2 Affen', 5, 10),
(5, '2 Nagetiere', 12, 53),
(6, '1 Hamster', 13, 14),
(7, '2 Kaninchen', 15, 16),
(8, '3 Reptilien', 54, 55),
(9, '1 Schimpansen', 6, 7),
(10, '2 Menschen', 8, 9),
(11, '3 Mäuse', 17, 18),
(12, '4 Ratten', 19, 52),
(13, '1 Bisonratten', 20, 21),
(14, '2 Drecksviecher', 22, 51),
(15, '1 argjire', 23, 24),
(16, '2 gerijg', 25, 26),
(17, '3 hgjireghoerajgfo', 27, 46),
(18, '4 gejfisf', 47, 48),
(19, '5 grejiglsaf', 49, 50),
(20, '1 grejiglsaf', 28, 29),
(21, '2 agerjigorjf', 30, 31),
(22, '3 gejrigej', 32, 33),
(23, '4 hrtjihg', 34, 35),
(24, '5 gesgjds', 36, 37),
(25, '6 ergjieajzh', 38, 39),
(26, '7 fgerjgeiuorajf', 40, 41),
(27, '8 gejgkldjlg', 42, 43),
(28, '9 gdfgjetitjgi', 44, 45);
"));
	while(db()->next_result());
	echo "backup done\n";
}



function nested_add($table, $prefix, $fields, $parent) {
	//rgt von root
	db()->query('LOCK TABLES '.$table.' WRITE');
	$rgt = db()->query('SELECT '.$prefix.'rgt AS rgt FROM '.$table.' WHERE '.$parent)->fetch_assoc();
	$rgt = ($rgt ? $rgt['rgt'] : 1);
	db()->query('UPDATE '.$table.' SET '.$prefix.'rgt='.$prefix.'rgt+2 WHERE '.$prefix.'rgt>='.$rgt);
	db()->query('UPDATE '.$table.' SET '.$prefix.'lft='.$prefix.'lft+2 WHERE '.$prefix.'lft>'.$rgt);
	db()->query('INSERT INTO '.$table.' SET '.$fields.', '.$prefix.'lft='.$rgt.', '.$prefix.'rgt='.($rgt+1));
	db()->query('UNLOCK TABLES');
	return db()->insert_id;
}

function display_rev($from) {
	return db()->query("
		SELECT
			tree.id AS id,
			tree.name AS name,
			tree.lft AS lft,
			tree.rgt AS rgt
		FROM tree, tree AS tree1
		WHERE
			tree1.lft BETWEEN tree.lft AND tree.rgt AND
			tree1.id=$from
		GROUP BY tree.id
		ORDER BY tree.lft");
}

function display($table, $prefix, $root = 1, $levels = 0) {
	$root = 1;
	$levels = 3;
	$q = 'SELECT
			'.$table.'.id AS id,
			'.$table.'.name AS name,
			'.$table.'.lft AS lft,
			'.$table.'.rgt AS rgt,
			COUNT(*)-1 AS level,
			ROUND(('.$table.'.'.$prefix.'rgt-'.$table.'.'.$prefix.'lft-1)/2) AS childs,
			(MIN('.$table.'.'.$prefix.'rgt)-'.$table.'.'.$prefix.'rgt-('.$table.'.'.$prefix.'lft>1))/2 > 0 AS lower
		FROM '.$table.', '.$table.' AS '.$table.'_1'.($root == 999999 ? '' : ', '.$table.' AS '.$table.'_2').'
		WHERE
			'.$table.'.'.$prefix.'lft BETWEEN '.$table.'_1.'.$prefix.'lft AND '.$table.'_1.'.$prefix.'rgt
			'.($root == 9999999 ? '' : 'AND '.$table.'.'.$prefix.'lft BETWEEN '.$table.'_2.'.$prefix.'lft AND '.$table.'_2.'.$prefix.'rgt AND '.$table.'_2.id='.$root).'
		GROUP BY '.$table.'.'.$prefix.'lft
		'.($levels ? 'HAVING  COUNT(*)-1<'.$levels : '').'
		ORDER BY '.$table.'.'.$prefix.'lft';
	explain($q);
	return db()->query($q);
}

function delete($id, $with_childs = true) {
	//rgt von root
	db()->query('LOCK TABLES tree WRITE');
	$r = db()->query('SELECT lft, rgt FROM tree WHERE id='.$id)->fetch_assoc();
	if($r) {
		if($with_childs) {
			db()->query('DELETE FROM tree WHERE lft BETWEEN '.$r['lft'].' AND '.$r['rgt']);
			db()->query('UPDATE tree SET lft=lft-'.($r['rgt']-$r['lft']+1).' WHERE lft>'.$r['rgt']);
			db()->query('UPDATE tree SET rgt=rgt-'.($r['rgt']-$r['lft']+1).' WHERE rgt>'.$r['rgt']);
		}
		else {
			db()->query('DELETE FROM tree WHERE lft='.$r['lft']);
			db()->query('UPDATE tree SET lft=lft-1, rgt=rgt-1 WHERE lft BETWEEN '.$r['lft'].' AND '.$r['rgt']);
			db()->query('UPDATE tree SET lft=lft-2 WHERE lft>'.$r['rgt']);
			db()->query('UPDATE tree SET rgt=rgt-2 WHERE rgt>'.$r['rgt']);
		}
	}
	db()->query('UNLOCK TABLES');
}

function move($table, $prefix, $id1, $id2, $behind = true) {
	if($id1 == 1 or $id1 == $id2) return;
	db()->query('LOCK TABLES '.$table.' WRITE');
	$r1 = db()->query('
		SELECT
			'.$prefix.'lft AS lft,
			'.$prefix.'rgt AS rgt,
			ROUND(('.$prefix.'rgt-'.$prefix.'lft-1)/2) AS childs
		FROM '.$table.'
		WHERE '.$id1)->fetch_assoc();
	$r2 = db()->query('SELECT 1 FROM '.$table.' WHERE '.$id2)->fetch_assoc(); //just to check if that id exists
	//todo: check if id2 is child of id1
	if($r1 and $r2) {
		//move id1 to the outback
		$max_diff = db()->query('SELECT '.$prefix.'rgt AS rgt FROM '.$table.' WHERE '.$prefix.'lft=1')->fetch_object()->rgt - $r1['lft'];
		db()->query('
			UPDATE '.$table.'
			SET
				'.$prefix.'lft='.$prefix.'lft+'.$max_diff.',
				'.$prefix.'rgt='.$prefix.'rgt+'.$max_diff.'
			WHERE
				'.$prefix.'lft BETWEEN '.$r1['lft'].' AND '.$r1['rgt']);
		
		//close the hole
		$diff = $r1['rgt']-$r1['lft']+1;
		db()->query('UPDATE '.$table.' SET '.$prefix.'lft='.$prefix.'lft-'.$diff.' WHERE '.$prefix.'lft>'.$r1['rgt']);
		db()->query('UPDATE '.$table.' SET '.$prefix.'rgt='.$prefix.'rgt-'.$diff.' WHERE '.$prefix.'rgt>'.$r1['rgt']);
		
		//calculate new lft of id1; we don't need rgt from that anymore
		$r1['lft'] += $max_diff;
		
		$diff = ($r1['childs']+1)*2;
		if($behind) {
			//create hole behind id2
			$rgt = db()->query('SELECT '.$prefix.'rgt FROM '.$table.' WHERE '.$id2)->fetch_object()->rgt;
			db()->query('UPDATE '.$table.' SET '.$prefix.'rgt='.$prefix.'rgt+'.$diff.' WHERE '.$prefix.'rgt>'.$rgt);
			db()->query('UPDATE '.$table.' SET '.$prefix.'lft='.$prefix.'lft+'.$diff.' WHERE '.$prefix.'lft>'.$rgt);
			
			//move id1 in the hole
			$diff = $r1['lft']-$rgt-1;
		}
		else {
			//create hole before id2
			$lft = db()->query('SELECT '.$prefix.'lft FROM '.$table.' WHERE '.$id2)->fetch_object()->lft;
			db()->query('UPDATE '.$table.' SET '.$prefix.'rgt='.$prefix.'rgt+'.$diff.' WHERE '.$prefix.'rgt>='.$lft);
			db()->query('UPDATE '.$table.' SET '.$prefix.'lft='.$prefix.'lft+'.$diff.' WHERE '.$prefix.'lft>='.$lft);
			
			//move id1 in the hole
			$diff = $r1['lft']-$lft;
		}
		db()->query('
			UPDATE '.$table.'
			SET
				'.$prefix.'lft='.$prefix.'lft-'.$diff.',
				'.$prefix.'rgt='.$prefix.'rgt-'.$diff.'
			WHERE
				'.$prefix.'lft>='.$r1['lft']);
	}
	db()->query('UNLOCK TABLES');
}

function move2($table, $prefix, $id1, $id2, $behind = true) {
	$r1 = db()->query('SELECT '.$prefix.'lft AS lft, '.$prefix.'rgt AS rgt FROM '.$table.' WHERE '.$id1)->fetch_assoc();
	$r2 = db()->query('SELECT '.$prefix.($behind ? 'rgt+1' : 'lft').' AS p FROM '.$table.' WHERE '.$id2)->fetch_assoc();
	db()->query('
		UPDATE '.$table.'
		SET
			'.$prefix.'lft='.$prefix.'lft+IF('.$r2['p'].'>'.$r1['rgt'].',
				IF('.$r1['rgt'].'<'.$prefix.'lft AND '.$prefix.'lft<'.$r2['p'].',
					'.$r1['lft'].'-'.$r1['rgt'].'-1,
					IF('.$r1['lft'].'<='.$prefix.'lft AND '.$prefix.'lft<'.$r1['rgt'].',
						'.$r2['p'].'-'.$r1['rgt'].'-1,0
					)
				),
				IF('.$r2['p'].'<='.$prefix.'lft AND '.$prefix.'lft<'.$r1['lft'].',
					'.$r1['rgt'].'-'.$r1['lft'].'+1,
					IF('.$r1['lft'].'<='.$prefix.'lft AND '.$prefix.'lft<'.$r1['rgt'].',
						'.$r2['p'].'-'.$r1['lft'].',0
					)
				)
			),
			'.$prefix.'rgt='.$prefix.'rgt+IF('.$r2['p'].'>'.$r1['rgt'].',
				IF('.$r1['rgt'].'<'.$prefix.'rgt AND '.$prefix.'rgt<'.$r2['p'].',
					'.$r1['lft'].'-'.$r1['rgt'].'-1,
					IF('.$r1['lft'].'<'.$prefix.'rgt AND '.$prefix.'rgt<='.$r1['rgt'].',
						'.$r2['p'].'-'.$r1['rgt'].'-1,0
					)
				),
				IF('.$r2['p'].'<='.$prefix.'rgt AND '.$prefix.'rgt<'.$r1['lft'].',
					'.$r1['rgt'].'-'.$r1['lft'].'+1,
					IF('.$r1['lft'].'<'.$prefix.'rgt AND '.$prefix.'rgt<='.$r1['rgt'].',
						'.$r2['p'].'-'.$r1['lft'].',0
					)
				)
			)
		WHERE '.$r1['rgt'].'<'.$r2['p'].' OR '.$r2['p'].'<'.$r1['lft']);
}
function swap2($id1, $id2) {
	$r1 = db()->query('SELECT lft, rgt FROM tree WHERE id='.$id1)->fetch_assoc();
	$r2	= db()->query('SELECT lft, rgt FROM tree WHERE id='.$id2)->fetch_assoc();
	db()->query('
		-- swaps two subtrees, where A is the subtree having the lower lgt/rgt values
		-- and B is the subtree having the higher ones
		-- @param al the lft of subtree A
		-- @param ar the rgt of subtree A, must be lower than bl
		-- @param bl the lft of subtree B, must be higher than ar
		-- @param br the rgt of subtree B
		UPDATE tree
		SET
			lft = lft + @offset := IF(lft > '.$r1['rgt'].' AND rgt < '.$r2['lft'].',
				'.$r2['rgt'].' - '.$r2['lft'].' - '.$r1['rgt'].' + '.$r1['lft'].',
				IF(lft < '.$r2['lft'].', '.$r2['rgt'].' - '.$r1['rgt'].', '.$r1['lft'].' - '.$r2['lft'].')
			),
			rgt = rgt + @offset
		WHERE lft >= '.$r1['lft'].' AND lft <= '.$r2['rgt'].' AND '.$r1['rgt'].' < '.$r2['lft'].'');
}

#backup();
#nested_add('tree', '', 'name="1 tfgjklsafe"', 'id=16');
#nested_add('tree', '', 'name="2 fgjlkragjrio"', 'id=16');
#add(utf8_encode("gdfgjetitjgi"), 17);
#delete(12, false);

//TODO: catch move into myself... move(17, 24);
#move('tree', '', 'id=22', 'id=17', true);

#move2('tree', '', 'id=16', 'id=2', false);
#swap2(3, 17);




/*db()->query('UPDATE categorys SET lft=0, rgt=0');
db()->query('UPDATE categorys SET lft=1, rgt=2 WHERE parent=9999999');

function shit_to_good($parent1, $parent2) {
	$rv = db()->query('SELECT id FROM categorys WHERE '.$parent1.' ORDER BY name');
	while($r = $rv->fetch_assoc()) {
		$rgt = db()->query('SELECT rgt FROM categorys WHERE '.$parent2)->fetch_assoc();
		$rgt = ($rgt ? $rgt['rgt'] : 1);
		db()->query('UPDATE categorys SET rgt=rgt+2 WHERE rgt>='.$rgt);
		db()->query('UPDATE categorys SET lft=lft+2 WHERE lft>'.$rgt);
		db()->query('UPDATE categorys SET lft='.$rgt.', rgt='.($rgt+1).' WHERE id='.$r['id']);
		shit_to_good('parent='.$r['id'], 'id='.$r['id']);
	}
}
shit_to_good('parent=0', 'parent=9999999');*/




$rv = display('categorys', '', 4117, 2);
while($r = $rv->fetch_assoc())
	echo sprintf("<%4s - %4s>   - %4s %4s %4s - %s %s\n",
		$r['lft'], $r['rgt'],
		$r['id'], $r['level'], $r['childs'],
		str_repeat("   ", $r['level']), $r['name']);

#$rv = display_rev(4);
#while($r = $rv->fetch_assoc()) echo sprintf("%2s ", $r['id']).$r['name']."\n";

echo "\n\n";






$root = 70;


/*while($r = $rv->fetch_assoc())
	echo sprintf("<%3s - %3s>   - %4s %4s %4s - %s %s\n",
		$r['lft'], $r['rgt'],
		$r['id'], $r['level'], $r['childs'],
		str_repeat("   ", $r['level']), $r['name']);*/
#display(4);

#$rv = display_rev(4);
#while($r = $rv->fetch_assoc()) echo sprintf("%2s ", $r['id']).$r['name']."\n";










function explain($q) {
	$tq = "EXPLAIN ".str_replace(";", "; EXPLAIN ", $q);
	$tq = preg_replace("~EXPLAIN[ \r\n\t]*DELETE FROM [^;]+;?[ \r\n\t]*~s", "", $tq);
	$tq = preg_replace("~ EXPLAIN[ \r\n\t]*$~", "", $tq);
	$mrv = db()->multi_query($tq);
	do {
		$rv = db()->store_result();
		$a = $rv->fetch_assoc();
		$e = @$a['Extra'];
		unset($a['Extra']);
		unset($a['id']);
		$rv = array();
		foreach($a as $k=>$v) $rv[] = "$k: $v";
		echo join(", ", $rv)."\n$e\n";
	}
	while(db()->next_result());
	flush();
}




?>
