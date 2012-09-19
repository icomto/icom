<?php

class m_wiki extends imodule {
	use ilphp_trait;
	
	public $WIKI_SPEZIAL_PAGES_COLS = 3;
	public $predefined_content = '';
	public $wiki = NULL;
	public $page = NULL;
	
	public function __construct() {
		parent::__construct(__DIR__);
		$this->ilphp_construct('wiki.php.ilp');
		#$this->is_multilang = (get_lang_query($this->lang_list) == '' ? true : false);
	}
	
	protected function MODULE(&$args) {
		$data = $this->_run();
		set_site_title_i($this->way, 0);
		return $data;
	}
	
	protected function POST(&$args) {
		if(IS_AJAX) return $this->MODULE($args);
	}
	
	private function _run() {
		$this->way = array(array('Wiki', '/wiki/'));
		$this->wiki = $this->args['wiki'];
		$this->errors = array();
		$this->warnings = array();
		$this->infos = array();
		
		if(isset($this->args['new_wiki']) and IS_LOGGED_IN)
			return $this->redirect($this->args['new_wiki']);

		$temp = wiki_urldecode($this->wiki);
		if(wiki_urlencode($temp) != str_replace('%3A', ':', urlencode($this->wiki))) return $this->redirect($temp);
		$this->wiki = $temp;
		#$this->wiki = es($temp);

		if(preg_match('~(Kategorie|Spezial|Admin):(.+)$~i', $this->wiki, $out)) {
			switch(mb_strtolower($out[1])) {
			case 'category':
			case 'kategorie':
				$this->way[] = array(LS('Kategorien'), '/wiki/'.LS('Spezial').':'.LS('Kategorien').'/');
				$this->way[] = array($out[2], '/wiki/'.LS('Kategorie').':'.wiki_urlencode($out[2]).'/');
				$this->type = "";
				$this->category = $out[2];
				return $this->pages();
			case 'special':
			case 'spezial':
				switch(mb_strtolower($out[2])) {
				default:
				return $this->redirect(LS('Spezial').':404');
				case 'alle seiten':
				case 'all pages':
					$this->way[] = array(LS('Alle Seiten'), '/wiki/'.LS('Spezial').':'.LS('Alle_Seiten').'/');
					$this->type = "";
					$this->category = "";
					return $this->pages();
				case 'kategorien':
				case 'categorys':
					$this->way[] = array(LS('Kategorien'), '/wiki/Spezial:'.LS('Kategorien').'/');
					$this->type = LS('Kategorie:');
					$this->category = '';
					return $this->pages();
				case 'suche':
				case 'search':
					$this->way[] = array(LS('Suche'), '/wiki/'.LS('Spezial').':'.LS('Suche').'/');
					return $this->search();
				case '403':
				case '404':
				case '504':
					$this->action = 'ERROR';
					$this->way[] = array(LS('Fehler %1%', $out[2]), '/wiki/'.LS('Spezial').':'.$out[2].'/');
					$this->wiki = LS('Spezial').':'.$out[2];
					$this->page = NULL;
					$this->code = $out[2];
					return $this->ilphp_fetch();
				}
			case 'admin':
				if(!has_privilege('wiki_mod')) return $this->redirect(LS('Spezial').':403');
				switch(mb_strtolower($out[2])) {
				default:
					return $this->redirect(LS('Spezial').':404');
				case 'letzte änderungen':
				case utf8_encode('letzte änderungen'):
				case 'recent changes':
					$this->way[] = array(LS('Letzte &Auml;nderungen'), '/wiki/'.LS('Admin:Letzte_Änderungen').'/');
					return $this->admin_lastest_changes();
				case 'unaktivierte artikel':
				case 'unactivated articles':
					$this->way[] = array(LS('Unaktivierte Artikel'), '/wiki/'.LS('Admin:Unaktivierte_Artikel').'/');
					return $this->admin_unactivated_articles();
				case 'artikel gelöscht':
				case utf8_encode('artikel gelöscht'):
				case 'article deleted':
					$this->way[] = array(LS('Artikel gel&ouml;scht'), '/wiki/'.LS('Admin:Artikel_gel&ouml;scht').'/');
					$this->action = 'ARTICLE_DELETED';
					return $this->ilphp_fetch();
				}
			}
		}
		
		if(!$this->wiki) {
			$this->page = db()->query("SELECT id, lang, name, locked, history FROM wiki_pages WHERE lang='".LANG."' AND name='".(LANG == 'de' ? 'Hauptseite' : 'Main Page')."' AND deleted=0 LIMIT 1")->fetch_assoc();
			return $this->redirect($this->page['name']);
		}
		else {
			$this->page = db()->query("SELECT id, lang, name, locked, history FROM wiki_pages WHERE name='".es($this->wiki)."' AND lang='".LANG."' AND deleted=0 LIMIT 1")->fetch_assoc();
			if(!$this->page) {
				$this->page = db()->query("
					SELECT wiki_pages.id AS id, wiki_pages.lang AS lang, wiki_pages.name AS name, wiki_pages.locked AS locked, wiki_pages.history AS history
					FROM wiki_aliases
					LEFT JOIN wiki_pages ON wiki_pages.id=wiki_aliases.page
					WHERE wiki_aliases.name='".es($this->wiki)."' AND wiki_pages.lang='".LANG."' AND wiki_pages.deleted=0
					LIMIT 1")->fetch_assoc();
			}
		}
		
		if($this->page) {
			$h = db()->query("SELECT timeadded, content FROM wiki_history WHERE id='".$this->page['history']."' LIMIT 1")->fetch_assoc();
			$this->page['timeadded'] = $h['timeadded'];
			$this->page['content'] = $h['content'];
			$this->ws = wikicode::parse($this->page['name'], $this->page['content']);
			$this->way[] = array($this->page['name'].($this->ws->aliases ? ' ('.implode(', ', $this->ws->aliases).')' : ''), '/wiki/'.wiki_urlencode($this->page['name']).'/');
		}
		else {
			$this->args['wiki'] = urldecode($this->wiki);
			if($this->args['wiki'] != $this->wiki) return $this->_run();
			$this->way[] = array(LS('Artikel nicht gefunden'), '/wiki/'.wiki_urlencode($this->wiki).'/');
		}

		if(IS_LOGGED_IN) {
			if(isset($this->args['edit'])) return $this->edit();
			elseif($this->page) {
				if(isset($this->args['history'])) return $this->history();
				elseif(isset($this->args['compare']) and isset($this->args['with'])) return $this->history_compare();
				elseif(isset($this->args['create_ticket'])) return $this->create_ticket();
				elseif(has_privilege('wiki_mod')) {
					if(isset($this->args['history_sighted'])) {
						db()->query("UPDATE wiki_pages SET lastchange=CURRENT_TIMESTAMP WHERE id='".$this->page['id']."' LIMIT 1");
						db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='".$this->page['id']."', action='history_sighted', reason='".es($this->args['reason'])."'");
						$this->delete_wiki_cache();
						return $this->redirect($this->wiki, 'history/');
					}
					elseif(isset($this->args['lock_page'])) {
						db()->query("UPDATE wiki_pages SET locked=1, lastchange=CURRENT_TIMESTAMP WHERE id='".$this->page['id']."' LIMIT 1");
						if(db()->affected_rows) {
							db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='".$this->page['id']."', action='page_locked', reason='".es($this->args['reason'])."'");
							$this->delete_wiki_cache();
						}
						return $this->redirect($this->wiki, 'history/');
					}
					elseif(isset($this->args['unlock_page'])) {
						db()->query("UPDATE wiki_pages SET locked=0, lastchange=CURRENT_TIMESTAMP WHERE id='".$this->page['id']."' LIMIT 1");
						if(db()->affected_rows) {
							db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='".$this->page['id']."', action='page_unlocked', reason='".es($this->args['reason'])."'");
							$this->delete_wiki_cache();
						}
						return $this->redirect($this->wiki, 'history/');
					}
					elseif(isset($this->args['close_ticket'])) return $this->close_ticket();
					elseif(has_privilege('wiki_admin')) {
						if(isset($this->args['delete_log']) and $this->args['delete_log']) {
							db()->query("DELETE FROM wiki_changes WHERE id='".es($this->args['delete_log'])."' AND page='".$this->page['id']."' LIMIT 1");
							return $this->redirect($this->wiki, 'history/');
						}
						elseif(isset($this->args['rename_page']) and ($this->args['rename_page'] or (isset($this->args['name']) and $this->args['name']))) {
							return $this->rename();
						}
						elseif(isset($this->args['change_language']) and isset($this->args['lang']) and in_array($this->args['lang'], array('de', 'en'))) {
							db()->query("UPDATE wiki_pages SET lang='".es($this->args['lang'])."' WHERE id='".$this->page['id']."' LIMIT 1");
							if(db()->affected_rows) {
								db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='".$this->page['id']."', action='language_changed', reason='".es($this->page['lang'].' -> '.$this->args['lang'])."'");
								$this->delete_wiki_cache();
								page_redir('/'.$this->args['lang'].'/wiki/'.wiki_urlencode($this->wiki).'/history');
							}
							return $this->redirect($this->wiki, 'history/');
						}
						elseif(isset($this->args['delete_article'])) {
							if(!@$this->args['confirm']) return $this->redirect($this->wiki, 'history/');
							/*db()->query("DELETE FROM wiki_aliases WHERE page='".$this->page['id']."'");
							db()->query("DELETE FROM wiki_changes WHERE page='".$this->page['id']."'");
							db()->query("DELETE FROM wiki_history WHERE page='".$this->page['id']."'");
							db()->query("DELETE FROM wiki_tickets WHERE page='".$this->page['id']."'");*/
							db()->query("UPDATE wiki_pages SET deleted=1, locked=0 WHERE id='".$this->page['id']."' LIMIT 1");
							db()->query("DELETE FROM user_bookmarks WHERE thing='wiki' AND thing_id='".$this->page['id']."'");
							if(db()->affected_rows) {
								db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='".$this->page['id']."', action='article_deleted'");
								$this->delete_wiki_cache();
							}
							return $this->redirect(LS('Admin:Artikel_gel&ouml;scht'));
						}
					}
				}
			}
		}

		if($this->page and !$this->page['history']) return $this->redirect($this->wiki, 'history/');
		$this->action = 'MAIN';
		if($this->page) {
			db()->query("UPDATE LOW_PRIORITY wiki_pages SET hits=hits+1 WHERE id='".$this->page['id']."' LIMIT 1");
			if(IS_LOGGED_IN and db()->query("SELECT 1 FROM wiki_tickets WHERE page='".$this->page['id']."' AND closer=0 LIMIT 1")->num_rows)
				$this->warnings[] = 'HAS_OPEN_TICKETS';
		}
		if(!has_privilege('wiki_mod')) {
			$this->ilphp_init('', 30, $this->action.','.$this->wiki);
			##########if($data = $this->ilphp_cache_load()) return $data;
		}
		return $this->ilphp_fetch();
	}
	
	private function delete_wiki_cache() {
		/*ilphp_cache_delete2('wiki.php.ilp', 'MAIN,'.$this->wiki);
		ilphp_cache_delete2('wiki.php.ilp', 'EDIT,'.$this->wiki);
		ilphp_cache_delete2('wiki.php.ilp', 'HISTORY_OVERVIEW,'.$this->wiki.',1');
		ilphp_cache_delete2('wiki.php.ilp', 'HISTORY_OVERVIEW,'.$this->wiki.',2');
		ilphp_cache_delete2('wiki.php.ilp', 'HISTORY_OVERVIEW,'.$this->wiki.',3');
		ilphp_cache_delete2('wiki.php.ilp', 'HISTORY_OVERVIEW,'.$this->wiki.',4');
		ilphp_cache_delete2('wiki.php.ilp', 'HISTORY_OVERVIEW,'.$this->wiki.',5');*/
	}
	
	private function redirect($wiki, $extra = '') {
		page_redir('/'.LANG.'/wiki/'.wiki_urlencode($wiki).'/'.$extra);
	}


	private function pages() {
		$this->action = 'PAGES';
		if(!has_privilege('wiki_mod')) {
			$this->ilphp_init('', 30, $this->action.','.$this->type.','.$this->category);
			########if($data = $this->ilphp_cache_load()) return $data;
		}
		$this->pages = array();
		if(!$this->type) {
			if($this->category)
				$pages = db()->query("
					SELECT name
					FROM wiki_pages
					LEFT JOIN wiki_history ON wiki_history.id=wiki_pages.history
					WHERE wiki_pages.lang='".LANG."' AND wiki_history.content REGEXP '\\\\[\\\\[ *(Kategorie|Category): *".es($this->category)." *\\\\]\\\\]' AND wiki_pages.deleted=0
					ORDER BY name");
			else $pages = db()->query("SELECT name FROM wiki_pages WHERE deleted=0 ORDER BY name");
			if(!$pages->num_rows) $this->errors[] = 'NO_ARTICLES';
		}
		elseif($this->type == 'Kategorie:' or $this->type == 'Category:') {
			$pages = db()->query("SELECT name FROM wiki_categorys WHERE lang='".LANG."' ORDER BY name");
			if(!$pages->num_rows) $this->errors[] = 'NO_CATEGORYS';
		}
		else {
			$this->errors[] = 'PAGES_INVALID_TYPE';
			return $this->ilphp_fetch();
		}
		if($pages->num_rows) {
			$current = '#';
			while($i = $pages->fetch_assoc()) {
				$c = strtoupper(mb_substr($i['name'], 0, 1));
				if(($current == '#' and preg_match('~[a-z]~i', $c)) or $c != $current) {
					$current = $c;
					$this->pages[$current] = array();
				}
				$this->pages[$current][] = $i['name'];
			}
			$this->rows_per_col = round((count($this->pages)*2 + 2*3 + $pages->num_rows)/$this->WIKI_SPEZIAL_PAGES_COLS);
		}
		return $this->ilphp_fetch();
	}

	private function admin_lastest_changes() {
		$this->action = 'ADMIN_LASTEST_CHANGES';
		$this->pages = db()->query("
			SELECT wiki_pages.id AS id, wiki_pages.lang AS lang, wiki_pages.name AS name, wiki_pages.history AS history, wiki_pages.lastchange AS lastchange
			FROM wiki_pages
			JOIN wiki_history ON wiki_history.page=wiki_pages.id
			WHERE wiki_history.timeadded>wiki_pages.lastchange AND wiki_pages.deleted=0
			GROUP BY wiki_pages.id
			ORDER BY wiki_pages.lastchange DESC
			LIMIT 10");
		if(!$this->pages->num_rows) $this->infos[] = 'NO_LATEST_CHANGES_FOUND';
		return $this->ilphp_fetch();
	}
	public function admin_lastest_changes_query_page() {
		$this->changes = db()->query("
			SELECT
				wiki_changes.id AS id,
				wiki_changes.timeadded AS timeadded,
				wiki_changes.user AS user,
				wiki_changes.history AS history,
				wiki_history.timeadded AS history_timeadded,
				wiki_changes.action AS action,
				wiki_changes.reason AS reason
			FROM wiki_changes
			LEFT JOIN wiki_history ON wiki_history.id=wiki_changes.history
			WHERE
				wiki_changes.page='".$this->i['id']."' AND
				(wiki_history.timeadded>='".$this->i['lastchange']."' OR wiki_history.id='".$this->i['history']."')
			ORDER BY wiki_changes.timeadded DESC");
	}
	
	private function admin_unactivated_articles() {
		$this->action = 'ADMIN_UNACTIVATED_ARTICLES';
		$this->pages = db()->query("SELECT lang, name, lastchange FROM wiki_pages WHERE NOT history AND deleted=0 ORDER BY lastchange DESC");
		return $this->ilphp_fetch();
	}

	private function edit() {
		$this->action = 'EDIT';
		$this->history = es($this->args['edit']);
		if(!$this->history and $this->page) $this->history = $this->page['history'];
		if($this->history and $this->page) $this->history = db()->query("SELECT id, timeadded, content FROM wiki_history WHERE id='".$this->history."' AND page='".$this->page['id']."' LIMIT 1")->fetch_assoc();
		else $this->history = '';
		if($this->history) $this->way[] = array(LS('Version vom %1%', default_date_format($this->history['timeadded'])), '/wiki/'.wiki_urlencode($this->wiki).'/'.($this->history ? $this->history['id'].'/' : ''));
		if($this->page and $this->page['locked'] and !has_privilege('wiki_mod')) {
			$this->way[] = array(LS('Artikelquelltext'), '/wiki/'.wiki_urlencode($this->page['name']).'/edit/'.($this->history ? $this->history['id'].'/' : ''));
			$this->infos[] = 'PAGE_EDIT_LOCKED_ACCESS_DENIED';
		}
		else {
			if($this->page) $this->way[] = array(LS('Artikel bearbeiten'), '/wiki/'.wiki_urlencode($this->wiki).'/edit/'.($this->history ? $this->history['id'].'/' : ''));
			else $this->way[] = array(LS('Artikel erstellen'), '');
			if(isset($this->args['content']) and $this->args['content']) {
				$content = es(trim($this->args['content']));
				if($this->page) {
					$mirrors = db()->query("SELECT 1 FROM wiki_history WHERE page='".$this->page['id']."' AND content='$content' LIMIT 1");
					if($mirrors->num_rows) $this->errors[] = 'ARTICLE_NOT_CHANGED';
					else {
						$reason = (isset($this->args['reason']) ? es(trim($this->args['reason'])) : "");
						db()->query("INSERT INTO wiki_history SET page='".$this->page['id']."', content='$content'");
						$history_id = db()->insert_id;
						db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='".$this->page['id']."', history='$history_id', action='content_changed', reason='$reason'");
						//db()->query("UPDATE wiki_pages SET lastchange=CURRENT_TIMESTAMP, history='$history_id' WHERE id='".$this->page['id']."' LIMIT 1");
						return $this->redirect($this->wiki, 'history/page_added/');
					}
				}
				else {
					$deleted_article = db()->query("SELECT id FROM wiki_pages WHERE lang='".LANG."' AND name='".es($this->wiki)."' AND deleted=1 LIMIT 1")->fetch_assoc();
					if($deleted_article) {
						$page_id = $deleted_article['id'];
						db()->query("UPDATE wiki_pages SET deleted=0 WHERE id='".$page_id."' LIMIT 1");
					}
					else {
						db()->query("INSERT INTO wiki_pages SET lang='".LANG."', name='".es($this->wiki)."', lastchange=CURRENT_TIMESTAMP");
						$page_id = db()->insert_id;
					}
					db()->query("INSERT INTO wiki_history SET page='$page_id', content='$content'");
					$history_id = db()->insert_id;
					db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='$page_id', action='article_created', reason=''");
					db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='$page_id', history='$history_id', action='content_changed', reason=''");
					return $this->redirect($this->wiki, 'history/page_added/');
				}
			}
		}
		if(!$this->page) $this->page = array('id'=>0, 'name'=>$this->wiki, 'locked'=>false, 'content'=>$this->predefined_content, 'history'=>0);
		elseif($this->history) $this->page['content'] = $this->history['content'];
		return $this->ilphp_fetch();
	}

	private function history() {
		if(isset($this->args['set'])) {
			if(!has_privilege('wiki_mod')) {
				$this->errors[] = 'HISTORY_SET_ACCESS_DENIED';
				return $this->history_overview();
			}
			if(true or isset($this->args['reason'])) {
				if($this->args['history'] == '0') $history = 0;
				else {
					$history = db()->query("SELECT id FROM wiki_history WHERE id='".es($this->args['history'])."' AND NOT id='".$this->page['history']."' AND page='".$this->page['id']."' LIMIT 1")->fetch_assoc();
					if($history) $history = $history['id'];
					else $this->errors[] = 'HISTORY_NOT_SET';
				}
				if(!$this->errors) {
					if($history) $this->page['content'] = db()->query("SELECT content FROM wiki_history WHERE id='$history' LIMIT 1")->fetch_object()->content;
					else $this->page['content'] = '';
					$this->update_page_structure();
					db()->query("
						UPDATE wiki_pages
						SET lastchange=CURRENT_TIMESTAMP, history='".$history."'
						WHERE id='".$this->page['id']."'
						LIMIT 1");
					db()->query("
						INSERT INTO wiki_changes
						SET
							user='".USER_ID."',
							page='".$this->page['id']."',
							history='".es($this->args['history'])."',
							action='history_activated',
							reason='".es(@$this->args['reason'])."'");
					$this->delete_wiki_cache();
					return $this->redirect($this->wiki, 'history/');
				}
				
			}
			else {
				$this->action = 'HISTORY_SET';
				$this->history = $this->args['history'];
				return $this->ilphp_fetch();
			}
		}
		elseif(is_numeric($this->args['history'])) {
			$this->action = 'MAIN';
			$this->history = db()->query("SELECT id, page, timeadded, content FROM wiki_history WHERE id='".es($this->args['history'])."' AND page='".es($this->page['id'])."' LIMIT 1")->fetch_assoc();
			if(!$this->history) $this->errors[] = 'HISTORY_NOT_FOUND';
			else {
				$this->page['content'] = $this->history['content'];
				$this->ws = wikicode::parse($this->page['name'], $this->page['content']);
				$this->way[] = array(LS('Version vom %1%', default_date_format($this->history['timeadded'])), '/wiki/'.wiki_urlencode($this->page['name']).'/history/'.$this->history['id'].'/');
			}
			if(!has_privilege('wiki_mod')) {
				$this->ilphp_init('', 30, $this->action.','.$this->page['name'].','.$this->history['id']);
				########if($data = $this->ilphp_cache_load()) return $data;
			}
			return $this->ilphp_fetch();
		}
		elseif((!$this->page['locked'] or has_privilege('wiki_mod')) and isset($this->args['ticket']) and trim($this->args['ticket'])) {
			$ticket = es(trim($this->args['ticket']));
			db()->query("INSERT INTO wiki_tickets SET page='".$this->page['id']."', opener='".USER_ID."', message='$ticket'");
			$this->infos[] = 'TICKET_CREATED';
		}
		elseif(has_privilege('wiki_mod')) {
			if(isset($this->args['close_ticket']) and trim($this->args['close_ticket'])) {
				$ticket_id = es(trim($this->args['close_ticket']));
				db()->query("UPDATE wiki_tickets SET closer='".USER_ID."', timeclosed=CURRENT_TIMESTAMP WHERE id='$ticket_id' AND page='".$this->page['id']."' LIMIT 1");
				$this->infos[] = 'TICKET_CLOSED';
			}
			elseif(isset($this->args['reopen_ticket']) and trim($this->args['reopen_ticket'])) {
				$ticket_id = es(trim($this->args['reopen_ticket']));
				db()->query("UPDATE wiki_tickets SET closer=0, timeclosed=0 WHERE id='$ticket_id' AND page='".$this->page['id']."' LIMIT 1");
				$this->infos[] = 'TICKET_OPENED';
			}
		}
		if($this->page['locked'] and !has_privilege('wiki_mod')) $this->warnings[] = 'PAGE_EDIT_LOCKED_ACCESS_DENIED';
		return $this->history_overview();
	}
	private function history_overview() {
		$this->action = 'HISTORY_OVERVIEW';
		if(preg_match('~^page:(\d+)$~i', @$this->args['history'], $out)) $this->current_page = ($out[1] > 1 ? $out[1] : 1);
		else $this->current_page = 1;
		if(isset($this->args['history']) and $this->args['history'] == 'page_added' and !has_privilege('wiki_admin'))
			$this->infos[] = 'PAGE_ADDED_WAITING_FOR_PUBLISH';
		elseif(!has_privilege('wiki_mod')) {
			$this->ilphp_init('', 30, $this->action.','.$this->page['name'].','.$this->current_page);
			###########if($data = $this->ilphp_cache_load()) return $data;
		}
		$this->way[] = array(LS('Versionsgeschichte'), '/wiki/'.wiki_urlencode($this->page['name']).'/history/');
		if($this->current_page > 1) $this->way[] = array('Seite '.$this->current_page, '/wiki/'.wiki_urlencode($this->page['name']).'/history/page:'.$this->current_page.'/');
		$this->changes = db()->query("
			SELECT SQL_CALC_FOUND_ROWS
				wiki_changes.id AS id,
				wiki_changes.timeadded AS timeadded,
				wiki_changes.user AS user,
				wiki_changes.history AS history,
				wiki_history.timeadded AS history_timeadded,
				wiki_changes.action AS action,
				wiki_changes.reason AS reason
			FROM wiki_changes
			LEFT JOIN wiki_history ON wiki_history.id=wiki_changes.history
			WHERE wiki_changes.page='".$this->page['id']."'
			ORDER BY wiki_changes.id DESC
			LIMIT ".(($this->current_page - 1)*20).", 20");
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, 20);
		if(!$this->page['history']) $this->infos[] = 'NO_HISTORY_ACTIVATED';
		$this->view_closed_tickets = (@$this->args['history'] == 'closed_tickets' ? true : false);
		$this->tickets = db()->query("SELECT * FROM wiki_tickets WHERE page='".$this->page['id']."'".($this->view_closed_tickets ? '' : ' AND NOT closer').' ORDER BY timecreated DESC');
		if($this->view_closed_tickets) $this->has_closed_tickets = false;
		else $this->has_closed_tickets = (db()->query("SELECT 1 FROM wiki_tickets WHERE page='".$this->page['id']."' AND closer LIMIT 1")->num_rows ? true : false);
		return $this->ilphp_fetch();
	}
	public function history_changes_fetch(&$changes) {
		static $h = false;
		static $c = false;
		if($h === false) {
			$h = NULL;
			$c = false;
			$this->unsighted_changes = false;
		}
		if($h) {
			$i = $h;
			$i['x'] = NULL;
			$h = NULL;
			return $i;
		}
		$i = $changes->fetch_assoc();
		if(!$i) {
			$h = false;
			return;
		}
		$i['x'] = NULL;
		switch($i['action']) {
		case 'history_activated':
		case 'history_sighted':
			$c = true;
			$h = $changes->fetch_assoc();
			if($h and $i['history'] == $h['history'] and $h['action'] == 'content_changed') {
				$j = $i;
				$i = $h;
				$i['x'] =& $j;
				$h = NULL;
			}
			break;
		case 'article_created':
		case 'content_changed':
			if(!$c) {
				$this->unsighted_changes = true;
				$c = true;
			}
			break;
		}
		return $i;
	}
	private function history_compare() {
		$this->action = 'COMPARE';
		if(!has_privilege('wiki_mod') or true) {
			$this->ilphp_init('', 30, $this->action.','.$this->page['name'].','.$this->args['compare'].",".$this->args['with']);
			############if($data = $this->ilphp_cache_load()) return $data;
		}
		$this->way[] = array(LS('Versionen vergleichen'), '/wiki/'.wiki_urlencode($this->page['name']).'/compare/'.urlencode($this->args['compare']).'/with/'.urlencode($this->args['with']).'/');
		$this->compare = db()->query("
			SELECT
				wiki_history.id AS id,
				wiki_history.timeadded AS timeadded,
				wiki_changes.user AS user,
				wiki_history.content AS content
			FROM wiki_history
			LEFT JOIN wiki_changes ON wiki_changes.history=wiki_history.id AND (wiki_changes.action='article_created' OR wiki_changes.action='content_changed')
			WHERE wiki_history.id='".es($this->args['compare'])."' AND wiki_history.page='".$this->page['id']."' LIMIT 1")->fetch_assoc();
		if(!$this->compare) return $this->redirect($this->wiki, 'history/');
		$this->with = db()->query("
			SELECT
				wiki_history.id AS id,
				wiki_history.timeadded AS timeadded,
				wiki_changes.user AS user,
				wiki_history.content AS content
			FROM wiki_history
			LEFT JOIN wiki_changes ON wiki_changes.history=wiki_history.id AND (wiki_changes.action='article_created' OR wiki_changes.action='content_changed')
			WHERE wiki_history.id='".es($this->args['with'])."' AND wiki_history.page='".$this->page['id']."' LIMIT 1")->fetch_assoc();
		if(!$this->with) return $this->redirect($this->wiki, 'history/');
		if(strtotime($this->compare['timeadded']) > strtotime($this->with['timeadded'])) {
			$temp = $this->compare;
			$this->compare = $this->with;
			$this->with = $temp;
		}
		include_once 'lib/DifferenceEngine.php';
		$d = new Diff(explode("\n", $this->compare['content']), explode("\n", $this->with['content']));
		$df = new TableDiffFormatter;
		$this->diff = $df->format($d);
		return $this->ilphp_fetch();
	}
	public function history_get_reason(&$i, $action_after_time = false) {//function called in template
		if($i['action']) {
			switch($i['action']) {
			default: $d = $i['action']; break;
			case 'history_activated': $d = LS('&Auml;nderungen aktiviert'); break;
			case 'history_sighted': $d = LS('Neueste &Auml;nderungen gesichtet'); break;
			case 'content_changed': $d = LS('Seite ge&auml;ndert'); break;
			case 'article_created': $d = LS('Seite erstellt'); break;
			case 'article_deleted': $d = LS('Seite gel&ouml;scht'); break;
			case 'page_locked': $d = LS('Seite gesperrt'); break;
			case 'page_unlocked': $d = LS('Seite entsperrt'); break;
			case 'page_renamed': $d = LS('Seite umbenannt'); break;
			case 'language_changed': $d = LS('Sprache ge&auml;ndert'); break;
			}
			$d = htmlspecialchars($d);
			$d .= ' '.LS('von').' '.user($i['user'])->html(-1).' ';
			if($action_after_time) $d .= htmlspecialchars(timeelapsed($action_after_time, LS('nach')));
			else $d .= timeago($i['timeadded']);
			if($i['reason']) $d .= ": ";
			$d = '<span class="historyInfo">'.$d.'</span>';
		}
		elseif(!$i['reason']) return '-';
		return $d ? $d.htmlspecialchars($i['reason']) : htmlspecialchars($i['reason']);
	}

	private function rename() {
		$this->way[] = array(LS('Artikel umbenennen'), '/wiki/'.wiki_urlencode($this->page['name']).'/rename_page/');
		$this->newname = wiki_urldecode((isset($this->args['name']) and $this->args['name']) ? $this->args['name'] : $this->args['rename_page']);
		if($this->newname == $this->wiki) {
			$this->errors[] = 'PAGE_RENAME_SAME_NAME';
			return $this->history_overview();
		}
		elseif(db()->query("SELECT 1 FROM wiki_pages WHERE id!='".$this->page['id']."' AND lang='".LANG."' AND name='".es($this->newname)."' AND deleted=0 LIMIT 1")->num_rows) {
			$this->errors[] = 'PAGE_RENAME_ALREADY_EXISTS';
			return $this->history_overview();
		}
		elseif(isset($this->args['name']) and $this->args['name']) {
			$this->action = 'RENAME_PAGE';
			$this->num = db()->query("
				SELECT COUNT(page) AS num
				FROM wiki_history
				WHERE
					content REGEXP '\\\\[\\\\[ *".es(preg_quote($this->page['name']))." *(\\\\||\\\\]\\\\])' OR
					content REGEXP '\\\\[\\\\[ *".es(preg_quote(wiki_urlencode($this->page['name'])))." *(\\\\||\\\\]\\\\])'")->fetch_assoc();
			if($this->num) $this->num = $this->num['num'];
			return $this->ilphp_fetch();
		}
		elseif($this->args['rename_page']) {
			if(isset($this->args['change_links'])) {
				$history = db()->query("
					SELECT id, page, content
					FROM wiki_history
					WHERE
						content REGEXP '\\\\[\\\\[ *".es(preg_quote($this->page['name']))." *(\\\\||\\\\]\\\\])' OR
						content REGEXP '\\\\[\\\\[ *".es(preg_quote(wiki_urlencode($this->page['name'])))." *(\\\\||\\\\]\\\\])'
					ORDER BY page");
				$reason = LS('Bot: Verlinkung von Artikel %1% in %2% ge&auml;ndert; vorgenommene &Auml;nderungen: ', $this->wiki, $this->newname);
				$current_page = 0;
				$current_changes = 0;
				while($i = $history->fetch_assoc()) {
					if($current_page != $i['page']) {
						if($current_page and $current_changes)
							db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='".$current_page."', action='links_renamed', reason='".es($reason.$current_changes)."'");
						$current_page = $i['page'];
						$current_changes = 0;
					}
					$new = preg_replace('~\[\[ *'.preg_quote($this->page['name'], '~').' *(\||\]\])~i', '[['.$this->newname.'$1', $i['content']);
					$new = preg_replace('~\[\[ *'.preg_quote(wiki_urlencode($this->page['name']), '~').' *(\||\]\])~i', '[['.wiki_urlencode($this->newname).'$1', $new);
					if($new != $i['content']) {
						db()->query("UPDATE wiki_history SET content='".es($new)."' WHERE id='".$i['id']."' LIMIT 1");
						if(db()->affected_rows) $current_changes++;
					}
				}
				if($current_page and $current_changes)
					db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='".$current_page."', action='links_renamed', reason='".es($reason.$current_changes)."'");
			}
			db()->query("UPDATE wiki_pages SET name='".es($this->newname)."' WHERE id='".$this->page['id']."' AND deleted=0 LIMIT 1");
			db()->query("INSERT INTO wiki_changes SET user='".USER_ID."', page='".$this->page['id']."', action='page_renamed', reason='".es($this->page['name'])." -> ".es($this->newname)."'");
			return $this->redirect($this->newname, 'history/');
		}
		else
			return $this->redirect($this->wiki, 'history/');
	}

	private function search() {
		$this->action = 'SEARCH';
		
		if(IS_AJAX)
			return $this->redirect($this->wiki, ((isset($this->args['deactivated']) and $this->args['deactivated']) ? 'qd' : 'q').'/'.wiki_urlencode(trim($this->args['q'])).'/');
		
		$this->q = (isset($this->args['q']) ? 'q' : (isset($this->args['qd']) ? 'qd' : ''));
		$this->term = ((isset($this->args[$this->q]) and $this->args[$this->q]) ? trim(wiki_urldecode($this->args[$this->q])) : '');
		$this->page = ((isset($this->args['page']) and (int)$this->args['page'] > 1) ? (int)$this->args['page'] : 1);
		
		if($this->term) {
			//set_search_category_text('wiki', '', $this->term);
			$this->way[] = array($this->term, '/wiki/Spezial:Suche/'.$this->q.'/'.wiki_urlencode($this->term).'/');
			if($this->page > 1) $this->way[] = array('Seite '.$this->page, '/wiki/Spezial:Suche/'.$this->q.'/'.wiki_urlencode($this->term).'/page/'.$this->page.'/');
			if(!has_privilege('wiki_mod') or true) {
				$this->ilphp_init('', 30, $this->action.','.$this->q.','.$this->term.",".$this->page);
				#########if($data = $this->ilphp_cache_load()) return $data;
			}
			$t = es($this->term);
			$e = explode(' ', preg_replace('~  +~', ' ', trim($t)));
			$score = "IF(%F LIKE '%".implode("%',1,0)+IF(%F LIKE '%", $e)."%',1,0)";
			$like = "%F LIKE '%".implode("%' AND %F LIKE '%", $e)."%'";
			$this->results = db()->query("
				SELECT SQL_CALC_FOUND_ROWS
					wiki_pages.id AS id, wiki_pages.name AS name, wiki_pages.history AS history,
					(".str_replace('%F', 'wiki_pages.name', $score).")*10 +
					(".str_replace('%F', 'wiki_history.content', $score).") +
					(MATCH (wiki_pages.name) AGAINST ('$t'))*10 +
					(MATCH (wiki_history.content) AGAINST ('$t')) AS score
				FROM wiki_pages
				LEFT JOIN wiki_history ON wiki_history.id=wiki_pages.history
				WHERE
					".($this->q == 'qd' ? "" : "wiki_pages.history AND")."
					(
						(".str_replace('%F', 'wiki_pages.name', $like).") OR
						(".str_replace('%F', 'wiki_history.content', $like).")
					) AND
					wiki_pages.deleted=0
				ORDER BY IF(wiki_pages.lang='".LANG."',0,1) ASC, score DESC
				LIMIT ".(($this->page - 1)*15).", 15");
			$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, 15);
		}
		
		return $this->ilphp_fetch();
	}
	public function search_query_aliases() {
		$this->aliases = db()->query("SELECT name FROM wiki_aliases WHERE page='".$this->i['id']."' ORDER BY name");
	}
	
	public function update_page_structure() {
		$ws = wikicode::parse($this->page['name'], $this->page['content']);
		#db()->query("DELETE FROM wiki_headings WHERE page='".$this->page['id']."'");
		db()->query("DELETE FROM wiki_aliases WHERE page='".$this->page['id']."'");
		foreach($ws->aliases as $a) db()->query("INSERT INTO wiki_aliases SET page='".$this->page['id']."', name='".es($a)."'");
		#foreach($ws->headings as $a) db()->query("INSERT INTO wiki_aliases SET page='".$this->page['id']."', name='".es($a)."'");
	}
}

?>
