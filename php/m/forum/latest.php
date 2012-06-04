<?php

class m_forum_latest extends imodule {
	use ilphp_trait;
	use im_way;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		$this->url = '/'.LANG.'/forum/';
		$this->way[] = [LS('Forum'), $this->url];
	}
	
	protected function MODULE(&$args) {
		$this->namespace = $args[$this->imodule_name]; 
		
		$this->url .= 'latest/';
		$this->way[] = [LS('Die letzten 100 Beitr&auml;ge'), $this->url];
		
		switch($this->namespace) {
		default: throw new iexception('404', $this);
		case '': break;
		case 'def': $this->title = LS('Normales Forum'); break;
		case 'news': $this->title = LS('News'); break;
		case 'team': $this->title = LS('Team'); break;
		}
		
		if($this->namespace) {
			$this->url .= 'namespace/'.$this->namespace.'/';
			$this->way[] = [$this->title, $this->url];
		}
		
		$this->im_way_title();
		
		$this->ilphp_init('latest.php.ilp', 30, $this->namespace.','.implode(',', user()->groups).','.implode(',', user()->languages));
		if(($data = $this->ilphp_cache_load()) !== false) return $data;;
		
		list($read_groups, $write_groups) = m_forum_global::get_group_querys();
		$sections = m_forum_global::get_read_section_ids($read_groups, $this->namespace);
		
		$this->lang_query = get_lang_query();
		$this->is_multilang = ($this->lang_query ? false : true);
		
		$this->threads = db()->query("
			SELECT
				forum_threads.thread_id AS thread_id, forum_threads.num_posts AS thread_num_posts,
				forum_threads.lang_de AS thread_lang_de, forum_threads.lang_en AS thread_lang_en,
				REPLACE(firstpost.name, '--REDIRECT: ','') AS firstpost_name
			FROM forum_threads
			LEFT JOIN forum_posts AS firstpost ON firstpost.post_id=forum_threads.firstpost
			WHERE forum_threads.section_id IN ($sections) AND forum_threads.state!='moved'".($this->lang_query ? " AND forum_threads.lang_".$this->lang_query."=1" : "")."
			ORDER BY
				forum_threads.priority DESC,
				forum_threads.lastpost DESC
			LIMIT ".FORUM_LATEST_STEP);
		return $this->ilphp_fetch();
	}
}

?>
