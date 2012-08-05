<?php

ubbcode::$BLACKLISTED_DOMAINS = 
	array_filter(array_map('trim', file('../txt/blacklisted_domains.txt')));

class ubbcode {
	public static $BLACKLISTED_DOMAINS = array();

	protected static function handle_tag(&$data, &$tag, &$arg, &$parent_tags, &$max_width, &$max_height, &$user_said_thanks) {
		switch($tag) {
		case 'quote':
			$data = '<div class="quote-header">Zitat'.($arg ? " von $arg" : "").':</div><blockquote class="quote-content">'.trim($data).'</blockquote>';
			break;
		case 'code':
			$data = str_replace('(', '&#40;', $data);
			$data = str_replace(')', '&#41;', $data);
			$data = str_replace(':', '&#58;', $data);
			$data = str_replace('-', '&#45;', $data);
			$data = str_replace('|', '&#124;', $data);
			$data = str_replace('[', '&#91;', $data);
			$data = str_replace(']', '&#93;', $data);
			$data = str_replace('\\', '&#92;', $data);
			$data = str_replace('/', '&#47;', $data);
			$data = '<div class="code-header">'.($arg ? $arg : 'Code:').'</div><pre class="code-content">'.trim($data).'</pre>';
			break;
		case 'spoiler':
			$data = '<button type="button" class="button"  onclick="$(this).next().toggle();">'.($arg ? str_replace('"', '&quot;', $arg) : 'Spoiler').'</button><blockquote class="quote-content hidden">'.trim($data).'</blockquote>';
			break;
		case 'hidden':
			if(IS_LOGGED_IN) $data = '<button type="button" class="button" onclick="$(this).next().toggle();">'.($arg ? str_replace('"', '&quot;', $arg) : 'Spoiler').'</button><blockquote class="quote-content hidden">'.trim($data).'</blockquote>';
			else $data = '<button type="button" class="button error">Der Inhalt dieses Spoilers ist nur f&uuml;r Mitglieder sichtbar</button>';
			break;
		case 'thx':
		case 'thanked':
			if(!IS_LOGGED_IN) $data = '<button type="button" class="button error">Der Inhalt dieses Spoilers ist nur f&uuml;r Mitglieder sichtbar</button>';
			elseif($user_said_thanks === false) $data = '<button type="button" class="button" onclick="$(this).next().toggle();">Spoiler</button><blockquote class="quote-content hidden info">Du musst dich bedanken um den Inhalt dieses Spoilers zu sehen</blockquote>';
			else $data = '<button type="button" class="button" onclick="$(this).next().toggle();">'.($arg ? str_replace('"', '&quot;', $arg) : 'Spoiler').'</button><blockquote class="quote-content hidden">'.trim($data).'</blockquote>';
			break;
		case 'b':
			$data = '<strong style="font-weight:bold;">'.$data.'</strong>';
			break;
		case 'i':
			$data = '<em>'.$data.'</em>';
			break;
		case 'u':
			$data = '<u>'.$data.'</u>';
			break;
		case 'strike':
			$data = '<strike>'.$data.'</strike>';
			break;
		case 'left':
			$data = '<div style="text-align:left;">'.$data.'</div>';
			break;
		case 'right':
			$data = '<div class="right">'.$data.'</div>';
			break;
		case 'center':
			$data = '<div class="center">'.trim($data).'</div>';
			break;
		case 'color':
			$W3C_COLOR_NAMES = array('aqua', 'black', 'blue', 'fuchsia', 'gray', 'green', 'lime', 'maroon', 'navy', 'olive', 'purple', 'red', 'silver', 'teal', 'white', 'yellow', 'orange');
			if (in_array($arg, $W3C_COLOR_NAMES) || self::checkHEX($arg)) {
				$data = '<span style="color: '.str_replace('"', '&quot;', $arg).';">'.$data.'</span>';	
			}
			break;
		case 'size':
			if (filter_var($arg, FILTER_VALIDATE_INT)) {	
				if ((int) $arg > MAX_FONT_SIZE) 
					$arg = MAX_FONT_SIZE;
				if ((int) $arg < MIN_FONT_SIZE) 
					$arg = MIN_FONT_SIZE;
				$data = '<span style="font-size:'.str_replace('"', '&quot;', $arg).'px;">'.$data.'</span>';
			}
			break;
		case 'font':
			$arg = explode(';', str_replace('"', '&quot;', $arg)); 
			$data = '<span style="font-family:'.$arg[0].';">'.$data.'</span>';
			break;
		case 'email':
			$link = trim(str_replace('"', '&quot;', $arg ? $arg : $data));
			if(strpos($link, "\n") !== false) $data = 'Invalid link';
			$data = '<a href="mailto:'.$link.'" target="_blank">'.$data.'</span>';
			break;
		case 'url':
			$link = trim($arg ? $arg : $data);
			if(strpos($link, "\n") !== false) $data = 'Invalid link';
			else {
				if(!preg_match('~^https?://~i', $link)) $link = "http://$link";
				if(preg_match('~^https?://[^/]*'.preg_quote(SITE_DOMAIN).'(/.*)~i', $link, $out)) $data = '<a href="'.str_replace('"', '&quot;', @$out[1]).'">'.$data.'</a>';
				else $data = '<a href="'.str_replace('"', '&quot;', $link).'" target="_blank">'.$data.'</a>';
			}
			break;
		case 'img':
			$link = trim(str_replace('"', '&quot;', $data));
			if(strpos($link, "\n") !== false) $data = 'Invalid link';
			$data = 'src="'.$link.'" alt="'.$link.'"'.($max_height ? ' style="max-height:'.str_replace('"', '&quot;', $max_height).'px;"' : '');
			if($arg) $data = '<div><img '.$data.'><br><em>'.$arg.'</em></div>';
			else $data = '<img '.$data.'>';
			break;
		case 'img_left':
			$link = trim(str_replace('"', '&quot;', $data));
			if(strpos($link, "\n") !== false) $data = 'Invalid link';
			$data = 'src="'.$link.'" alt="'.$link.'"'.($max_height ? ' style="max-height:'.str_replace('"', '&quot;', $max_height).'px;"' : '');
			if($arg) $data = '<div class="fleft"><img '.$data.'><br><em>'.$arg.'</em></div>';
			else $data = '<img class="fleft" '.$data.'>';
			break;
		case 'img_right':
			$link = trim(str_replace('"', '&quot;', $data));
			if(strpos($link, "\n") !== false) $data = 'Invalid link';
			$data = 'src="'.$link.'" alt="'.$link.'"'.($max_height ? ' style="max-height:'.str_replace('"', '&quot;', $max_height).'px;"' : '');
			if($arg) $data = '<div class="fright"><img '.$data.'><br><em>'.$arg.'</em></div>';
			else $data = '<img class="fright" '.$data.'>';
			break;
		#case 'radio':
		#	$channel = trim($arg ? $arg : $data);
		#	$data = '<a href="/radio/'.urlencode(str_replace('"', '&quot;', $channel)).'/" target="_blank" rel="nofollow" onclick="window.open(\'/radio/'.urlencode($channel).'\',\'icom-radio\',\'width=175,height=500,status=no,scrollbars=yes,resizable=yes,location=no\');return false;">'.($data ? $data : $arg).'</a>';
		#	break;
		case 'poll':
			$data = iengine::GET('poll', ['poll' => (int)trim($arg ? $arg : $data)])->RUN('ITEM');
			$data = '<blockquote class="quote-content">'.$data.'</blockquote>';
			break;
		case 'lightbox':
			$data =
				'<a href="'.trim(str_replace('"', '&quot;', $data)).'" target="_blank" class="image">'.
					'<img src="'.trim(str_replace('"', '&quot;', $arg ? $arg : $data)).'" alt="'.trim(str_replace('"', '&quot;', $data)).'"'.($max_height ? ' style="max-height:'.str_replace('"', '&quot;', $max_height).'px;"' : ' style="max-width:150px"').'>'.
				'</a>';
			break;
		case 'list':
			if($arg) $data = '<ol type="'.str_replace('"', '&quot;', $arg).'">'.trim($data).'</ol>';
			else $data = '<ul>'.trim($data).'</ul>';
			break;
		case 'google':
			$data = '<a href="http://www.google.de/search?q='.trim(str_replace('"', '&quot;', urlencode($data))).'" target="_blank">'.trim($data).'</a>';
			break;
		case 'wiki':
			$arg = trim($arg);
			$data = trim($data);
			$name = ($arg ? $arg : $data);
			$wiki_exists = wiki_exists($name);
			$data = '<span class="wiki"><a href="/'.LANG.'/wiki/'.str_replace('"', '&quot;', wiki_urlencode($name)).'/"'.($wiki_exists ? '' : ' class="new"').'>'.trim($data).'</a></span>';
			break;
		case 'lang':
			if(trim($arg) == LANG) $data = trim($data);
			else $data = '';
			break;
		case '*':
			#$parent = end($parent_tags);
			#if($parent_tags and $parent[0] == "list")
			$data = '<li>'.$data.'</li>';
			break;
		case 'table';
			$data = '<table>'.trim($data).'</table>';
			break;
		case 'row';
			if($parent_tags and $parent_tags[0] == 'table') $data = '<tr>'.trim($data).'</tr>';
			break;
		case 'col';
			if(count($parent_tags) >= 2 and $parent_tags[0] == 'row' and $parent_tags[1] == 'table') $data = '<td>'.$data.'</td>';
			break;
		case 'col_head';
			if(count($parent_tags) >= 2 and $parent_tags[0] == 'row' and $parent_tags[1] == 'table') $data = '<th>'.$data.'</th>';
			break;
		case 'youtube';
			$link = "";
			$data = trim($data);
			if(preg_match('/^http:\/\/(www\.|de\.)?youtube\.com\/v\/([^&\?=]+)/i', $data, $eid)) $link = 'http://www.youtube.com/v/'.$eid[2];
			elseif(preg_match('/^http:\/\/(www\.|de\.)?youtube\.com\/watch\?v=([^&]+)(&.+)?$/i', $data, $eid)) $link = 'http://www.youtube.com/v/'.$eid[2];
			elseif(preg_match('~^http://.*?youtube\.com/watch\?v=([^&]+)$~i', $data, $eid)) $link = 'http://www.youtube.com/v/'.$eid[1];
			if($link) {
				if(is_numeric($arg) and $arg >= 30 and $arg < $max_width) $max_width = $arg;
				$w = 560;
				$h = 340;
				if($max_width and $w > $max_width) {
					$h -= round(($w - $max_width)*($h/$w));
					$w = $max_width;
				}
				if($max_height and $h > $max_height) {
					$w -= round(($h - $max_height)*($w/$h));
					$h = $max_height;
				}
				if($w < 30 or $h < 30) $data = 'Zu wenig Platz um dieses Video anzuzeigen';
				else $data = '<div class="center"><object width="'.$w.'" height="'.$h.'"><param name="movie" value="'.str_replace('"', '&quot;', $link).'&amp;hl=de&amp;fs=1"></param><param name="allowFullScreen" value="true"></param><embed src="'.str_replace('"', '&quot;', $link).'&amp;hl=de&amp;fs=1" type="application/x-shockwave-flash" allowfullscreen="true" width="'.$w.'" height="'.$h.'"></embed></object></div>';
			}
			else $data = "Fehlerhafter YouTube Link";
			break;
		case 'clipfish':
			$link = "";
			if(preg_match("~http://www.clipfish.de/video/(\d+)/~i", $data, $eid)) {
				if(is_numeric($arg) and $arg >= 30 and $arg < $max_width) $max_width = $arg;
				$w = 560;
				$h = 340;
				if($max_width and $w > $max_width) {
					$h -= round(($w - $max_width)*($h/$w));
					$w = $max_width;
				}
				if($max_height and $h > $max_height) {
					$w -= round(($h - $max_height)*($w/$h));
					$h = $max_height;
				}
				if($w < 30 or $h < 30) $data = 'Zu wenig Platz um dieses Video anzuzeigen';
				else $data = '<div class="center"><object codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="'.$w.'" height="'.$h.'"><param name="allowScriptAccess" value="always"></param><param name="movie" value="http://www.clipfish.de/cfng/flash/clipfish_player_3.swf?videoid='.str_replace('"', '&quot;', $eid[1]).'"></param><param name="bgcolor" value="#ffffff"></param><param name="allowFullScreen" value="true"></param><embed src="http://www.clipfish.de/cfng/flash/clipfish_player_3.swf?vid='.str_replace('"', '&quot;', $eid[1]).'" quality="high" bgcolor="#990000" width="'.$w.'" height="'.$h.'" name="player" align="middle" allowFullScreen="true" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed></object></div>';
			}
			else $data = 'Fehlerhafter Clipfish Link';
			break;
		case 'news_introduce':
			if(($news = iengine::GET('news')) !== NULL) $data = preg_replace('~[\r\n]~', '', $news->news($data, 'forum'));
			else $data = '<p class="error">News module not loaded</p>';
			break;
		case 'fb_like':
			$data = trim($data);
			if(!$data) $url = 'document.location';
			elseif(preg_match('~^https?://[^/]*icom\.([^/]+)~', $data)) $url = '\''.str_replace('"', '\\"', str_replace('\'', '\\\'', $data)).'\'';
			else {
				$data = '<span class="error">'.LS('Facebook Like URL fehlerhaft oder verboten (es sind ausschlie&szlig;lich iCom Links erlaubt).').'</span>';
				return;
			}
			$id = 'FBLike'.random_string(8);
			$blubb = '<iframe src="http://www.facebook.com/plugins/like.php?href=%LINK%&amp;layout=button_count&amp;show_faces=true&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none;overflow:hidden;width:300px;height:21px;margin-top:4px;margin-left:5px;" allowTransparency="true"></iframe>';
			$data = '<span id="'.$id.'"></span><script>$(function(){ $(\'#'.$id.'\').html(String(\''.$blubb.'\').replace(/%LINK%/,escape('.$url.'))); });</script>';
			break;
		case 'ace_of_spades':
			$data = strtolower(trim($data));
			if(!preg_match('~^http://.*~', $data)) return;
			$cache_id = 'ubb_ace_of_spades_'.crc32($data);
			if(($temp = cache_L2::get($cache_id)) === false) {
				cache_L2::set($cache_id, 2*60, 'WORKING');
				$temp = @my_file_get_contents($data);
				if(!preg_match('~Aktuelle Map: <b>(.*?)</b><br>\s*Aktuelle Spieler: <b>(\d+)/(\d+)</b><br>\s*Uptime: <b>(\d+)</b>~si', $temp, $out)) {
					$temp = 'FAILED';
					cache_L2::set($cache_id, 1*60, $temp);
				}
				else {
					$temp =& $out;
					cache_L2::set($cache_id, 1*60, $temp);
				}
			}
			switch($temp) {
			default:
				$width = (($max_width and $max_width < 200) ? $max_width : 200);
				$data =
					'<table style="width:'.$width.'px;" border="1" class="ace-of-spades-server-stats">'.
					'<tr style="height:1px;border:0;"><th style="width:60px;border:0;"></td><td style="border:0;"></td></tr>'.
					'<tr><th colspan="2" style="text-align:center;font-weight:bold;">'.($arg ? htmlspecialchars($arg) : LS('Ace of Spades')).'</th></tr>'.
					'<tr><th style="font-weight:bold;">'.LS('Map:').'</th><td style="text-align:center;">'.htmlspecialchars($temp[1]).'</td></tr>'.
					'<tr><th style="font-weight:bold;">'.LS('Spieler:').'</th><td style="text-align:center;">'.htmlspecialchars($temp[2]).' / '.htmlspecialchars($temp[3]).'</td></tr>'.
					'<tr><th style="font-weight:bold;">'.LS('Online:').'</th><td style="text-align:center;">'.timeelapsed($temp[4], 'seit', 'seit').'</td></tr>'.
					'</table>';
				return;
			case 'WORKING':
				$data = LS('<p class="info">Ace of Spades Daten werden vom Server %1% abgerufen.<br>Aktualisiere die Seite in ein paar Sekunden.</p>', $data);
				return;
			case 'FAILED':
				$data = LS('<p class="error">Es trat ein Fehler beim abrufen der Ace of Spades Server Daten auf. Neuversuch in ein bis zwei Minuten...</p>', $data);
				return;
			}
			break;
		/*case 'radio_stats':
			if(!$data) $data = '[radio_stats]CHANNEL[/radio_stats]';
			else {
				$css = '';
				if($arg == 'center') $css = 'margin-left:auto;margin-right:auto;';
				$data = '<div style="width:160px;border:1px #666 solid;position:inherit;'.$css.'">'.menu::radio_get_infos(true, $data).'</div>';
				$data = str_replace("\n", "", $data);
			}
			break;*/
		}
	}
	protected static function parse_close(&$data, &$parent_tags, &$offset, &$this_tag, &$data_end, &$max_width, &$max_height, &$user_said_thanks) {
		do {
			$tag = array_pop($parent_tags);
			$tag_start = $tag[1] - 1;
			$tag_length = $offset - $tag_start;
			$data_start = $tag_start + $tag[2] + 1;
			
			$new = substr($data, $data_start, $data_end - $data_start);
			self::handle_tag($new, $tag[0], $tag[3], $parent_tags, $max_width, $max_height, $user_said_thanks);
			
			$data = substr_replace($data, $new, $tag_start, $tag_length);
			
			$offset += (strlen($new) - $tag_length);
			$data_end = $offset;
		}
		while($parent_tags and $tag[0] != $this_tag and ($this_tag != 'list' or $tag[0] == '*'));
	}
	protected static function parse(&$data, $max_width = NULL, $max_height = NULL, $user_said_thanks = NULL) {
		static $TAGS = array(
			'quote'=>1, 'code'=>1, 'spoiler'=>1, 'hidden'=>1, 'b'=>1, 'i'=>1, 'u'=>1,
			'left'=>1, 'right'=>1, 'center'=>1, 'color'=>1, 'size'=>1, 'font'=>1, 'email'=>1,
			'url'=>1, 'img'=>1, 'list'=>1, '*'=>1, 'table'=>1, 'row'=>1, 'col'=>1, 'col_head'=>1,
			'youtube'=>1, 'clipfish'=>1, 'thanked'=>1, 'thx'=>1, 'lightbox'=>1, 'google'=>1,
			'radio'=>1, 'poll'=>1, 'lang'=>1, 'strike'=>1, 'wiki'=>1, 'news_introduce'=>1,
			'img_left'=>1, 'img_right'=>1, 'fb_like'=>1, 'radio_stats'=>1, 'ace_of_spades'=>1);
		static $REGEX = '[^\]=]+';
		$regex = $REGEX;
		$offset = 0;
		$parent_tags = array();
		while(preg_match('~(\[('.$regex.')( *= *[\'"]? *([^\]\'"]+) *[\'"]?)?\])~SUs', $data, $out, PREG_OFFSET_CAPTURE, $offset) or $regex != $REGEX) {
			if($regex != $REGEX) {
				$regex = $REGEX;
				if(!$out) continue;
			}
			
			if(substr($out[2][0], 0, 1) == "/") {
				$out[2][0] = substr($out[2][0], 1);
				$is_closing_tag = true;
			}
			else $is_closing_tag = false;
			$out[2][0] = strtolower($out[2][0]);
			
			$offset = $out[1][1] + strlen($out[1][0]);
			if(!isset($TAGS[$out[2][0]])) continue;
			
			if($is_closing_tag) {
				for($i = count($parent_tags) - 1; $i >= 0; $i--) {
					if($parent_tags[$i][0] == $out[2][0]) {
						self::parse_close($data, $parent_tags, $offset, $out[2][0], $out[1][1], $max_width, $max_height, $user_said_thanks);
						break;
					}
				}
			}
			else {
				$out[2][2] = strlen($out[1][0]) - 1;
				$out[2][3] =& $out[4][0];
				array_push($parent_tags, $out[2]);
				if($out[2][0] == "code") $regex = "/code";
			}
		}
		if($parent_tags) {
			$offset = strlen($data);
			$this_tag = "";
			self::parse_close($data, $parent_tags, $offset, $this_tag, $offset, $max_width, $max_height, $user_said_thanks);
		}
	}

	public static function compile($data, $max_width = NULL, $max_height = NULL, $user_said_thanks = NULL) {
		$t = get_militime();
		#$md5 = 'ubbcode_'.md5($data).'-'.$max_width.'-'.$max_height.'-'.IS_LOGGED_IN.'-'.$user_said_thanks;
		#if(($cached = cache_L1::get($md5)) !== false) return $cached;
		$od = $data;
		$data = htmlspecialchars(stripslashes(trim($data)));
		$data = str_replace("\r", "", $data);
		$data = str_replace("&quot;", "\"", $data);
		$data = str_replace("\t", "    ", $data);
		$data = str_replace(array('b0g.org'), '*ZENSORED*', $data);
		
		$data = str_replace(':hrtsdh6r5cherg:', '', $data);
		#$data = str_replace(':hrtsdh6r5cherg:', IS_LOGGED_IN ? '[img]http://icom.to/img/e/'.(mt_rand() % 3 + 1).'.png[/img]' : '', $data);
		
		/*if(preg_match_all('~(\s)([a-z]+)()~i', $data, $out, PREG_OFFSET_CAPTURE)) {
			$new = '';
			$last = strlen($data);
			for($i = count($out[1]) - 1; $i >= 0; $i--) {
				$pos3 = $out[3][$i][1] + strlen($out[3][$i][0]);
				$nw = $out[2][$i][0];
				$nl = strlen($nw);
				
				########## reverse
				/*$nw = strrev($nw);*/
				
				########## l33t
				/*$from = array('a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 't', 'T', 's', 'S', 'z', 'Z', 'g', 'G', 'b', 'B');
				$to   = array('4', '4', '3', '3', '1', '1', '0', '0', '7', '7', '5', '5', '2', '2', '9', '9', '8', '8');
				$nw = str_replace($from, $to, $nw);*/
				
				########## chaos
				/*if($nl > 3) {
					$w = substr($nw, 0, 1);
					$c = array();
					for($j = 1; $j < $nl - 2; $j++)
						$c[] = substr($nw, $j, 1);
					shuffle($c);
					foreach($c as $d) $w .= $d;
					$nw = $w.substr($nw, $nl - 2);
				}*/
				
				/*$new = $out[1][$i][0].$nw.$out[3][$i][0].substr($data, $pos3, $last - $pos3).$new;
				$last = $out[1][$i][1];
			}
			$data = substr($data, 0, $last).$new;
		}*/
		
		self::parse($data, $max_width, $max_height, $user_said_thanks);
		$found = 1;
		$data = ' '.$data.' ';
		for($i = 0; $i < 10	and $found; $i++) {
			$j = 0;
			$data = preg_replace('~([^\'"=<>\[\]])(https?://[^/]*icom\.to(/(de|en)?/radio[^ \t\r\n<>]*))([ \t\r\n<>])~si', '\1<a href="\3">\2</a>\5', $data, -1, $j);
			$found = $j;
			$data = preg_replace('~([^\'"=<>\[\]])(https?://[^/]*icom\.to(/[^ \t\r\n<>]*))([ \t\r\n<>])~si', '\1<a href="\3">\2</a>\4', $data, -1, $j);
			$found = $j;
			$data = preg_replace('~([^\'"=<>\[\]])(https?://[^ \t\r\n<>]+/[^ \t\r\n<>]*)([ \t\r\n<>])~si', '\1<a href="\2" target="_blank">\2</a>\3', $data, -1, $j);
			$found += $j;
		}
		$data = substr($data, 1);
		$data = substr($data, 0, strlen($data) - 1);
		$data = str_replace("[/*]", "", $data);
		$data = str_replace("\n", "<br>", $data);
		$data = str_replace("</li>", "", $data);
		
		if(preg_match_all('~<a [^>]*href=["\'](((http|ftp)s?://)?([a-z0-9\.]+\.)?('.implode('|', array_map('preg_quote', self::$BLACKLISTED_DOMAINS)).')[^"\']*)["\'][^>]*>(.*?)</a>~i', $data, $out)) {
			for($i = 0, $num = count($out[0]); $i < $num; $i++) {
				if($out[1][$i] != $out[6][$i]) $out[6][$i] .= ' ('.$out[1][$i].') ';
				$data = str_replace($out[0][$i], $out[6][$i], $data);
			}
		}
		/*if(preg_match_all('~((<a href=")(https?://[^"]+)("[^>]*>))~', $data, $out)) {
			static $cache = array();
			for($i = 0, $num = count($out[0]); $i < $num; $i++) {
				$id = md5($out[3][$i]);
				if(!isset($cache[$id])) {
					db()->query("INSERT IGNORE INTO out_links SET id='".$id."', link='".db()->escape_string(html_entity_decode($out[3][$i]))."'");
					$cache[$id] = true;
				}
				$data = str_replace($out[1][$i], $out[2][$i].'/out/'.$id.'-'.urlencode($out[3][$i]).'/'.$out[4][$i], $data);
			}
		}*/
		#if(sub_militime($t, get_militime()) > 0.05) cache_L1::set($md5, 0, $data);
		return $data;
	}


	public static function add_smileys($text) {
		static $in = array();
		static $out = array();
		$text = preg_replace('~\(flag:([a-z]{2})\)~', '<img class="countryflag" src="'.STATIC_CONTENT_DOMAIN.'/img/countryflags/\\1.gif" alt="(flag&#58;\\1)" title="(flag&#58;\\1)">', $text);
		if(!$in or !$out) {
			$data = file("../txt/smileys.txt");
			foreach($data as $l) {
				$l = trim($l);
				if(!$l or !preg_match("/^([^ \t]+)[ \t]+(.+)$/", $l, $reg)) continue;
				$in[] = $reg[1];
				$out[] = '<img class="smiley" src="'.STATIC_CONTENT_DOMAIN.$reg[2].'" alt="'.$reg[1].'" title="'.$reg[1].'">';
			}
		}
		return str_replace($in, $out, $text);
	}

	protected static function checkHEX($hex) {
		/*
			Prüft lediglich auf einen validen HEX-Wert
			HEX kann 3 oder 6 Zeichen haben (#fff, #ffffff)
		*/
		$hex_pattern = '~^#?([0-9a-f]{3}\b|[0-9a-f]{6}\b)$~i';
		if (preg_match($hex_pattern, $hex)) {
			return True; 
		}
		else{
			return False;
		}
	}
}

?>
