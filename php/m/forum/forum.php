<?php

/*
table definitions:
forum_threads:
	state:
		NULL:
		sticky:
		important:
			normal_thread
		moved:
			firstpost = lastpost = new empty post
			forum_post values:
				uid = thread creator
				timeadded = last post time in moved thread
				lasteditor = target thread id
				lastedit = expiretime
				name = first post name in moved thread
*/	

class m_forum extends imodule {
	use ilphp_trait;
	use im_way;
	use im_pages;
	
	public $action = 'show';
	public $errors = [];
	
	private static $last_ids = [];
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	
	protected function MENU(&$args) {
		if(empty($args['namespace'])) $args['namespace'] = 'def';
		if(empty($args['limit'])) $args['limit'] = FORUM_MENU_STEP;
		
		$last_id = $this->imodule_args[$args['namespace']][$args['limit']] = $this->get_last_id($args['namespace']);
		
		$this->ilphp_init('forum.php.menu.ilp', 60, $last_id.'-'.$args['namespace'].'-'.$args['limit'].'-'.implode(',', user()->groups).'-'.implode(',', user()->languages));
		if(($data = cache_L1::get($this->ilphp_cache_file)) !== false) return $data;
		if(($data = $this->ilphp_cache_load()) !== false) {
			cache_L1::set($this->ilphp_cache_file, 20, $data);
			return $data;
		}
		
		$this->namespace =& $args['namespace'];
		
		list($read_groups, $write_groups) = m_forum_global::get_group_querys();
		$sections = m_forum_global::get_read_section_ids($read_groups, $args['namespace']);
		$this->lang_query = get_lang_query();
		$this->is_multilang = ($this->lang_query ? false : true);
		$this->threads = db()->query("
			SELECT
				forum_threads.thread_id AS thread_id, forum_threads.num_posts AS thread_num_posts,
				forum_threads.lang_de AS thread_lang_de, forum_threads.lang_en AS thread_lang_en,
				firstpost.name AS firstpost_name
			FROM forum_threads
			LEFT JOIN forum_posts AS firstpost ON firstpost.post_id=forum_threads.firstpost
			WHERE
				forum_threads.section_id IN ($sections) AND
				forum_threads.state!='moved' AND
				(forum_threads.priority=0 OR forum_threads.lang_".LANG."=1)".($this->lang_query ? " AND
				forum_threads.lang_".$this->lang_query."=1" : "")."
			ORDER BY
				forum_threads.priority DESC,
				forum_threads.lastpost DESC
			LIMIT ".(int)$args['limit']);
		if($this->threads->num_rows == 0) {
			$this->imodule_args = [];
			return;
		}
		$data = $this->ilphp_fetch();
		cache_L1::set($this->ilphp_cache_file, 10, $data);
		return $data;
	}
	private function get_last_id($namespace) {
		if(($last_id = cache_L1::get('last_forum_post_id_'.$namespace)) === false) {
			$last_id = db()->query("
				SELECT MAX(b.lastpost) id
				FROM forum_sections a
				JOIN forum_threads b USING (section_id)
				WHERE a.namespace='".es($namespace)."'")->fetch_object()->id;
			cache_L1::set('last_forum_post_id_'.$namespace, 60, $last_id);
		}
		return $last_id;
	}
	
	
	protected function IDLE(&$idle) {
		foreach($idle as $namespace=>$limits) {
			$args = ['namespace' => $namespace];
			foreach($limits as $limit=>$last_id) {
				if($last_id == $this->get_last_id($namespace)) {
					$this->imodule_args[$namespace][$limit] = $last_id;
					continue;
				}
				$args['limit'] = $limit;
				$args['last_id'] = $last_id;
				G::$json_data['e']['IM_MENU_'.$this->imodule_name.'_'.$namespace.'_'.$limit] = $this->MENU($args);
			}
		}
	}
	
	
	protected function INIT(&$args) {
		$this->url = '/'.LANG.'/forum/';
		$this->way[] = [LS('Forum'), $this->url];
		
		list($this->read_groups, $this->write_groups) = m_forum_global::get_group_querys();
		
		$this->lang_query = get_lang_query();
		$this->is_multilang = ($this->lang_query ? false : true);
		
		$this->section_id = (int)$args[$this->imodule_name];
		$this->im_pages_get(@$args['page']);
		
		$this->sections = db()->query("
			SELECT
				*, section_id id, ".LQ('name_LL')." AS name, ".LQ('description_LL')." AS description,
				(allow_threads AND ".$this->write_groups.") AS allow_write,
				".m_forum_global::get_is_mod_query()." AS is_mod, parent, namespace
			FROM forum_sections
			WHERE ".($this->section_id ? "section_id=".$this->section_id : "parent=0")." AND ".$this->read_groups."
			ORDER BY lft");
		print_r($this->read_groups);
		if($this->sections->num_rows == 0) throw new iexception('403_404', $this);
		
		if($this->section_id) {
			$this->section = $this->sections->fetch_assoc();
			$rv = db()->query("
				SELECT forum_sections.section_id AS id, ".LQ('forum_sections.name_LL')." AS name, forum_sections.is_fsk18 AS is_fsk18
				FROM forum_sections, forum_sections AS a
				WHERE a.lft BETWEEN forum_sections.lft AND forum_sections.rgt AND forum_sections.lft>1 AND a.section_id=".$this->section_id." AND ".$this->read_groups."
				GROUP BY forum_sections.section_id
				ORDER BY forum_sections.lft");
			while($r = $rv->fetch_assoc()) {
				if($r['is_fsk18'] and !session::$s['verified_fsk18'])
					return view_fsk18_blocked();
				$this->way[] = [$r['name'], $this->url.$r['id'].'-'.urlenc($r['name']).'/'];
			}
			$this->url .= $this->section['id'].'-'.urlenc($this->section['name']).'/';
			if($this->page > 1) $this->im_pages_way();
		}
	}
	
	protected function MODULE(&$args) {
		if($this->section_id and !empty($args['new']))
			return $this->overview_post($args);
		
		if($this->action != 'show' or has_forumrights()) $cache_id = NULL;
		else {
			$cache_id = md5(LANG.'-'.$this->section_id.'-'.$this->page.print_r($this->read_groups, true).print_r($this->write_groups, true).'-'.implode(',', user()->languages));
			if($data = cache_L2::get($cache_id)) return $data;
		}
		
		$this->ilphp_init('forum.php.ilp');
		$this->im_way_title();
		if($cache_id) {
			$data = $this->ilphp_fetch();
			cache_L2::set($cache_id, 10, $data);
			return $data;
		}
		return $this->ilphp_fetch();
	}
	
	protected function POST_content_edit(&$args) {
		if(!$this->section['is_mod']) throw new iexception('403', $this);
		$this->action = 'content_edit';
		if(IS_AJAX) return $this->MODULE($args);
	}
	
	protected function POST_content_save(&$args) {
		if(!$this->section['is_mod']) throw new iexception('403', $this);
		$content = es($args['content']);
		db()->query("UPDATE forum_sections SET content='$content' WHERE section_id='".$this->section['section_id']."' LIMIT 1");
		page_redir($this->url);
	}
	
	protected function POST_new(&$args) {
		if(!$this->section['allow_write']) throw new iexception('403', $this);
		
		$this->action = 'new';
		$this->way[] = [LS('Neuer Thread'), ''];
		
		$this->possible_languages = array();
		get_lang_query($this->possible_languages);
		
		if(IS_AJAX) return $this->MODULE($args);
	}
	protected function POST_save(&$args) {
		if(!$this->section['allow_write']) throw new iexception('403', $this);
		
		$this->thread_name = $args['name'];
		$this->thread_content = $args['content'];
		
		if(!empty($args['prefix'])) $this->thread_name = sprintf('[%s] %s', $args['prefix'], $this->thread_name);
		if(mb_strlen($this->thread_name) < 4) $this->errors[] = LS('Der Threadtitel ist zu kurz.');
		if(mb_strlen($this->thread_content) < 4) $this->errors[] = LS('Der Threadinhalt ist zu kurz.');
		$lang_de = (!empty($args['lang_de']) ? 1 : 0);
		$lang_en = (!empty($args['lang_en']) ? 1 : 0);
		if(!$lang_de and !$lang_en) $this->errors[] = LS('Du musst mindestens eine Sprache ausw&auml;hlen.');
		if($this->errors) return $this->POST_new($args);
		
		db()->query("INSERT INTO forum_threads SET section_id='".$this->section_id."', num_posts='1', lang_de='$lang_de', lang_en='$lang_en'");
		$this->thread_id = db()->insert_id;
		db()->query("INSERT INTO forum_posts SET thread_id='".$this->thread_id."', user_id='".USER_ID."', name='".es($this->thread_name)."', content='".es($this->thread_content)."'");
		$post_id = db()->insert_id;
		db()->query("UPDATE forum_threads SET firstpost='$post_id', lastpost='$post_id' WHERE thread_id='".$this->thread_id."' LIMIT 1");
		db()->query("
			UPDATE forum_sections
			SET
				num_threads=num_threads+1".($lang_de ? ",
				num_threads_de=num_threads_de+1" : "").($lang_en ? ",
				num_threads_en=num_threads_en+1" : "").",
				num_posts=num_posts+1".($lang_de ? ",
				num_posts_de=num_posts_de+1" : "").($lang_en ? ",
				num_posts_en=num_posts_en+1" : "").",
				lastthread='".$this->thread_id."'".($lang_de ? ",
				lastthread_de='".$this->thread_id."'" : "").($lang_en ? ",
				lastthread_en='".$this->thread_id."'" : "")."
			WHERE section_id='".$this->section_id."'
			LIMIT 1");
		db()->query("UPDATE LOW_PRIORITY users SET forum_posts=forum_posts+1 WHERE user_id='".USER_ID."' LIMIT 1");
		if($this->section_id) cache_L1::del('last_forum_post_id_'.$this->section['namespace']);
		page_redir('/'.LANG.'/thread/'.$this->thread_id.'-'.urlenc($this->thread_name).'/');
	}
	
	public function query_child_sections($parent) {
		return db()->query("
			SELECT
				forum_sections.section_id AS section_id, ".LQ('forum_sections.name_LL')." AS section_name, ".LQ('forum_sections.description_LL')." AS section_description,
				forum_sections.num_threads".($this->lang_query ? "_".$this->lang_query : "")." AS section_num_threads,
				forum_sections.num_posts".($this->lang_query ? "_".$this->lang_query : "")." AS section_num_posts,
				lastthread.thread_id AS lastthread_id, lastthread.lang_de AS lastthread_lang_de, lastthread.lang_en AS lastthread_lang_en,
				lastthread.num_posts AS lastthread_num_posts, lastthread.state AS lastthread_state,
				REPLACE(firstpost.name, '--REDIRECT: ', '') AS firstpost_name, UNIX_TIMESTAMP(lastpost.timeadded) AS lastpost_time,
				users.user_id AS lastpost_uid, users.nick AS lastpost_nick, users.groups AS lastpost_groups,
				forum_sections.parent AS section_parent,
				COUNT(*)-1 AS level,
				ROUND((forum_sections.rgt-forum_sections.lft-1)/2) AS childs
			FROM forum_sections IGNORE INDEX (read_groups)
			JOIN forum_sections AS a
			".($parent ? "JOIN forum_sections AS b" : "")."
			LEFT JOIN forum_threads AS lastthread ON lastthread.thread_id=forum_sections.lastthread".($this->lang_query ? "_".$this->lang_query : "")."
			LEFT JOIN forum_posts AS firstpost ON firstpost.post_id=lastthread.firstpost
			LEFT JOIN forum_posts AS lastpost ON lastpost.post_id=lastthread.lastpost
			LEFT JOIN users ON users.user_id=lastpost.user_id
			WHERE
				forum_sections.lft BETWEEN a.lft AND a.rgt
				".($parent ? "AND forum_sections.lft BETWEEN b.lft AND b.rgt AND b.section_id=".(int)$parent." AND forum_sections.lft>b.lft" : "")." AND
				".$this->read_groups."
			GROUP BY forum_sections.lft
			ORDER BY forum_sections.lft");
	}
	
	public function query_child_threads() {
		if(($this->page - 1)*FORUM_THREAD_NUM_THREADS_PER_SITE > $this->section['num_threads'])
			$this->page = floor($this->section['num_threads']/FORUM_THREAD_NUM_THREADS_PER_SITE);
		
		$this->rvs = [];
		$this->rvs[] = $this->query_child_threads_q("section_id='".$this->section['id']."' AND state='important' ORDER BY forum_threads.lastpost DESC");
		$this->rvs[] = $this->query_child_threads_q("section_id='".$this->section['id']."' AND state='sticky' ORDER BY forum_threads.lastpost DESC");
		$this->rvs[] = $this->query_child_threads_q("section_id='".$this->section['id']."' AND (state='' OR state='moved') ORDER BY forum_threads.lastpost DESC LIMIT ".(($this->page - 1)*FORUM_THREAD_NUM_THREADS_PER_SITE).", ".FORUM_THREAD_NUM_THREADS_PER_SITE, "SQL_CALC_FOUND_ROWS");
		$this->num_posts = db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num;
		$this->has_posts = (($this->num_posts or $this->rvs[0]->num_rows) ? true : false);
		$this->num_pages = floor($this->num_posts/FORUM_THREAD_NUM_THREADS_PER_SITE);
		
		$this->mods = NULL;
		if($this->has_posts) {
			$section['mods'] = trim($this->section['mods'], ',');
			if($this->section['mods']) $this->mods = db()->query("SELECT user_id id, nick FROM users WHERE user_id IN (".$this->section['mods'].") AND user_id!=6312 ORDER BY id");
		}
	}
	private function query_child_threads_q($mod, $select_mod = '') {
		return db()->query("
			SELECT $select_mod
				forum_threads.thread_id AS thread_id, forum_threads.lang_de AS thread_lang_de, forum_threads.lang_en AS thread_lang_en,
				forum_threads.num_posts AS thread_num_posts, forum_threads.num_hits AS thread_num_hits,
				forum_threads.state AS thread_state, forum_threads.priority AS thread_priority, forum_threads.open AS thread_open,
				
				firstpost.post_id AS firstpost_id, REPLACE(firstpost.name,'--REDIRECT: ','') AS firstpost_name, UNIX_TIMESTAMP(firstpost.timeadded) AS firstpost_time,
				
				firstpost_user.user_id AS firstpost_user_id,
				firstpost_user.nick AS firstpost_user_nick,
				
				UNIX_TIMESTAMP(lastpost.timeadded) AS lastpost_time,
				lastpost_user.user_id AS lastpost_user_id,
				lastpost_user.nick AS lastpost_user_nick,
				
				UNIX_TIMESTAMP(CURRENT_TIMESTAMP) AS _now
			FROM forum_threads
			LEFT JOIN forum_posts AS firstpost ON firstpost.post_id=forum_threads.firstpost
			LEFT JOIN forum_posts AS lastpost ON lastpost.post_id=forum_threads.lastpost
			LEFT JOIN users AS firstpost_user ON firstpost_user.user_id=firstpost.user_id
			LEFT JOIN users AS lastpost_user ON lastpost_user.user_id=lastpost.user_id
			WHERE".($this->lang_query ? " forum_threads.lang_".$this->lang_query."=1 AND" : "")." $mod");
	}
	
	public function query_last_editor($post_id) {
		return db()->query("SELECT lasteditor FROM forum_posts WHERE post_id='$post_id' LIMIT 1")->fetch_object()->lasteditor;
	}
}

?>
