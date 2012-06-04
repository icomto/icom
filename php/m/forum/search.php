<?php

class m_forum_search extends imodule {
	use ilphp_trait;
	use im_way;
	use im_pages;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		$this->url = '/'.LANG.'/forum/';
		$this->way[] = [LS('Forum'), $this->url];
	}
	
	
	protected function POST_search(&$args) {
		return $this->MODULE($args);
	}
	
	protected function MODULE(&$args) {
		$this->search_tab = 'for';
		$this->forum = NULL;
		if(isset($args['forum']))
			$this->forum = array_map(function($v) { return (int)$v; }, is_array($args['forum']) ? $args['forum'] : explode_arr_list($args['forum']));
		if(!$this->forum) $this->forum = [0];
		
		if(isset($args['search'])) {
			$this->term = urldecode($args['search']);
			$this->user = (empty($args['user']) ? '' : $args['user']);
			$this->order_by = ((!empty($args['order_by']) and in_array($args['order_by'], array('score', 'hits', 'time'))) ? $args['order_by'] : 'score');
			$this->group_by = ((!empty($args['group_by']) and $args['group_by'] == 'posts') ? 'posts' : 'threads');
			
			if(!IS_AJAX And !empty($args['options'])) {
				$options = explode(',', es(trim($args['options'], " \r\t\n,")));
				while(count($options) < 3) $options[] = 1;
				$this->names = ($options[0] ? 1 : 0);
				$this->content = ($options[1] ? 1: 0);
				$this->search_sub = ($options[2] ? 1: 0);
			}
			else {
				$this->names = (empty($args['names']) ? 0 : 1);
				$this->content = (empty($args['content']) ? 0 : 1);
				$this->search_sub = (empty($args['sub']) ? 0 : 1);
			}
		}
		
		if($this->term == '-') $this->term = '';
		//if($this->term) set_search_category_text($this->search_tab, '', $this->term);
		
		$this->im_pages_get(@$args['page']);
		
		$this->url = '/'.LANG.'/forum/';
		$this->way = [[LS('Forum'), $this->url]];
		$this->way[] = [LS('Suche'), $this->url.'0/search/'];
		
		$this->url = '/'.LANG.'/'.
					'forum/'.($this->search_tab == 'bor' ? 'bor' : implode_arr_list($this->forum)).'/'.
					'search/'.urlencode($this->term ? $this->term : '-').'/'.
					'options/'.$this->names.','.$this->content.','.$this->search_sub.'/'.
					($this->user ? 'user/'.urlencode($this->user).'/' : '').
					($this->order_by == 'score' ? '' : 'order_by/'.$this->order_by.'/').
					($this->group_by == 'posts' ? 'group_by/posts/' : '');
		if(IS_AJAX) page_redir($this->url);
		
		list($this->read_groups, $this->write_groups) = m_forum_global::get_group_querys();
		
		$this->ilphp_init('search.php.ilp', 5*60, md5($this->url.print_r($this->read_groups, true).print_r($this->write_groups, true)).','.implode(',', user()->languages));
		###############if(($data = $this->ilphp_cache_load()) !== false) return $data;
		
		$this->num_pages = 1;
		$this->forum_sections = db()->query("
			SELECT
				forum_sections.section_id AS section_id,
				".LQ('forum_sections.name_LL')." AS name,
				COUNT(*)-2 AS level,
				forum_sections.section_id IN (".implode_arr_list($this->forum).") AS selected
			FROM forum_sections, forum_sections AS a
			WHERE
				".$this->read_groups." AND
				forum_sections.lft BETWEEN a.lft AND a.rgt
			GROUP BY forum_sections.lft
			ORDER BY forum_sections.lft");
		
		if((!$this->term and !$this->user) or (!$this->names and !$this->content)) {
			$this->im_way_title();
			return $this->ilphp_fetch();
		}
		
		if($this->forum[0] != 0) {
			$rv = db()->query("
				SELECT forum_sections.section_id
				FROM forum_sections, forum_sections AS a
				WHERE
					".$this->read_groups." AND
					forum_sections.lft BETWEEN a.lft AND a.rgt AND
					a.section_id IN (".implode_arr_list($this->forum).")
				GROUP BY forum_sections.lft");
			$this->forum = array();
			while($r = $rv->fetch_assoc()) $this->forum[] = $r['section_id'];
		}
		
		$this->way[] = [($this->user ? LS('Beitr&auml;ge von %1%', $this->user) : '').(($this->term and $this->user) ? ' - ' : '').($this->term ? LS('Suchergebnisse zu "%1%"', $this->term) : ''), $this->url];
		$this->im_way_title();
		array_pop($this->way);
		
		$where = '';
		$this->user_id = 0;
		$this->user_not_found_error = false;
		if($this->user) {
			$rv = db()->query("SELECT user_id FROM users WHERE nick='".es($this->user)."' LIMIT 1");
			if($rv->num_rows == 0) $this->user_not_found_error = true;
			else {
				$this->user_id = $rv->fetch_object()->user_id;
				$where .= " AND forum_posts.user_id='".$this->user_id."'";
			}
		}
		
		if($this->user_not_found_error)
			return $this->ilphp_fetch();
		
		$fields = array();
		if($this->names) $fields[] = 'name';
		if($this->content) $fields[] = 'content';
		$num_fields = count($fields);
		
		$search_method = 'sphinx';
		
		$searchtext = preg_replace("~  +~", " ", preg_replace("/[\.\-_]/", " ", $this->term));
		$words = array_map('trim', explode(" ", $searchtext));
		$num_words = count($words);
		
		$my_words = array();
		$small_words = array();
		$bad_words = array();
		$stopped_words = array();
		static $BADWORDS = NULL;
		if($BADWORDS === NULL) {
			$BADWORDS = array();
			$data = explode("\n", str_replace("\r", '', file_get_contents('../txt/sql-forum-DE-badwords.txt')));
			foreach($data as $d) $BADWORDS[trim($d)] = true;
		}
		static $STOPWORDS = NULL;
		if($STOPWORDS === NULL) {
			$STOPWORDS = array();
			$data = explode("\n", str_replace("\r", '', file_get_contents('../txt/sql-forum-DE-stopwords.txt')));
			foreach($data as $d) $STOPWORDS[trim($d)] = true;
		}
		foreach($words as $word) {
			if(isset($STOPWORDS[strtolower($word)])) $stopped_words[] = $word;
			elseif(isset($BADWORDS[strtolower($word)])) $bad_words[] = $word;
			elseif(mb_strlen($word) <= 3) $small_words[] = $word;
			else $my_words[] = $word;
		}
		if($small_words and (!$my_words or count($my_words)/$num_words < 0.4)) {
			uasort($small_words, function($a, $b) { return mb_strlen($b) - mb_strlen($a); });
			while($small_words and count($my_words)/$num_words < 0.4)
				$my_words[] = array_shift($small_words);
		}
		if($bad_words and (!$my_words or count($my_words)/$num_words < 0.3)) {
			uasort($bad_words, function($a, $b) { return mb_strlen($b) - mb_strlen($a); });
			while($bad_words and count($my_words)/$num_words < 0.3)
				$my_words[] = array_shift($bad_words);
		}
		if(!$my_words) $my_words = array_merge($my_words, $stopped_words);
		/*if(@USER_ID == 1) {
			print_r($words); echo '<br>';
			echo 'my: '; print_r($my_words); echo '<br>';
			echo 'words: '; print_r($words); echo '<br>';
			echo 'small: '; print_r($small_words); echo '<br>';
			echo 'bad: '; print_r($bad_words); echo '<br>';
			echo 'stopped: '; print_r($stopped_words); echo '<br>';
		}*/
		$orignial_words = implode(' ', $words);
		$words = $my_words;
		
		$words_fulltext = array();
		#$words_sphinx = array();
		foreach($words as $word) $words_fulltext[] = '+'.$word.(mb_strlen($word) <= 2 ? '' : '*');
		/*foreach($words as $word) {
			$word = trim($word, '*$"\'\\!~?<>+-&|()[]\t\r\n ');
			if($word) $words_sphinx[] = $word.(mb_strlen($word) <= 2 ? '' : '*');
		}*/
		uasort($words_fulltext, function($a, $b) { return mb_strlen($b) - mb_strlen($a); });
		$words = implode(' ', $words);
		$words_fulltext = implode(' ', $words_fulltext);
		#$words_sphinx = implode(' ', $words_sphinx);
		
		$words_sphinx = preg_replace('~^@\* ~', '', sphinx::compile_words($searchtext));
		
		$match_fulltext_score = "MATCH (".implode(',', $fields).") AGAINST ('".es($words)."')";
		$match_fulltext = "MATCH (".implode(',', $fields).") AGAINST ('".es($words_fulltext)."' IN BOOLEAN MODE)";
		$match_sphinx = "MATCH ('@(".implode(',', $fields).") ".$words_sphinx."')";
		
		$sections = m_forum_global::get_read_section_ids($this->read_groups);
		if($this->forum[0] != 0) {
			$new = array();
			$sections = explode_arr_list($sections);
			foreach($sections as $id)
				if(in_array($id, $this->forum)) $new[] = $id;
			$sections = implode_arr_list($new);
		}
		
		$this->lang_query = get_lang_query();
		$this->is_multilang = ($this->lang_query ? false : true);
		
		switch($search_method) {
		default:
			throw new iexception('INVALID SEARCH METHOD: '.$search_method, $this);
		case 'fulltext':
			switch($this->order_by) {
			default:
			case 'score':
				$order_by = "score*forum_threads.num_hits";
				break;
			case 'hits':
				$order_by = "forum_threads.num_hits";
				break;
			case 'time':
				$order_by = "forum_posts.timeadded";
				break;
			}
			$this->results = db()->query("
				SELECT SQL_CALC_FOUND_ROWS /* ORIGINAL SEARCH: $orignial_words */
					forum_posts.post_id AS post_id,
					$match_fulltext_score AS score
				FROM forum_posts
				JOIN forum_threads USING (thread_id)
				/*LEFT JOIN users USING (user_id)*/
				WHERE".($this->user_id ? "
					forum_posts.user_id='".$this->user_id."' AND" : "")."
					forum_threads.section_id IN ($sections) AND
					$match_fulltext".($this->lang_query ? " AND
					forum_threads.lang_".$this->lang_query."=1" : "").($this->group_by == 'posts' ? '' : "
				GROUP BY thread_id")."
				ORDER BY $order_by DESC
				LIMIT ".(($this->page - 1)*FORUM_SEARCH_ROWS_PER_SITE).", ".FORUM_SEARCH_ROWS_PER_SITE);
			$this->num_rows = db()->query("SELECT FOUND_ROWS() AS num_rows")->fetch_object()->num_rows;
			break;
		case 'sphinx':
			if(!$words_sphinx) {
				set_site_title(array(LS('Forum'), LS('Suche')));
				return $this->ilphp_fetch();
			}
			if(($this->page)*FORUM_SEARCH_ROWS_PER_SITE > 1000) {
				$this->results = db()->query("SELECT 0");
				$this->results->fetch_assoc();
				$this->num_rows = 0;
				$this->num_pages = floor(1000/FORUM_SEARCH_ROWS_PER_SITE);
				break;
			}
			switch($this->order_by) {
			default:
			case 'score':
				$order_by = "@weight";
				break;
			case 'hits':
				$order_by = "num_hits";
				break;
			case 'time':
				$order_by = "timeadded";
				break;
			}
			$this->results = db('sphinx')->query("
				SELECT /* ORIGINAL SEARCH: $orignial_words */
					*
				FROM forum_search, forum_search_delta
				WHERE".($this->user_id ? "
					user_id=".(int)$this->user_id." AND" : "")."
					section_id IN ($sections) AND
					$match_sphinx".($this->lang_query ? " AND
					lang_".$this->lang_query."=1" : "").($this->group_by == 'posts' ? '' : "
				GROUP BY thread_id")."
				ORDER BY $order_by DESC
				LIMIT ".(($this->page - 1)*FORUM_SEARCH_ROWS_PER_SITE).", ".FORUM_SEARCH_ROWS_PER_SITE."
				OPTION field_weights=(name=10, content=3)");
			$this->num_rows = db('sphinx')->query("SHOW META");
			while($a = $this->num_rows->fetch_assoc()) {
				if($a['Variable_name'] == 'total') {
					$this->num_rows = $a['Value'];
					break;
				}
			}
			break;
		}
		
		if($this->num_rows) $this->num_pages = calculate_pages($this->num_rows, FORUM_SEARCH_ROWS_PER_SITE);
		#if(@$this->num_rows) $this->num_pages = $this->num_rows/FORUM_SEARCH_ROWS_PER_SITE;
		
		return $this->ilphp_fetch();
	}
	
	function search_query_result($post_id) {
		return db()->query("
			SELECT
				REPLACE(forum_posts.name, '--REDIRECT: ','') AS post_name,
				forum_posts.content AS post_content,
				forum_posts.timeadded AS post_timeadded,
				users.user_id AS user_id,
				users.nick AS user_nick,
				forum_threads.thread_id AS thread_id,
				forum_threads.lang_de AS lang_de,
				forum_threads.lang_en AS lang_en,
				forum_threads.open AS open
			FROM forum_posts
			JOIN forum_threads USING (thread_id)
			LEFT JOIN users USING (user_id)
			WHERE forum_posts.post_id='$post_id'
			LIMIT 1")->fetch_assoc();
	}
}

?>
