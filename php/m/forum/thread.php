<?php

class m_forum_thread extends imodule {
	use ilphp_trait;
	use im_way;
	use im_pages;
	
	public $action = '';
	
	public $errors = [];
	public $fatal_errors = [];
	public $post_name = '';
	public $post_content = '';
	
	public $post_reason = '';
	
	public $edit_post_id = '';
	public $report_post_id = '';
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		$this->thread_id = es($args[$this->imodule_name]);
		
		list($this->read_groups, $this->write_groups) = m_forum_global::get_group_querys();
		
		$this->root = db()->query("
			SELECT
				forum_threads.thread_id AS thread_id, forum_threads.lang_de AS thread_lang_de, forum_threads.lang_en AS thread_lang_en,
				forum_threads.num_posts AS thread_posts, forum_threads.lastpost AS lastpost_id,
				forum_threads.state AS forum_state, forum_threads.priority AS forum_priority,
				forum_threads.open AS open, forum_threads.closed_by_mod AS closed_by_mod,
				
				forum_sections.section_id AS section_id, forum_sections.parent AS section_parent, ".LQ('forum_sections.name_LL')." AS section_name,
				forum_sections.namespace AS section_namespace,
				".m_forum_global::get_is_mod_query()." AS is_mod,
				
				(forum_sections.allow_threads AND {$this->write_groups}) AS allow_write,
				forum_sections.write_groups,
				
				forum_posts.post_id AS firstpost_id, forum_posts.name AS firstpost_name, forum_posts.user_id AS firstpost_uid,
				forum_posts.timeadded AS firstpost_timeadded
			FROM forum_threads
			LEFT JOIN forum_sections USING (section_id)
			LEFT JOIN forum_posts ON forum_posts.post_id=forum_threads.firstpost
			WHERE forum_threads.thread_id='{$this->thread_id}' AND {$this->read_groups}
			LIMIT 1")->fetch_assoc();
		if(!$this->root) throw new iexception('404', $this);
		
		if(preg_match('~^--REDIRECT: ~', $this->root['firstpost_name'])) {
			$a = db()->query("SELECT content FROM forum_posts WHERE post_id='".$this->root['firstpost_id']."' LIMIT 1")->fetch_assoc();
			page_redir($a['content']);
		}
		
		$this->im_pages_get(@$args['page']);
		
		$this->url = '/'.LANG.'/thread/'.$this->root['thread_id'].'-'.urlenc($this->root['firstpost_name']).'/';
		$this->url_page = $this->url.($this->page > 1 ? 'page/'.$this->page.'/' : '');
		
		if($this->root['is_mod']) { //initialize moderator stuff
			$this->LANG_NAMES =& G::$LANG_NAMES;
			$this->FORUM_STATES = array('normal'=>'normal', 'wichtig'=>'important', 'angepinnt'=>'sticky');
			$this->FORUM_STATES2 = array('normal'=>'normal', '1.Platz'=>'1stonlast', '2.Platz'=>'2ndonlast');
			$this->forum_sections = db()->query("
				SELECT
					forum_sections.section_id AS id,
					".LQ('forum_sections.name_LL')." AS name,
					".m_forum_global::get_is_mod_query()." AS is_mod,
					".$this->write_groups." AS allow_write,
					COUNT(*)-2 AS level
				FROM forum_sections, forum_sections AS a
				WHERE
					".$this->read_groups." AND
					forum_sections.lft BETWEEN a.lft AND a.rgt
				GROUP BY forum_sections.lft
				ORDER BY forum_sections.lft");
		}
		
		if($this->root['thread_posts'] and ($this->page - 1)*FORUM_THREAD_NUM_POSTS_PER_SITE + 1 > $this->root['thread_posts']) {
			$this->page = ceil(($this->root['thread_posts'] - 1)/FORUM_THREAD_NUM_POSTS_PER_SITE);
			page_redir($this->url.($this->page > 1 ? 'page/'.$this->page.'/' : ''));
		}
		
		$this->way = [[LS('Forum'), '/'.LANG.'/forum/']];
		
		$aa = db()->query('
			SELECT forum_sections.section_id, '.LQ('forum_sections.name_LL').' AS name, forum_sections.is_fsk18 AS is_fsk18
			FROM forum_sections, forum_sections AS a
			WHERE a.lft BETWEEN forum_sections.lft AND forum_sections.rgt AND forum_sections.lft>1 AND a.section_id='.$this->root['section_id'].'
			GROUP BY forum_sections.section_id
			ORDER BY forum_sections.lft');
		$this->is_possible_solved_thread = false;
		while($a = $aa->fetch_assoc()) {
			if($a['is_fsk18'] and !session::$s['verified_fsk18'])
				return view_fsk18_blocked();
			$this->way[] = [$a['name'], '/'.LANG.'/forum/'.$a['section_id'].'-'.urlenc($a['name']).'/'];
			switch($a['section_id']) {
			case 71://Boerse -> Suche
				$this->is_possible_solved_thread = true;
				break;
			case 151://Boerse -> Suche -> Erledigt
				$this->is_possible_solved_thread = false;
				break;
			}
		}
		$this->way[] = [$this->root['firstpost_name'], $this->url];
		if($this->page > 1) $this->im_pages_way();
	}
	
	protected function MODULE(&$args) {
		$this->im_way_title();
		
		/*if($this->is_possible_solved_thread) {
			if($this->root['is_mod'] or (IS_LOGGED_IN and $this->root['firstpost_uid'] == USER_ID)) {
				if(isset($args['solved'])) {
					db()->query("UPDATE forum_threads SET section_id='151' WHERE thread_id='".$this->root['thread_id']."' LIMIT 1");
					db()->query("UPDATE forum_posts SET name='".es('[E]'.$this->root['firstpost_name'])."' WHERE post_id='".$this->root['firstpost_id']."' LIMIT 1");
					$this->way[] = array('Thread erledigt', '');
					$this->action = 'moved_to_solved';
					return $this->ilphp_fetch();
				}
			}
			else
				$this->is_possible_solved_thread = false;
		}*/
		
		
		
		$this->posts = db()->query("
			SELECT SQL_CALC_FOUND_ROWS
				forum_posts.post_id AS post_id, UNIX_TIMESTAMP(forum_posts.timeadded) AS post_timeadded,
				REPLACE(forum_posts.name, '--REDIRECT: ','') AS post_name, forum_posts.content AS post_content,
				forum_posts.thanks AS post_thanks,
				".(IS_LOGGED_IN ? "(".USER_ID."=forum_posts.user_id OR FIND_IN_SET(".USER_ID.",forum_posts.thanks))" : "0")." AS post_user_thanked,
				UNIX_TIMESTAMP(forum_posts.lastedit) AS post_lastedit,
				
				post_user.user_id AS user_id, post_user.nick AS user_nick, post_user.groups AS user_groups,
				UNIX_TIMESTAMP(post_user.regtime) AS user_regtime, post_user.forum_posts AS user_posts,
				post_user.avatar_img AS user_avatar, post_user.signature AS user_signature,
				post_user.points AS user_points,
				
				forum_posts.lasteditor AS lasteditor_id, lasteditor_user.nick AS lasteditor_nick,
					
				UNIX_TIMESTAMP(CURRENT_TIMESTAMP) AS _now
			FROM forum_posts
			LEFT JOIN users AS post_user USING (user_id)
			LEFT JOIN users AS lasteditor_user ON lasteditor_user.user_id=forum_posts.lasteditor
			WHERE forum_posts.thread_id='{$this->thread_id}'
			ORDER BY forum_posts.post_id
			LIMIT ".(($this->page - 1)*FORUM_THREAD_NUM_POSTS_PER_SITE).", ".FORUM_THREAD_NUM_POSTS_PER_SITE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, FORUM_THREAD_NUM_POSTS_PER_SITE);
		
		if(IS_LOGGED_IN) $query = "INSERT IGNORE INTO forum_threads_visited_users SET thread_id='".$this->root['thread_id']."', user_id='".USER_ID."'";
		elseif(USING_COOKIES) $query = "INSERT IGNORE INTO forum_threads_visited_guests SET thread_id='".$this->root['thread_id']."', guest_id=UNHEX('".es(USER_ID)."')";
		else $query = '';
		if($query) {
			db()->query($query);
			if(db()->affected_rows) db()->query("UPDATE forum_threads SET num_hits=num_hits+1 WHERE thread_id='".$this->root['thread_id']."' LIMIT 1");
		}
		
		return $this->ilphp_fetch('thread.php.ilp');
	}
	
	
	
	public function query_reports() {
		$this->reports = db()->query("
			SELECT
				reported.id AS id,
				reported.timeadded AS timeadded,
				reported.reason AS reason,
				users.user_id AS user_id,
				users.nick AS user_nick
			FROM forum_reported_posts AS reported
			JOIN users USING (user_id)
			WHERE
				reported.post_id='".$this->i['post_id']."' AND
				reported.open='1'
			ORDER BY reported.id");
		return $this->ilphp_fetch('thread.php.reports.ilp');
	}
	
	
	
	protected function POST_quote(&$args) {
		$post_id = (int)$args['post_id'];
		$post = db()->query("SELECT user_id, REPLACE(name, '--REDIRECT: ','') name, content FROM forum_posts WHERE thread_id='{$this->thread_id}' AND post_id='{$post_id}' LIMIT 1")->fetch_assoc();
		if($post) {
			$this->post_name = $post['name'];
			$this->post_content =
				'[quote='.user($post['user_id'])->nick.']'.
				preg_replace('~\[(thx|thanked)([ \t]*=[^\]]+)?\].*?\[/(thx|thanked)\]~si', '[quote]Bedanken-Spoiler entfernt / Thanks-Spoiler removed[/quote]', $post['content']).
				'[/quote]'."\n\n";
		}
		return $this->POST_reply($args);
	}
	
	protected function POST_reply(&$args) {
		$this->action = 'reply';
		
		if(!IS_LOGGED_IN or ((!$this->root['allow_write'] or !$this->root['open']) and !$this->root['is_mod']))
			throw new iexception('403', $this);
		
		if(isset($args['init'])) {
			if(!$this->post_name) $this->post_name = $this->root['firstpost_name'];
			if(!preg_match('~^Re: ~', $this->post_name)) $this->post_name = 'Re: '.$this->post_name;
			return IS_AJAX ? $this->MODULE($args) : NULL;
		}
		
		if(!isset($args['name']) or !isset($args['content']))
			return IS_AJAX ? $this->ilphp_fetch('thread.php.reply.ilp') : NULL;
		
		$this->post_name = $args['name'];
		$this->post_content = $args['content'];
		if(mb_strlen($this->post_name) < 4) $this->errors[] = LS('Der Titel ist zu kurz.');
		if(mb_strlen($this->post_content) < 4) $this->errors[] = LS('Der Inhalt ist zu kurz.');
		
		if($this->errors)
			return $this->ilphp_fetch('thread.php.reply.ilp');
		
		db()->query("INSERT INTO forum_posts SET thread_id='{$this->thread_id}', user_id='".USER_ID."', name='".es($this->post_name)."', content='".es($this->post_content)."'");
		$post_id = db()->insert_id;
		db()->query("UPDATE forum_threads SET num_posts=num_posts+1, lastpost='$post_id' WHERE thread_id='{$this->thread_id}' LIMIT 1");
		db()->query("UPDATE forum_sections SET num_posts=num_posts+1, lastthread='{$this->thread_id}' WHERE section_id='".$this->root['section_id']."' LIMIT 1");
		db()->query("UPDATE LOW_PRIORITY users SET forum_posts=forum_posts+1 WHERE user_id='".USER_ID."' LIMIT 1");
		
		cache_L1::del('last_forum_post_id_'.$this->root['section_namespace']);
		$num_lower_posts = db()->query("SELECT COUNT(*) AS num FROM forum_posts WHERE thread_id='".$this->thread_id."' AND post_id<='".$post_id."'")->fetch_object()->num;
		page_redir($this->url_page.'#post'.$num_lower_posts);
	}
	
	
	
	protected function POST_edit(&$args) {
		$this->action = 'edit';
		
		if(!isset($this->i)) { //only happens with IS_AJAX and !init
			$post_id = (int)$args['post_id'];
			$this->i = db()->query("
				SELECT post_id AS post_id, REPLACE(name, '--REDIRECT: ','') AS post_name, content AS post_content, user_id
				FROM forum_posts
				WHERE thread_id='{$this->thread_id}' AND post_id='{$post_id}'
				LIMIT 1")->fetch_assoc();
			if(!$this->i) throw new iexception('404', $this);
		}
		
		if(!($this->root['is_mod'] or (IS_LOGGED_IN and $this->root['open'] and $this->i['user_id'] == USER_ID and $this->root['allow_write'])))
			throw new iexception('403', $this);
		
		if(!isset($this->while_i)) { //only happens with IS_AJAX and !init
			$this->while_i = db()->query("SELECT COUNT(*) AS num FROM forum_posts WHERE thread_id='".$this->thread_id."' AND post_id<='".$this->i['post_id']."'")->fetch_object()->num;
			$this->page = calculate_pages($this->while_i, FORUM_THREAD_NUM_POSTS_PER_SITE);
		}
		
		$this->edit_post_id = $this->i['post_id'];
		
		if(isset($args['init'])) {
			$this->post_name = $this->i['post_name'];
			$this->post_content = $this->i['post_content'];
			return IS_AJAX ? $this->MODULE($args) : NULL;
		}
		
		$this->ilphp_init('thread.php.edit.ilp');
		
		if(!isset($args['name']) or !isset($args['content']))
			return IS_AJAX ? $this->ilphp_fetch('thread.php.edit.ilp') : NULL;
		
		$this->post_name = $args['name'];
		$this->post_content = $args['content'];
		if(mb_strlen($this->post_name) < 4) $this->errors[] = LS('Der Titel ist zu kurz.');
		if(mb_strlen($this->post_content) < 4) $this->errors[] = LS('Der Inhalt ist zu kurz.');
		
		$lang_de = (!empty($args['lang_de']) ? 1 : 0);
		$lang_en = (!empty($args['lang_en']) ? 1 : 0);
		
		if($this->root['firstpost_id'] == $this->i['post_id'] and !$lang_de and !$lang_en)
			$this->errors[] = LS('Du musst mindestens eine Sprache ausw&auml;hlen.');
		
		if($this->errors)
			return IS_AJAX ? $this->ilphp_fetch('thread.php.edit.ilp') : NULL;
		
		db()->query("
			UPDATE forum_posts
			SET
				name='".es($this->post_name)."',
				content='".es($this->post_content)."',
				lastedit=CURRENT_TIMESTAMP,
				lasteditor='".USER_ID."'
			WHERE post_id='".$this->i['post_id']."'
			LIMIT 1");
		if($this->root['firstpost_id'] == $this->i['post_id']) {
			db()->query("UPDATE forum_threads SET lang_de='$lang_de', lang_en='$lang_en' WHERE thread_id='".$this->root['thread_id']."' LIMIT 1");
			function ___change_lang_update(&$root, $extra, $add) {
				$lastthread = db()->query("
					SELECT forum_threads.thread_id AS id
					FROM forum_threads
					LEFT JOIN forum_posts USING (thread_id)
					WHERE forum_threads.section_id='".$root['section_id']."' AND forum_threads.lang$extra='1'
					ORDER BY forum_posts.timeadded DESC
					LIMIT 1")->fetch_assoc();
				$lastthread = ($lastthread ? $lastthread['id'] : 0);
				db()->query("
					UPDATE forum_sections
					SET
						num_threads$extra=num_threads$extra".($add ? '+' : '-')."1,
						num_posts$extra=num_posts$extra".($add ? '+' : '-').$root['thread_posts'].",
						lastthread$extra=$lastthread
					WHERE section_id='".$root['section_id']."'
					LIMIT 1");
			}
			if($this->root['thread_lang_de'] != $lang_de) ___change_lang_update($this->root, '_de', $lang_de);
			if($this->root['thread_lang_en'] != $lang_en) ___change_lang_update($this->root, '_en', $lang_en);
		}
		
		if($this->root['is_mod'] and $this->i['user_id'] != USER_ID)
			bigbrother('forum_post_edit', array($this->thread_id, $this->i['post_id'], $this->i['user_id']));
		
		page_redir($this->url_page.'#post'.$this->while_i);
	}
	
	
	
	protected function POST_report(&$args) {
		$this->action = 'report';
		
		if(!($this->root['is_mod'] or (IS_LOGGED_IN and $this->root['allow_write'])))
			throw new iexception('403', $this);
		
		if(!isset($this->i)) { //only happens with IS_AJAX and !init
			$post_id = (int)$args['post_id'];
			$this->i = db()->query("
				SELECT post_id
				FROM forum_posts
				WHERE thread_id='{$this->thread_id}' AND post_id='{$post_id}'
				LIMIT 1")->fetch_assoc();
			if(!$this->i) throw new iexception('404', $this);
		}
		if(!isset($this->while_i)) { //only happens with IS_AJAX and !init
			$this->while_i = db()->query("SELECT COUNT(*) AS num FROM forum_posts WHERE thread_id='".$this->thread_id."' AND post_id<='".$this->i['post_id']."'")->fetch_object()->num;
			$this->page = calculate_pages($this->while_i, FORUM_THREAD_NUM_POSTS_PER_SITE);
		}
		
		$this->report_post_id = $this->i['post_id'];
		
		if(db()->query("SELECT 1 FROM forum_reported_posts WHERE post_id='".$this->i['post_id']."' AND open=1 LIMIT 1")->num_rows)
			$this->fatal_errors[] = LS('Dieser Beitrag hat bereits offene Tickets die erst bearbeitet werden m&uuml;ssen.');
		elseif(db()->query("SELECT 1 FROM forum_reported_posts WHERE post_id='".$this->i['post_id']."' AND open=0 AND was_good_ticket=1 LIMIT 1")->num_rows)
			$this->fatal_errors[] = LS('Dieser Beitrag wurde bereits gemeldet und das Ticket als gut eingestuft.');
		
		if(isset($args['init']))
			return IS_AJAX ? $this->MODULE($args) : NULL;
		
		if($this->errors or !isset($args['reason']))
			return IS_AJAX ? $this->ilphp_fetch('thread.php.report.ilp') : NULL;
		
		$reason = es($args['reason']);
		if(mb_strlen($reason) < 4) $this->errors[] = LS('Der Grund ist zu kurz.');
		
		if($this->errors)
			return IS_AJAX ? $this->ilphp_fetch('thread.php.report.ilp') : NULL;
		
		db()->query("INSERT INTO forum_reported_posts SET post_id='".$this->i['post_id']."', namespace='".es($this->root['section_namespace'])."', reason='$reason', user_id='".USER_ID."'");
		page_redir($this->url_page.'#post'.$this->while_i);
	}
	
	
	
	
	
	protected function POST_thanks(&$args) {
		if(!IS_LOGGED_IN) throw new iexception('403', $this);
		$post_id = (int)$args['post_id'];
		$post = db()->query("SELECT thanks FROM forum_posts WHERE post_id='".$post_id."' AND thread_id='{$this->thread_id}' LIMIT 1")->fetch_assoc();
		if($post) {
			$post['thanks'] = explode_arr_list($post['thanks']);
			if(!in_array(USER_ID, $post['thanks'])) {
				$post['thanks'][] = USER_ID;
				db()->query("UPDATE forum_posts SET thanks='".implode_arr_list($post['thanks'])."' WHERE post_id='".$post_id."' LIMIT 1");
			}
			if(IS_AJAX) {
				$this->i = [
					'post_id' => $post_id,
					'post_thanks' => $post['thanks']
				];
				G::$json_data['e']['thread_thanks_'.$post_id] = $this->ilphp_fetch('thread.php.post.ilp|thanks');
				G::$json_data['s'][] = "$('#thread_thanks_$post_id').show();";
				return '';
			}
			else page_redir($this->url.($this->page > 1 ? 'page/'.$this->page.'/' : ''));
		}
		return;
	}
	
	protected function POST_openclose(&$args) {
		if(!IS_LOGGED_IN) throw new iexception('403', $this);
		if($this->root['firstpost_uid'] != USER_ID and !$this->root['is_mod'])
			return $this->post_moderator_end($args);
		$closed_by_mod = ($this->root['firstpost_uid'] == USER_ID ? 0 : 1);
		db()->query("UPDATE forum_threads SET open=NOT open, closed_by_mod='$closed_by_mod' WHERE thread_id='".$this->root['thread_id']."' LIMIT 1");
		$this->root['open'] = db()->query("SELECT open FROM forum_threads WHERE thread_id='".$this->root['thread_id']."' LIMIT 1")->fetch_object()->open;
		if($closed_by_mod) bigbrother('forum_thread_openclose', array($this->root['thread_id'], $this->root['open'] ? 'open' : 'close'));
		return $this->post_moderator_end($args);
	}
	
	
	
	protected function POST_close_report(&$args) {
		if(!$this->root['is_mod']) throw new iexception('403', $this);
		bigbrother('forum_report_ticket_closed', array($args['ticket_id']));
		$was_good_ticket = (empty($args['good']) ? 0 : 1);
		db()->query("UPDATE forum_reported_posts SET open=0, timeclosed=CURRENT_TIMESTAMP, closer_uid='".USER_ID."', was_good_ticket='$was_good_ticket' WHERE id='".es($args['ticket_id'])."' LIMIT 1");
		if(!$was_good_ticket and !empty($args['reason'])) {
			$reported_post = db()->query("SELECT id, user_id FROM forum_reported_posts WHERE id='".es($args['ticket_id'])."' LIMIT 1")->fetch_assoc();
			if($reported_post)
				user($reported_post['user_id'])->pn_system(
					sprintf('Deine Meldung wurde als nicht gut eingestuft.

Thread: [url]http://%s/thread/%s-%s/[/url]
Ticket ID: %s
Grund: %s', SITE_DOMAIN, $this->root['thread_id'], urlenc($this->root['firstpost_name']), $reported_post['id'], $args['reason']));
		}
		if(IS_AJAX) G::$json_data['e']['__obj__'] = '<p class="info">'.LS('OK').'</p>';
		else page_redir($this->url.($this->page > 1 ? 'page/'.$this->page.'/' : ''));
	}
	
	protected function POST_thread_state(&$args) {
		if(!$this->root['is_mod']) throw new iexception('403', $this);
		$state = es(@$args['state']);
		if(!in_array($state, $this->FORUM_STATES)) $state = 'normal';
		bigbrother('forum_thread_state', array($this->root['thread_id'], $state));
		db()->query("UPDATE forum_threads SET state='".$state."' WHERE thread_id='".$this->root['thread_id']."' LIMIT 1");
		return $this->post_moderator_end($args);
	}
	
	protected function POST_thread_priority(&$args) {
		if(!$this->root['is_mod']) throw new iexception('403', $this);
		$priority = (int)@$args['priority'];
		$priority = ($priority ? $priority : 0);
		bigbrother('forum_thread_priority', array($this->root['thread_id'], $priority));
		db()->query("UPDATE forum_threads SET priority='".$priority."' WHERE thread_id='".$this->root['thread_id']."' LIMIT 1");
		return $this->post_moderator_end($args);
	}
	
	protected function POST_change_lang(&$args) {
		if(!$this->root['is_mod']) throw new iexception('403', $this);
		$source_lang_de = $this->root['thread_lang_de'];
		$source_lang_en = $this->root['thread_lang_en'];
		$target_lang_de = (empty($args['de']) ? false : true);
		$target_lang_en = (empty($args['en']) ? false : true);
		bigbrother('forum_thread_lang', array($this->root['thread_id'], $source_lang_de, $source_lang_en, $target_lang_de, $target_lang_en));
		db()->query("UPDATE forum_threads SET lang_de='$target_lang_de', lang_en='$target_lang_en' WHERE thread_id='".$this->root['thread_id']."' LIMIT 1");
		$this->root['thread_lang_de'] = $target_lang_de;
		$this->root['thread_lang_en'] = $target_lang_en;
		function ___change_lang_update(&$root, $extra, $add) {
			$lastthread = db()->query("
				SELECT forum_threads.thread_id AS id
				FROM forum_threads
				LEFT JOIN forum_posts USING (thread_id)
				WHERE forum_threads.section_id='".$root['section_id']."' AND forum_threads.lang$extra='1'
				ORDER BY forum_posts.timeadded DESC
				LIMIT 1")->fetch_assoc();
			$lastthread = ($lastthread ? $lastthread['id'] : 0);
			db()->query("
				UPDATE forum_sections
				SET lastthread$extra=$lastthread
				WHERE section_id='".$root['section_id']."'
				LIMIT 1");
		}
		if($source_lang_de != $target_lang_de) ___change_lang_update($this->root, '_de', $target_lang_de);
		if($source_lang_en != $target_lang_en) ___change_lang_update($this->root, '_en', $target_lang_en);
		m_forum_global::fix_stats($this->root['section_id']);
		return $this->post_moderator_end($args);
	}
	
	protected function POST_delete_thread(&$args) {
		if(!$this->root['is_mod']) throw new iexception('403', $this);
		$posts = db()->query("SELECT thread_id id FROM forum_posts WHERE thread_id='{$this->thread_id}'");
		while($post = $posts->fetch_assoc()) db()->query("DELETE FROM forum_reported_posts WHERE post_id='".$post['id']."' AND open=1");
		db()->query("DELETE FROM forum_posts WHERE thread_id='{$this->thread_id}'");
		$num_posts = db()->affected_rows;
		bigbrother('forum_thread_delete', array($this->root['section_id'], $this->root['thread_id'], $num_posts));
		db()->query("DELETE FROM forum_threads WHERE thread_id='{$this->thread_id}' LIMIT 1");
		db()->query("DELETE FROM user_bookmarks WHERE thing='thread' AND thing_id='{$this->thread_id}'");
		function ___handle_lastthread(&$root, $num_posts, $extra) {
			$lastthread = db()->query("
				SELECT forum_threads.thread_id AS id
				FROM forum_threads
				LEFT JOIN forum_posts USING (thread_id)
				WHERE forum_threads.section_id='".$root['section_id']."'".($extra ? " AND forum_threads.lang$extra=1" : "")."
				ORDER BY forum_posts.timeadded DESC
				LIMIT 1")->fetch_assoc();
			$lastthread = ($lastthread ? $lastthread['id'] : 0);
			db()->query("
				UPDATE forum_sections
				SET lastthread$extra=$lastthread
				WHERE section_id='".$root['section_id']."'
				LIMIT 1");
		}
		___handle_lastthread($this->root, $num_posts, '');
		if($this->root['thread_lang_de']) ___handle_lastthread($this->root, $num_posts, '_de');
		if($this->root['thread_lang_en']) ___handle_lastthread($this->root, $num_posts, '_en');
		
		$message = LS('
			Der Thread &quot;%1%&quot; (ID %2%) wurde gel&ouml;scht<br>
			Es wurden %3% Beitr&auml;ge gel&ouml;scht.',
			$this->root['firstpost_name'], $this->root['thread_id'], $num_posts);
		
		m_forum_global::fix_stats($this->root['section_id']);
		cache_L1::del('last_forum_post_id_'.$this->root['section_namespace']);
		if(IS_AJAX) {
			G::$json_data['e']['__obj__'] = $message;
			return;
		}
		return m_tools::view_module_box(LS('Thread wurde gel&ouml;scht'), $message);
	}
	
	protected function POST_delete_post(&$args) {
		if(!$this->root['is_mod']) throw new iexception('403', $this);
		$post_id = (int)$args['post_id'];
		if($post_id == $this->root['firstpost_id']) {
			return $this->post_moderator_end_message(
				'<td class="post-infos">%s</td>',
				LS('Beitrag wurde nicht gel&ouml;scht'),
				LS('Wenn du den ersten Post im Thread l&ouml;schen willst l&ouml;sche den kompletten Thread.'));
				
		}
		$post = db()->query("SELECT post_id, user_id FROM forum_posts WHERE thread_id='{$this->thread_id}' AND post_id='$post_id' LIMIT 1")->fetch_assoc();
		if(!$post) return $this->post_moderator_end_message('<td class="post-infos">%s</td>', LS('Beitrag wurde nicht gel&ouml;scht'), LS('Der ausgew&auml;hlte Beitrag wurde nicht gefunden.'));
		
		bigbrother('forum_post_delete', array($this->root['thread_id'], $post['post_id'], $post['user_id']));
		db()->query("DELETE FROM forum_reported_posts WHERE post_id='$post_id' AND open=1");
		db()->query("DELETE FROM forum_posts WHERE post_id='$post_id' LIMIT 1");
		db()->query("UPDATE users SET forum_posts=forum_posts-1 WHERE user_id='".$post['user_id']."' LIMIT 1");
		db()->query("UPDATE forum_threads SET num_posts=num_posts-1 WHERE thread_id='".$this->root['thread_id']."' LIMIT 1");
		if($post_id == $this->root['lastpost_id']) {
			$newlastpost = db()->query("SELECT post_id id FROM forum_posts WHERE thread_id='".$this->root['thread_id']."' ORDER BY post_id DESC LIMIT 1")->fetch_object()->id;
			db()->query("UPDATE forum_threads SET lastpost='$newlastpost' WHERE thread_id='".$this->root['thread_id']."' LIMIT 1");
		}
		m_forum_global::fix_stats($this->root['section_id']);
		cache_L1::del('last_forum_post_id_'.$this->root['section_namespace']);
		if(IS_AJAX) {
			G::$json_data['e']['__obj__'] = '<td class="post-infos"><p class="info">'.LS('Der Beitrag wurde gel&ouml;scht.').'</p></td>';
			return;
		}
		page_redir($this->url.($this->page > 1 ? 'page/'.$this->page.'/' : ''));
	}
	
	protected function POST_thread_move(&$args) {
		if(!$this->root['is_mod']) throw new iexception('403', $this);
		if($args['section_id'] == $this->root['section_id']) return $this->post_moderator_end($args);
		bigbrother('forum_thread_move', array($this->root['thread_id'], $this->root['section_id'], $args['section_id']));
		$target_section = db()->query("
			SELECT
				forum_sections.section_id AS section_id,
				forum_sections.allow_threads AS allow_threads,
				UNIX_TIMESTAMP(lastpost.timeadded) AS lastpost_timeadded,
				UNIX_TIMESTAMP(lastpost_lang_de.timeadded) AS lastpost_timeadded_lang_de,
				UNIX_TIMESTAMP(lastpost_lang_en.timeadded) AS lastpost_timeadded_lang_en,
				forum_sections.namespace AS section_namespace
			FROM forum_sections
			LEFT JOIN forum_threads AS lastthread ON lastthread.thread_id=forum_sections.lastthread
			LEFT JOIN forum_posts AS lastpost ON lastpost.post_id=lastthread.lastpost
			LEFT JOIN forum_threads AS lastthread_lang_de ON lastthread_lang_de.thread_id=forum_sections.lastthread_de
			LEFT JOIN forum_posts AS lastpost_lang_de ON lastpost_lang_de.post_id=lastthread_lang_de.lastpost
			LEFT JOIN forum_threads AS lastthread_lang_en ON lastthread_lang_en.thread_id=forum_sections.lastthread_en
			LEFT JOIN forum_posts AS lastpost_lang_en ON lastpost_lang_en.post_id=lastthread_lang_en.lastpost
			WHERE forum_sections.section_id='".(int)$args['section_id']."'
			LIMIT 1")->fetch_assoc();
		if(!$target_section)
			return $this->post_moderator_end_message('%s', LS('Fehler'), LS('Die Zielsektion wurde nicht gefunden.'));
		if(!$target_section['allow_threads'])
			return $this->post_moderator_end_message('%s', LS('Fehler'), LS('In der Zielsektion d&uuml;rfen keine Threads erstellt werden.'));
		
		$messages = array();
		$this->root['section_lastthread'] = db()->query("SELECT lastthread FROM forum_sections WHERE section_id='".$this->root['section_id']."' LIMIT 1")->fetch_object()->lastthread;
		$this->root['section_lastthread_lang_de'] = db()->query("SELECT lastthread_de AS lastthread_lang_de FROM forum_sections WHERE section_id='".$this->root['section_id']."' LIMIT 1")->fetch_object()->lastthread_lang_de;
		$this->root['section_lastthread_lang_en'] = db()->query("SELECT lastthread_en AS lastthread_lang_en FROM forum_sections WHERE section_id='".$this->root['section_id']."' LIMIT 1")->fetch_object()->lastthread_lang_en;
		if(!empty($args['create_moved'])) {
			db()->query("
				INSERT INTO forum_posts
				SET
					user_id='".$this->root['firstpost_uid']."',
					timeadded='".$this->root['firstpost_timeadded']."',
					lasteditor='".$this->root['thread_id']."',
					lastedit=TIMESTAMPADD(DAY,1,CURRENT_TIMESTAMP),
					name='".es($this->root['firstpost_name'])."'");
			$newpostid = db()->insert_id;
			db()->query("
				INSERT INTO forum_threads
				SET
					section_id='".$this->root['section_id']."',
					lang_de='".$this->root['thread_lang_de']."',
					lang_en='".$this->root['thread_lang_en']."',
					state='moved',
					firstpost='$newpostid',
					lastpost='$newpostid'");
			$newthreadid = db()->insert_id;
			if($this->root['section_lastthread'] == $this->root['thread_id']) {
				db()->query("UPDATE forum_sections SET lastthread='$newthreadid' WHERE section_id='".$this->root['section_id']."' LIMIT 1");
				$messages[] = LS('Erster Thread in der alten Sektion %1% wurde auf %2% gesetzt.', $this->root['section_id'], $newthreadid);
			}
			if($this->root['section_lastthread_lang_de'] == $this->root['thread_id']) {
				db()->query("UPDATE forum_sections SET lastthread_de='$newthreadid' WHERE section_id='".$this->root['section_id']."' LIMIT 1");
				$messages[] = LS('Erster Thread in der Sprache %1% in der alten Sektion %2% wurde auf %3% gesetzt.', get_sitelang_string('de'), $this->root['section_id'], $newthreadid);
			}
			if($this->root['section_lastthread_lang_en'] == $this->root['thread_id']) {
				db()->query("UPDATE forum_sections SET lastthread_en='$newthreadid' WHERE section_id='".$this->root['section_id']."' LIMIT 1");
				$messages[] = LS('Erster Thread in der Sprache %1% in der alten Sektion %2% wurde auf %3% gesetzt.', get_sitelang_string('en'), $this->root['section_id'], $newthreadid);
			}
		}
		else { //not $args['create_moved']
			if($this->root['section_lastthread'] == $this->root['thread_id']) {
				function ___handle_lastthreadid(&$root, $lang, $extra) {
					$lastthreadid = db()->query("
						SELECT forum_threads.thread_id AS id
						FROM forum_threads
						LEFT JOIN forum_posts USING (thread_id)
						WHERE forum_threads.section_id='".$root['section_id']."'".($extra ? " AND forum_threads.lang$extra=1" : "")."
						ORDER BY forum_posts.timeadded DESC
						LIMIT 1")->fetch_assoc();
					$lastthreadid = ($lastthreadid ? $lastthreadid['id'] : 0);
					db()->query("
						UPDATE forum_sections
						SET lastthread$extra='$lastthreadid'
						WHERE section_id='".$root['section_id']."'
						LIMIT 1");
					if($lang) $messages[] = LS('Erster Thread in der Sprache %1% in der alten Sektion %1% wurde auf %2% gesetzt.', get_sitelang_string($lang), $root['section_id'], $lastthreadid);
					else $messages[] = LS('Erster Thread in der alten Sektion %1% wurde auf %2% gesetzt.', $root['section_id'], $lastthreadid);
				}
				___handle_lastthreadid($this->root, '', '');
				___handle_lastthreadid($this->root, 'de', '_de');
				___handle_lastthreadid($this->root, 'en', '_en');
			}
		}
		$this->root['lastpost_timeadded'] = db()->query("
			SELECT UNIX_TIMESTAMP(forum_posts.timeadded) AS lastpost_timeadded
			FROM forum_threads
			LEFT JOIN forum_sections USING (section_id)
			LEFT JOIN forum_posts ON forum_posts.post_id=forum_threads.lastpost
			WHERE forum_threads.thread_id='{$this->thread_id}'
			LIMIT 1")->fetch_assoc();
		if($this->root['lastpost_timeadded']) $this->root['lastpost_timeadded'] = $this->root['lastpost_timeadded']['lastpost_timeadded'];
		$this->root['lastpost_timeadded_lang_de'] = db()->query("
			SELECT UNIX_TIMESTAMP(forum_posts.timeadded) AS lastpost_timeadded_lang_de
			FROM forum_threads
			LEFT JOIN forum_sections USING (section_id)
			LEFT JOIN forum_posts ON forum_posts.post_id=forum_threads.lastpost
			WHERE forum_threads.thread_id='{$this->thread_id}' AND forum_threads.lang_de=1
			LIMIT 1")->fetch_assoc();
		if($this->root['lastpost_timeadded_lang_de']) $this->root['lastpost_timeadded_lang_de'] = $this->root['lastpost_timeadded_lang_de']['lastpost_timeadded_lang_de'];
		$this->root['lastpost_timeadded_lang_en'] = db()->query("
			SELECT UNIX_TIMESTAMP(forum_posts.timeadded) AS lastpost_timeadded_lang_en
			FROM forum_threads
			LEFT JOIN forum_sections USING (section_id)
			LEFT JOIN forum_posts ON forum_posts.post_id=forum_threads.lastpost
			WHERE forum_threads.thread_id='{$this->thread_id}' AND forum_threads.lang_en=1
			LIMIT 1")->fetch_assoc();
		if($this->root['lastpost_timeadded_lang_en']) $this->root['lastpost_timeadded_lang_en'] = $this->root['lastpost_timeadded_lang_en']['lastpost_timeadded_lang_en'];
		if($target_section['lastpost_timeadded'] < $this->root['lastpost_timeadded']) {
			db()->query("UPDATE forum_sections SET lastthread='".$this->root['thread_id']."' WHERE section_id='".$target_section['section_id']."' LIMIT 1");
			$messages[] = LS('Erster Thread in der neuen Sektion %1% wurde auf %2% gesetzt.', $target_section['section_id'], $this->root['thread_id']);
		}
		if($target_section['lastpost_timeadded_lang_de'] < $this->root['lastpost_timeadded_lang_de']) {
			db()->query("UPDATE forum_sections SET lastthread_de='".$this->root['thread_id']."' WHERE section_id='".$target_section['section_id']."' LIMIT 1");
			$messages[] = LS('Erster Thread in der Sprache %1% in der neuen Sektion %2% wurde auf %3% gesetzt.', get_sitelang_string('de'), $target_section['section_id'], $this->root['thread_id']);
		}
		if($target_section['lastpost_timeadded_lang_en'] < $this->root['lastpost_timeadded_lang_en']) {
			db()->query("UPDATE forum_sections SET lastthread_en='".$this->root['thread_id']."' WHERE section_id='".$target_section['section_id']."' LIMIT 1");
			$messages[] = LS('Erster Thread in der Sprache %1% in der neuen Sektion %2% wurde auf %3% gesetzt.', get_sitelang_string('en'), $target_section['section_id'], $this->root['thread_id']);
		}
		db()->query("UPDATE forum_threads SET section_id='".$target_section['section_id']."' WHERE thread_id='".$this->root['thread_id']."' LIMIT 1");
		
		m_forum_global::fix_stats($this->root['section_id']);
		m_forum_global::fix_stats($target_section['section_id']);
		
		cache_L1::del('last_forum_post_id_'.$this->root['section_namespace']);
		cache_L1::del('last_forum_post_id_'.$target_section['section_namespace']);
		$message =
			LS('Der Beitrag %1% wurde verschoben.', $this->root['thread_id']).'<br>'.
			implode('<br>', $messages).'<br>'.
			LS('<a href="/%1%/forum/%2%-%3%/">Hier gehts zur&uuml;ck zum Forum.</a>', LANG, $this->root['section_id'], urlenc($this->root['section_name'])).'<br>'.
			LS('<a href="/%1%/thread/%2%-%3%/">Hier gehts zum Thread.</a>', LANG, $this->root['thread_id'], urlenc($this->root['firstpost_name']));
		return $this->post_moderator_end_message('%s', LS('Beitrag verschoben'), $message);
	}
	
	
	
	private function post_moderator_end(&$args) {
		if(IS_AJAX) {
			$this->INIT($args);
			return $this->ilphp_fetch('thread.php.admin.ilp');
		}
		else page_redir($this->url.($this->page > 1 ? 'page/'.$this->page.'/' : ''));
	}
	
	private function post_moderator_end_message($ajax_format, $title, $message) {
		if(IS_AJAX) {
			return sprintf($ajax_format, '<strong>'.$title.'<br></strong> '.$message);
		}
		return m_tools::view_module_box($title, $message.
			LS('<br><a href="/%1%/forum/%2%-%3%/">Hier gehts zur&uuml;ck zum Forum.</a>', LANG, $this->root['section_id'], urlenc($this->root['section_name'])));
	}
}

?>
