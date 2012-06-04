<?php

class wiki_struct {
	var $page_name = '';
	var $name = '';
	var $level = 1;
	var $childs = array();
	var $parent = NULL;
	var $data = array();
	var $categorys = array();
	var $aliases = array();
	
	var $MIN_TOPICS_TO_DISPLAY = 4;
	var $TOPIC_DEEPNESS = 3;
	var $DEFAULT_IMAGE_WIDTH = 290;
	
	public static function init($page_name, $data) {
		$data = explode("\n", str_replace("\r", "", $data));
		$root = new wiki_struct;
		$ws =& $root;
		$ws->page_name = $page_name;
		$level = 1;
		$temp = array();
		for($i = 0, $num = count($data); $i < $num; $i++) {
			$line = rtrim($data[$i]);
			if(preg_match('~(==+) *(.+?) *==+~', $line, $out)) {
				$len = strlen($out[1]);
				for($j = count($temp) - 1; $j >= 0; $j--) {
					if(!$temp[$j]) unset($temp[$j]);
					else break;
				}
				$ws->data = $temp;
				
				while($len <= $level and $ws->parent) {
					$ws =& $ws->parent;
					$level--;
				}
				$level = $len;
				$temp = array();
				$ws->childs[] = new wiki_struct($page_name, $out[2], $len, $ws);
				$ws =& $ws->childs[count($ws->childs) - 1];
			}
			elseif($line or $temp)
				$temp[] = $line;
		}
		
		for($j = count($temp) - 1; $j >= 0; $j--) {
			if(!$temp[$j]) unset($temp[$j]);
			elseif(preg_match('~^\[\[ *(Kategorie|Category): *(.*?) *\]\] *~', $temp[$j], $out)) {
				$root->categorys[] = wiki_urldecode($out[2]);
				unset($temp[$j]);
			}
			elseif(preg_match('~^\[\[ *Alias: *(.*?) *\]\] *$~', $temp[$j], $out)) {
				$root->aliases[] = wiki_urldecode($out[1]);
				unset($temp[$j]);
			}
			else break;
		}
		$ws->data = $temp;
		return $root;
	}

	function wiki_struct($page_name = '', $name = '', $level = 1, $parent = NULL) {
		$this->page_name = $page_name;
		$this->name = $name;
		$this->level = $level;
		$this->parent = $parent;
	}
	function iterate($callback, $level = 1, $max_levels = 0) {
		$data = $this->$callback($level);
		if(!$max_levels or $level + 1 < $max_levels) {
			for($i = 0, $num = count($this->childs); $i < $num; $i++)
				$data .= $this->childs[$i]->iterate($callback, $level + 1);
		}
		return $data;
	}
	
	//////////////////////////////////////////////////////////////////////
	function create_topics(&$num_topics = 0, $level = 1, $pre = "") {
		if($level > $this->TOPIC_DEEPNESS or $this->level != $level) return;
		$data = "";
		for($i = 0, $num = count($this->childs); $i < $num; $i++) {
			if(!$this->childs[$i]->childs and !$this->childs[$i]->data) continue;
			$num_topics++;
			$data .= '<li class="toclevel-'.($level - 1).' tocsection-'.($level - 1).'">';
			$data .= '<a href="/'.LANG.'/wiki/'.wiki_urlencode($this->page_name).'/#'.wiki_idencode($this->childs[$i]->name).'" onclick="window.scrollTo(0,$(\'#'.wiki_idencode($this->childs[$i]->name).'\').position().top);return false;">';
			$data .= '<span class="tocnumber">'.$pre.($i + 1).'</span> <span class="toctext">'.htmlspecialchars($this->childs[$i]->name).'</span>';
			$data .= '</a>';
			$sub = $this->childs[$i]->create_topics($num_topics, $level + 1, $pre.($i + 1).".");
			if($sub) $data .= $sub;
			$data .= "</li>";
		}
		return $data ? "<ul>$data</ul>" : "";
	}
	function get_output($level = 1) {
		$data = "";
		for($i = 0, $num = count($this->childs); $i < $num; $i++)
			$data .= $this->childs[$i]->get_output($level + 1);
		if(!$data and !$this->data) return;
		$data = ($level > 1 ? '<h'.($this->level + 1).' id="'.wiki_idencode($this->name).'">'.htmlspecialchars($this->name).'</h'.$this->level.'>' : '').$this->data.$data;
		if($this->categorys) {
			$temp = array();
			foreach($this->categorys as $c) $temp[] = '<a href="/'.LANG.'/wiki/'.LS('Kategorie').':'.wiki_urlencode($c).'/">'.htmlspecialchars($c).'</a>';
			$data .= '<div class="catlinks"><a href="/'.LANG.'/wiki/'.LS('Spezial').':'.LS('Kategorien').'/">'.(count($temp) > 1 ? LS('Kategorie') : LS('Kategorien')).'</a>: '.implode(' | ', $temp).'</div>';
		}
		return $data;
	}
	
	
	function print_r($level = 1) {
		$pre = str_repeat("    ", $level - 1);
		$errors = array();
		if($this->level != $level) $errors[] = "invalid level: ".$this->level." != ".$level;
		echo $pre.$this->name.($errors ? " (".implode("; ", $errors).")" : "")."\n";
		if(!is_array($this->data)) $data = explode("\n", $this->data);
		else $data =& $this->data;
		foreach($data as $l) echo $pre."- ".$l."\n";
		echo "\n";
	}
	
	function compile($level = 1) {
		$this->_compile($level);
		for($i = 0, $num = count($this->childs); $i < $num; $i++)
			$this->childs[$i]->compile($level + 1);
	}
	function _compile_close_tags(&$parent_tags, &$tag_id, &$date_end, &$offset) {
		do {
			$tag = array_pop($parent_tags);
			$tag_start = $tag[2];
			$tag_length = $offset - $tag_start;
			$data_start = $tag_start + $tag[3];
			
			$new = substr($this->data, $data_start, $date_end - $data_start);
			switch($tag[0]) {
			case 0:
				if(preg_match('~^Image:(.+)$~i', $new, $o)) $new = $this->compile_image($o[1]);
				else $new = $this->compile_internal_link($new);
				break;
			case 1:
				$new = $this->compile_external_link($new);
				break;
			case 2:
				$new = $this->compile_function($new);
				break;
			case 3:
				$new = $this->compile_table($new);
				break;
			case 4:
				$new = "<strong>$new</strong>";
				break;
			case 5:
				$new = "<em>$new</em>";
				break;
			case 6:
				if(!$tag[1][2][0]) $lang = "text";
				else $lang = strtolower(str_replace('./\\', '', $tag[1][2][0]));
				if(!file_exists('lib/geshi/'.$lang.'.php')) $lang = "text"; #$new = '<div class="error">Unbekannte Scriptsprache: '.htmlspecialchars($tag[1][2][0]).'</div>';
				include_once 'lib/GeSHi.php';
				$g = new GeSHi(html_entity_decode(trim($new)), $lang);
				$g->set_header_type(GESHI_HEADER_NONE);
				//$g->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
				$new = $g->parse_code();
				$new = '<pre>'.preg_replace('~<br( /)?>~', '', $new).'</pre>';
				break;
			case 7:
				if(preg_match('~^ *([^\n]+) *\n?(.*)\n?$~s', $new, $out)) {
					switch(trim($out[1])) {
					case 'is_logged_in':
						$new = (IS_LOGGED_IN ? $out[2] : "");
						break;
					case 'is_wiki_mod':
						$new = (has_privilege('wiki_mod') ? $out[2] : "");
						break;
					case 'is_wiki_admin':
						$new = (has_privilege('wiki_admin') ? $out[2] : "");
						break;
					}
				}
				else '<div class="error">'.$new.'</div>';
				break;
			default:
				$new = '<div class="error">UNKNOWN TAG ID: '.$tag[0].'</div>';
				break;
			}
			$this->data = substr_replace($this->data, $new, $tag_start, $tag_length);
			
			$offset += (strlen($new) - $tag_length);
			$date_end = $offset;
		}
		while($parent_tags and $tag[0] != $tag_id);
	}
	function _compile($level) {
		$before = array();
		for($j = count($this->data) - 1; $j >= 0; $j--) {
			if(!$this->data[$j]) unset($this->data[$j]);
			elseif(preg_match('~^\[\[Image:.*?\]\] *$~', $this->data[$j])) {
				$before[] = $this->data[$j];
				unset($this->data[$j]);
			}
			else break;
		}
		if($before) $this->data = array_merge($before, $this->data);
		$this->thumb_float = "right";
		
		$this->data = implode("\n", $this->data);
		$this->data = htmlspecialchars($this->data);
		
		$tags_open = array(
			"\[\[",
			"\[",
			"\{\{",
			"\{\|",
			"'''",
			"''",
			'&lt;source lang=&quot;(.*?)&quot;&gt;',
			"\{\?");
		$tags_close = array(
			"\]\]",
			"\]",
			"\}\}",
			"\|\}",
			"'''",
			"''",
			'&lt;/source&gt;',
			"\?\}");
		$num_tags = count($tags_open);
		
		$to = array();
		for($i = 0; $i < $num_tags; $i++) $to[] = preg_replace('~\\\\(([\[\{\|\?\}\]]))~', '$1', $tags_open[$i]);
		$tc = array();
		for($i = 0; $i < $num_tags; $i++) $tc[] = preg_replace('~\\\\([\[\{\|\?\}\]])~', '$1', $tags_close[$i]);
		
		$offset = 0;
		$parent_tags = array();
		$REG = $reg = implode("|", $tags_open).'|'.implode("|", $tags_close);
		while(preg_match('~('.$reg.')~Ss', $this->data, $out, PREG_OFFSET_CAPTURE, $offset) or $reg != $REG) {
			if($reg != $REG) {
				$reg = $REG;
				if(!$out) continue;
			}
			$offset = $out[1][1] + strlen($out[1][0]);
			
			$tag_id = -1;
			for($i = 0; $i < $num_tags; $i++) {
				if(preg_match('~'.$tags_open[$i].'~', $out[1][0])) {
					$tag_id = $i;
					$is_closing_tag = false;
					if($tag_id == 4 or $tag_id == 5) {
						for($i = count($parent_tags) - 1; $i >= 0; $i--) {
							if($parent_tags[$i][0] == $tag_id) {
								$is_closing_tag = true;
								break;
							}
						}
					}
					break;
				}
			}
			if($tag_id == -1) {
				for($i = 0; $i < $num_tags; $i++) {
					if(preg_match('~'.$tags_close[$i].'~', $out[1][0])) {
						$tag_id = $i;
						$is_closing_tag = true;
						break;
					}
				}
			}
			if($tag_id == -1) continue;
			
			if($is_closing_tag) {
				for($i = count($parent_tags) - 1; $i >= 0; $i--) {
					if($parent_tags[$i][0] == $tag_id) {
						$this->_compile_close_tags($parent_tags, $tag_id, $out[1][1], $offset);
						break;
					}
				}
			}
			else {
				array_push($parent_tags, array($tag_id, $out, $out[1][1], strlen($out[1][0])));
				if($tag_id == 6) $reg = $tags_close[$tag_id];
			}
		}
		if($parent_tags) {
			$offset = strlen($this->data);
			$tag_id = -1;
			$this->_compile_close_tags($parent_tags, $tag_id, $offset, $offset);
		}
		
		//pass3
		$this->compile_lists();
		
		$this->data = trim($this->data);
		
		$num_topics = 0;
		$topics = $this->create_topics($num_topics);
		if($num_topics >= $this->MIN_TOPICS_TO_DISPLAY) {
			if($this->data) $this->data .= '<br><br>';
			$this->data .= '<table class="toc"><tr><td>';
			$this->data .= '<div class="toctitle"><h3>Inhaltsverzeichnis</h3></div>';
			$this->data .= $topics;
			$this->data .= '</td></tr></table>';
		}
		
		$this->data = preg_replace('~\n\n\n+~', "\n\n", $this->data);
		$this->data = str_replace("\n", "<br>", $this->data);
		$this->data = preg_replace('~(</(table|ol|div|pre)>)<br>~', '$1', $this->data);
		$this->data = preg_replace('~<br>(</(li)>)~', '$1', $this->data);
		
		$this->data = preg_replace('~<br><br>~', '</p><p>', $this->data.'<br><br>');
		$this->data = preg_replace('~</p>~', '', $this->data, 1);
		$this->data = preg_replace('~<p>$~', '', $this->data, 1);
		$this->data = preg_replace('~<p></p>$~', '<p>&nbsp;</p>', $this->data, 1);
		
		#$this->data = preg_replace('~<br><br>(.*?)<br><br>~', '[[p>$1[[/p><br><br>', $this->data);
		
		return $this->data;
	}
	
	//////////////////////////////////////////////////////////////////////
	function compile_lists() {
		$temp = explode("\n", $this->data);
		$listtype = "";
		$this->data = "";
		$open_tags = array();
		$num_open_tags = 0;
		foreach($temp as $l) {
			if(!preg_match('~^([\*#:\.]+)[ \t]*(.*)$~', $l, $out)) {
				for($i = $num_open_tags - 1; $i >= 0; $i--) {
					switch($open_tags[$i]) {
					case '*':
						$this->data .= '</li></ul>';
						break;
					case '#':
						$this->data .= '</li></ol>';
						break;
					case ':':
						$this->data .= '</dd></dl>';
						break;
					}
				}
				$open_tags = array();
				$num_open_tags = 0;
				$this->data .= $l."\n";
			}
			else {
				$tags = array();
				$new_at = -1;
				for($i = 0, $num_tags = strlen($out[1]); $i < $num_tags; $i++) {
					$tags[] = substr($out[1], $i, 1);
					if($i < $num_open_tags and $new_at + 1 == $i and @$tags[$i] == @$open_tags[$i]) {
						$new_at = $i;
					}
				}
				$new_at++;
				$temp = $open_tags;
				#schließe alte tags von num_open_tags bis new_at
				for($i = $num_open_tags - 1; $i >= $new_at; $i--) {
					switch(@$temp[$i]) {
					case '*':
						$this->data .= '</li></ul>';
						break;
					case '#':
						$this->data .= '</li></ol>';
						break;
					case ':':
						$this->data .= '</dd></dl>';
						break;
					}
					unset($temp[$i]);
					$num_open_tags--;
				}
				$open_tags = $temp;
				
				#öffne neue tags
				if($new_at != $num_tags) {
					for($i = $new_at; $i < $num_tags; $i++) {
						switch($tags[$i]) {
						case '*':
							$this->data .= '<ul><li>';
							$open_tags[] = $tags[$i];
							$num_open_tags++;
							break;
						case '#':
							$this->data .= '<ol><li>';
							$open_tags[] = $tags[$i];
							$num_open_tags++;
							break;
						case ':':
							$this->data .= '<dl><dd>';
							$open_tags[] = $tags[$i];
							$num_open_tags++;
							break;
						case '.':
							$this->data .= '<br>';
							$open_tags[] = $tags[$i];
							$num_open_tags++;
							break;
						}
					}
				}
				else {
					switch($tags[$num_tags - 1]) {
					case '*':
					case '#':
						if($num_tags == $new_at) $this->data .= '</li>';
						$this->data .= '<li>';
						break;
					case ':':
						if($num_tags == $new_at) $this->data .= '</dd>';
						$this->data .= '<dd>';
						break;
					case '.':
						$this->data .= '<br>';
						break;
					}
				}
				$this->data .= $out[2];
			}
		}
		for($i = $num_open_tags - 1; $i >= 0; $i--) {
			switch(@$open_tags[$i]) {
			case '*':
				$this->data .= '</li></ul>';
				break;
			case '#':
				$this->data .= '</li></ol>';
				break;
			case ':':
				$this->data .= '</dd></dl>';
				break;
			}
		}
	}
	
	function compile_function(&$inner) {
		if(trim($inner) ==  'clear') return '<div style="clear:both;"></div>';
		if(trim($inner) ==  'STATIC_CONTENT_DOMAIN') return STATIC_CONTENT_DOMAIN;
		elseif(preg_match('~^new_articles +(\d+)~', $inner, $out)) {
			static $cache_new_articles = array();
			if(!$out[1]) return;
			if(!isset($cache_new_articles[$out[1]])) {
				$cache_new_articles[$out[1]] = array();
				$pages = db()->query("SELECT name FROM wiki_pages WHERE lang='".LANG."' AND history!=0 AND deleted=0 ORDER BY timeadded DESC LIMIT ".(int)$out[1]);
				while($page = $pages->fetch_assoc()) {
					$cache_new_articles[$out[1]][] = '<a href="/'.LANG.'/wiki/'.wiki_urlencode($page['name']).'/">'.htmlspecialchars($page['name']).'</a>';
				}
				$cache_new_articles[$out[1]] = implode("<br>", $cache_new_articles[$out[1]]);
			}
			return $cache_new_articles[$out[1]];
		}
		elseif(preg_match('~last_changes (\d+)~', $inner, $out)) {
			static $cache_last_changes = array();
			if(!$out[1]) return;
			if(!isset($cache_last_changes[$out[1]])) {
				$cache_last_changes[$out[1]] = array();
				$pages = db()->query("SELECT name FROM wiki_pages WHERE lang='".LANG."' AND history!=0 AND deleted=0 ORDER BY lastchange DESC LIMIT ".(int)$out[1]);
				while($page = $pages->fetch_assoc())
					$cache_last_changes[$out[1]][] = '<a href="/'.LANG.'/wiki/'.wiki_urlencode($page['name']).'/">'.htmlspecialchars($page['name']).'</a>';
				$cache_last_changes[$out[1]] = implode("<br>", $cache_last_changes[$out[1]]);
			}
			return $cache_last_changes[$out[1]];
		}
		elseif(preg_match('~top_users (\d+) *([^ ]*) *(.*)$~', $inner, $out)) {
			static $cache_top_users = array();
			if(!$out[1]) return;
			if(!isset($cache_top_users[$out[1]])) {
				$users = db()->query("
					SELECT
						users.user_id AS id,
						SUM(IF(action='article_created',1,0)) AS article_created,
						SUM(IF(action='content_changed',1,0)) AS content_changed
					FROM wiki_changes
					RIGHT JOIN users ON users.user_id=wiki_changes.user AND (action='article_created' OR action='content_changed')
					GROUP BY users.user_id
					HAVING COUNT(wiki_changes.id)
					ORDER BY COUNT(wiki_changes.id) DESC
					LIMIT ".(int)$out[1]);
				$j = 1;
				if($out[2] == "table") {
					$cache_top_users[$out[1]] = '<table class="wikitable">';
					$cache_top_users[$out[1]] .= '<tr class="hintergrundfarbe6"><th width="35">'.LS('Platz').'</th><th width="90">'.LS('Name').'</th><th width="35">'.LS('Artikel').'</th><th width="35">'.LS('Ver&auml;nderungen').'</th></tr>';
				}
				else $cache_top_users[$out[1]] = array();
				while($i = $users->fetch_assoc()) {
					$l = user($i['id'])->html(1, '', array());
					$s = $i['article_created'].' Artikel erstellt, '.$i['content_changed'].' Ver&auml;nderung'.($i['content_changed'] == 1 ? '' : 'en').' vorgenommen';
					if($out[2] == "table") $cache_top_users[$out[1]] .= '<tr><th>'.$j.'</th><td>'.$l.'</td><td align="right">'.$i['article_created'].'</td><td align="right">'.$i['content_changed'].'</td></tr>';
					else $cache_top_users[$out[1]][] = $l.' ('.$i['article_created'].' Artikel erstellt, '.$i['content_changed'].' Ver&auml;nderung'.($i['content_changed'] == 1 ? '' : 'en').' vorgenommen)';
					$j++;
				}
				if($out[2] == "table") $cache_top_users[$out[1]] .= '</tr></td></table>';
				else $cache_top_users[$out[1]] = implode("<br>", $cache_top_users[$out[1]]);
			}
			return $cache_top_users[$out[1]];
		}
		elseif(preg_match('~open_tickets (\d+)~', $inner, $out)) {
			static $cache = array();
			if(!$out[1]) return;
			if(!isset($cache[$out[1]])) {
				$cache[$out[1]] = array();
				$pages = db()->query("
					SELECT a.name
					FROM wiki_pages a, wiki_tickets b
					WHERE a.lang='".LANG."' AND a.history!=0 AND a.deleted=0 AND a.id=b.page AND b.closer=0
					GROUP BY a.id
					ORDER BY b.timecreated DESC
					LIMIT ".(int)$out[1]);
				while($page = $pages->fetch_assoc())
					$cache[$out[1]][] = '<a href="/'.LANG.'/wiki/'.wiki_urlencode($page['name']).'/">'.htmlspecialchars($page['name']).'</a>';
				$cache[$out[1]] = implode('<br>', $cache[$out[1]]);
			}
			return $cache[$out[1]];
		}
		elseif($inner == 'wiki_search') {
			return '<form method="post" action="/'.LANG.'/wiki/'.LS('Spezial').':'.LS('Suche').'/" onsubmit="return iC(this, \'~.module-item\');">
				<input type="hidden" name="imodules/wiki/action" value="search"><input type="text" name="imodules/wiki/q" style="width:50%;" value=""> <input type="submit" class="button" value="'.LS('Suchen').'">
				<input type="checkbox" id="wikiSearchDeactivated" name="imoduleswiki/deactivated"><label for="wikiSearchDeactivated" class="quiet"> '.LS('Deaktivierte Artikel durchsuchen').'</label>
			</form>';
		}
		elseif($inner == 'wiki_new_article') {
		return '<form method="post" action="/'.LANG.'/wiki/" onsubmit="return iC(this, \'~.module-item\');">
				<input type="hidden" name="imodules/wiki/action" value="new"><input type="text" name="imodules/wiki/new_wiki" style="width:50%;" value=""> <input type="submit" class="button" value="'.LS('Erstellen').'">
			</form>';
		}
		elseif(preg_match('~css_server_stats (.+?)(\|(.+))?$~', $inner, $out)) {
			return (@$out[3] ? '<div style="float:'.self::safe_innerhtmltag($out[3]).';">' : '').
				'<iframe src="http://cache.www.gametracker.com/components/html0/?host='.$out[1].'&amp;bgColor=333333&amp;titleBgColor=222222&amp;borderColor=555555&amp;fontColor=CCCCCC&amp;titleColor=FF9900&amp;linkColor=FFCC00&amp;borderLinkColor=222222&amp;showMap=1&amp;currentPlayersHeight=100&amp;showCurrPlayers=1&amp;showTopPlayers=1&amp;showBlogs=0&amp;width=240" frameborder="0" scrolling="no" width="240" height="536"></iframe>'.
				(@$out[3] ? '</div>' : '');
		}
	}
	function compile_table(&$inner) {
		$d = explode("\n", $inner);
		$new = '<table'.self::safe_innerhtmltag($d[0]).'>';
		$in_row = false;
		$in_col = "";
		for($i = 1, $num = count($d); $i < $num; $i++) {
			$d[$i] = trim($d[$i]);
			if(preg_match('~^\|--(.*)$~', $d[$i], $o)) {
				if($in_row) $new .= '</tr>';
				else $in_row = true;
				$new .= '<tr'.self::safe_innerhtmltag($o[1]).'>';
			}
			elseif($d[$i] == '|-') {
				if($in_col) {
					$new .= $in_col;
					$in_col = "";
				}
				if($in_row) $new .= '</tr>';
				else $in_row = true;
				$new .= '<tr>';
			}
			elseif(preg_match('~^\| *(.*)~', $d[$i], $o)) {
				if($in_col) $new .= $in_col;
				if(!$in_row) {
					$new .= '<tr>';
					$in_row = true;
				}
				$new .= '<td>'.$o[1];
				$in_col = '</td>';
			}
			elseif(preg_match('~^! *(.*)$~', $d[$i], $o)) {
				if($in_col) $new .= $in_col;
				if(!$in_row) {
					$new .= '<tr>';
					$in_row = true;
				}
				$new .= '<th>'.$o[1];
				$in_col = '</th>';
			}
		}
		if($in_col) $new .= $in_col;
		if($in_row) $new .= '</tr>';
		$new .= '</table>';
		return $new;
	}
	function compile_internal_link(&$inner) {
		$d = explode("|", trim($inner));
		if(preg_match('~^(.+)#(.+)$~', $d[0], $o)) {
			$id = wiki_idencode(trim($o[2]));
			$link = $o[1];
		}
		else {
			$id = '';
			$link = $d[0];
		}
		if(preg_match('~^(Spezial|Special|Kategorie|Category|Admin):.*?~i', $link)) $extracode = '';
		else {
			$extracode = (
				(db()->query("SELECT 1 FROM wiki_pages WHERE name='".es($link)."' LIMIT 1")->num_rows or
				 db()->query("SELECT 1 FROM wiki_aliases WHERE name='".es($link)."' LIMIT 1")->num_rows) ? '' : ' class="new"');
		}
		$text = count($d) > 1 ? self::implode_at("|", $d, 1) : $d[0];
		return '<a href="/'.LANG.'/wiki/'.wiki_urlencode($link).'/'.($id ? '#'.$id : '').'"'.$extracode.'>'.htmlspecialchars($text).'</a>';
	}
	function compile_external_link(&$inner) {
		$d = explode('|', $inner);
		$text = (count($d) > 1 ? self::implode_at("|", $d, 1) : $d[0]);
		if(preg_match('~^http://(en|de|www)?\.?icom.to(/.*)$~i', $d[0], $out)) return '<a href="'.($out[2]).'">'.($text).'</a>';
		else return '<a href="'.($d[0]).'" target="_blank">'.($text).'</a>';
	}
	function compile_image(&$inner) {
		$attr = explode("|", $inner);
		$nattr = count($attr);
		
		$image = trim($attr[0]);
		unset($attr[0]);
		$j = 1;
		
		$type = 'thumb';
		$float = $this->thumb_float;
		$using_global_float = true;
		$width = $this->DEFAULT_IMAGE_WIDTH;
		
		for($i = 1, $num = count($attr) + 1; $i < $num; $i++) {
			$a = trim($attr[$i]);
			if($a == "thumb" or $a == "image") $type = $a;
			elseif($a == "left" or $a == "right" or $a == "none") {
				$float = $a;
				$using_global_float = false;
			}
			elseif(preg_match('~^(\d+)(px)?$~', $a, $out)) $width = $out[1];
			else break;
			unset($attr[$i]);
		}
		if($using_global_float) {
			if($type == 'image') $float = 'none';
			else $this->thumb_float = ($this->thumb_float == 'left' ? 'right' : 'left');
		}
		if($type == 'image' and $float == 'none') {
			$width_outer = "100%";
			$width = "99.5%";
		}
		else {
			$width_outer = ($width + 2).'px';
			$width = $width.'px';
		}
		
		$desc = implode("|", $attr);
		
		$new = '<div class="thumb t'.$float.'">';
		$new .= '<div class="thumbinner" style="max-width:'.$width.';">';
		$new .= '<a href="'.htmlspecialchars($image).'" class="image" target="_blank"><img alt="" src="'.htmlspecialchars($image).'" class="thumbimage" style="max-width:'.($width).';"></a>';
		if($desc) $new .= '<div class="thumbcaption">'.htmlspecialchars($desc).'</div>';
		$new .= '</div>';
		$new .= '</div>';
		
		return $new;
	}
	
	function compile_pass2($data) {
		$data = htmlspecialchars($data);
		return preg_replace("~\n~", '<br>', $data);
	}
	
	function implode_at($d, $arr, $i = 0) {
		$o = '';
		for($j = $i, $num = count($arr); $j < $num; $j++)
			$o .= ($i != $j ? $d : '').$arr[$j];
		return $o;
	}
	
	function safe_innerhtmltag($data) {
		$data = trim(str_replace("<", "&lt;", str_replace(">", "&gt;", html_entity_decode($data))));
		return $data ? " $data" : $data;
	}
}


/*if(!function_exists("es")) {
	require "init_session.inc.php";
	require "wiki.inc.php";
	
	ob_start();
	$data = wikicode('das hier ist ein test....

* erstes ding
* zweites ding

* naechste liste
*. neue zeile in der liste
*. und noch ne zeile
*# unterliste
*# zweite zeile in der unterliste
** neuer unterpunkt
*: einrueckung
*: noch eine
*::: dreifach
* und erstmal das letzte

ende');
	$x = ob_get_contents();
	ob_end_clean();
	echo '<pre>'.htmlspecialchars($x).'</pre>';
	echo '<hr><pre>'.htmlspecialchars($data->output).'</pre>';
	echo '<hr>'.$data->output;
	#die;
	
	$_GET['wiki'] = "GERMAN.RETAiL.XviD.RELEASiNG.GUiDELiNES";
	$_GET['history'] = 441;
	$w = new wiki();
	$data = $w->run();
	echo '<hr><pre>'.htmlspecialchars($data).'</pre>';
	echo '<hr>'.$data;
}*/

class wikicode {
	public static function parse($page_name, $data, $max_width = NULL, $max_height = NULL) {
		$ws = wiki_struct::init($page_name, $data);
		$ws->compile();
		$ws->output = $ws->get_output();
		return $ws;
	}
}

?>
