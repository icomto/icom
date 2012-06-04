<?php

define('PROFILE_THREADS_STEP', 50);

class m_activities extends im_tabs {
	use ilphp_trait;
	use im_pages;
	
	protected $im_tabs_var = 'upat';
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		if(!IS_LOGGED_IN) throw new iexception('ACCESS_DENIED', $this);
		
		$user_id = (int)$args[$this->imodule_name];
		$this->user = db()->query("SELECT * FROM users WHERE user_id='".$user_id."' LIMIT 1")->fetch_assoc();
		if(!$this->user) throw new iexception('USER_NOT_FOUND', $this);
		
		if($this->user['deleted']) throw new iexception('USER_DELETED', $this);
		
		$this->url = '/'.LANG.'/activities/'.$this->user['user_id'].'-'.urlenc($this->user['nick']).'/upat/';
		$this->way[] = [LS('Aktivit&auml;ten von %1%', $this->user['nick']), $this->url];
		
		$this->im_tabs_add('threads', LS('Threads'), TAB_SELF);
		$this->im_tabs_add('posts', LS('Beitr&auml;ge'), TAB_SELF);
		$this->im_tabs_add('wiki', LS('Wiki'), TAB_SELF);
		$this->im_tabs_add('profile', LS('Zum Profil'), TAB_SELF);
		
		parent::INIT($args);
	}
	
	protected function TAB_threads(&$args) {
		$this->im_pages_get(@$args['page']);
		$this->im_pages_way();
		
		$lang = get_lang_query();
		
		$cache_id = LANG.'_'.$lang.'_'.$this->user['user_id'].'_'.implode(',', user()->groups).'_'.implode(',', user()->languages);
		$this->ilphp_init('activities.php.threads.ilp', 5*60, $cache_id.'_'.$this->page);
		if(($data = $this->ilphp_cache_load()) !== false) return $data;
		
		list($read_groups, $write_groups) = m_forum_global::get_group_querys();
		$sections = m_forum_global::get_read_section_ids($read_groups);
		$this->is_multilang = ($lang ? false : true);
		$where = array();
		$where[] = "firstpost.user_id='".$this->user['user_id']."'";
		$where[] = "forum_threads.section_id IN ($sections)";
		$where[] = "forum_threads.state!='moved'";
		if($lang) $where[] = "forum_threads.lang_$lang=1";
		
		$num_found_rows = cache_L1::get('profile_activities_threads_'.$cache_id);
		$this->threads = db()->query("
			SELECT".($num_found_rows === false ? " SQL_CALC_FOUND_ROWS" : "")."
				forum_threads.thread_id AS thread_id, forum_threads.num_posts AS thread_num_posts,
				forum_threads.lang_de AS thread_lang_de, forum_threads.lang_en AS thread_lang_en,
				REPLACE(firstpost.name, '--REDIRECT: ','') AS firstpost_name
			FROM forum_threads
			JOIN forum_posts AS firstpost ON firstpost.post_id=forum_threads.firstpost
			WHERE ".implode(" AND ", $where)."
			GROUP BY forum_threads.thread_id
			ORDER BY MAX(forum_threads.lastpost) DESC
			LIMIT ".(($this->page - 1)*PROFILE_THREADS_STEP).", ". PROFILE_THREADS_STEP);
		if($num_found_rows === false) {
			$num_found_rows = db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num;
			cache_L1::set('profile_activities_threads_'.$cache_id, 30*60, $num_found_rows);
		}
		$this->num_pages = calculate_pages($num_found_rows, PROFILE_THREADS_STEP);
		return $this->ilphp_fetch();
	}
	
	protected function TAB_posts(&$args) {
		$this->im_pages_get(@$args['page']);
		$this->im_pages_way();
		
		$lang = get_lang_query();
		
		$cache_id = LANG.'_'.$lang.'_'.$this->user['user_id'].'_'.implode(',', user()->groups).'_'.implode(',', user()->languages);
		$this->ilphp_init('activities.php.posts.ilp', 5*60, $cache_id.'_'.$this->page);
		if(($data = $this->ilphp_cache_load()) !== false) return $data;
		
		list($read_groups, $write_groups) = m_forum_global::get_group_querys();
		$sections = m_forum_global::get_read_section_ids($read_groups);
		$this->is_multilang = ($lang ? false : true);
		$where = array();
		$where[] = "userpost.user_id='".$this->user['user_id']."'";
		$where[] = "forum_threads.section_id IN ($sections)";
		$where[] = "forum_threads.state!='moved'";
		if($lang) $where[] = "forum_threads.lang_$lang=1";
		
		$num_found_rows = cache_L1::get('profile_activities_posts_'.$cache_id);
		$this->posts = db()->query("
			SELECT".($num_found_rows === false ? " SQL_CALC_FOUND_ROWS" : "")."
				forum_threads.thread_id AS thread_id, forum_threads.num_posts AS thread_num_posts,
				forum_threads.lang_de AS thread_lang_de, forum_threads.lang_en AS thread_lang_en,
				REPLACE(firstpost.name, '--REDIRECT: ','') AS firstpost_name,
				userpost.post_id AS post_id
			FROM forum_threads
			JOIN forum_posts AS firstpost ON firstpost.post_id=forum_threads.firstpost
			JOIN forum_posts AS userpost ON userpost.thread_id=forum_threads.thread_id
			WHERE ".implode(" AND ", $where)."
			GROUP BY userpost.post_id
			ORDER BY userpost.timeadded DESC
			LIMIT ".(($this->page - 1)*PROFILE_THREADS_STEP).", ". PROFILE_THREADS_STEP);
		if($num_found_rows === false) {
			$num_found_rows = db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num;
			cache_L1::set('profile_activities_posts_'.$cache_id, 30*60, $num_found_rows);
		}
		$this->num_pages = calculate_pages($num_found_rows, PROFILE_THREADS_STEP);
		return $this->ilphp_fetch();
	}
	public function posts_row() {
		$this->i['num_lower_posts'] = db()->query("SELECT COUNT(*) AS num FROM forum_posts WHERE thread_id='".$this->i['thread_id']."' AND post_id<='".$this->i['post_id']."'")->fetch_object()->num;
		$this->i['page'] = calculate_pages($this->i['num_lower_posts'], FORUM_THREAD_NUM_POSTS_PER_SITE);
	}
	
	
	protected function TAB_wiki(&$args) {
		$this->im_pages_get(@$args['page']);
		$this->im_pages_way();
		
		$lang = get_lang_query();
		
		$cache_id = $this->user['user_id'].'_'.$lang;
		$this->ilphp_init('activities.php.wiki.ilp', 5*60, $cache_id.'_'.$this->page);
		if(($data = $this->ilphp_cache_load()) !== false) return $data;
		
		$this->is_multilang = ($lang ? false : true);
		
		$num_found_rows = cache_L1::get('profile_activities_wiki_'.$cache_id);
		$this->pages = db()->query("
			SELECT".($num_found_rows === false ? " SQL_CALC_FOUND_ROWS" : "")."
				p.lang, p.name
			FROM wiki_pages p, wiki_changes a
			LEFT JOIN wiki_changes b ON b.history=a.history AND b.action='history_activated' AND b.id>a.id
			WHERE
				a.user='".$this->user['user_id']."' AND
				a.page=p.id AND
				a.action IN ('article_created','content_changed') AND
				NOT b.id IS NULL".($lang ? " AND
				p.lang='$lang'" : "")."
			GROUP BY p.id
			ORDER BY MAX(a.id) DESC
			LIMIT ".(($this->page - 1)*PROFILE_THREADS_STEP).", ". PROFILE_THREADS_STEP);
		if($num_found_rows === false) {
			$num_found_rows = db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num;
			cache_L1::set('profile_activities_wiki_'.$cache_id, 30*60, $num_found_rows);
		}
		$this->num_pages = calculate_pages($num_found_rows, PROFILE_THREADS_STEP);
		return $this->ilphp_fetch();
	}
	
	
	protected function TAB_profile() {
		page_redir('/'.LANG.'/users/'.$this->user['user_id'].'-'.urlenc($this->user['nick']).'/');
	}
}

?>
