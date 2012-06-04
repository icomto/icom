<?php

define('NEWS_SECTION_ID', 236);

function is_newswriter() {
	return has_privilege('newswriter');
}

class m_news extends imodule {
	use ilphp_trait;
	use im_way;
	use im_pages;
	
	public function __construct() {
		parent::__construct(__DIR__);
		$this->url = '/'.LANG.'/news/';
		$this->way = [[LS('News'), $this->url]];
	}
	
	protected function MODULE(&$args) {
		$news_id = (int)@$args['news'];
		if($news_id > 0) return $this->news($news_id, (isset($args['edit']) and is_newswriter()) ? 'edit' : 'display');
		else return $this->overview($args);
	}
	
	protected function POST_new_acticle(&$args) {
		if(!is_newswriter()) throw new iexception('403', $this);
		if(empty($args['name'])) return;
		db()->query("
			INSERT INTO news
			SET
				user_id='".USER_ID."',
				name='".es($args['name'])."'");
		$news_id = db()->insert_id;
		page_redir($this->url.$news_id.'-'.urlenc($args['name']).'/edit/');
	}
	
	protected function overview(&$args) {
		$this->url .= 'overview/';
		$this->im_pages_get(@$args['page']);
		$this->im_pages_way();
		
		$where = array();
		if(!is_newswriter()) $where[] = "a.status='public'";
		if(isset($args['tag'])) $where[] = "MATCH (a.tags) AGAINST ('+".es(str_replace(' ', ' +', $args['tag']))."' IN BOOLEAN MODE)";
		
		$this->news = db()->query("
			SELECT SQL_CALC_FOUND_ROWS
				a.news_id, a.name, a.user_id, a.thread_id, COUNT(b.post_id) - 1 AS num_replys, a.timecreated, a.lastupdate, a.cover, a.introduce_content, a.tags
			FROM news a
			LEFT JOIN forum_posts b ON b.thread_id=a.thread_id AND b.thread_id>0
			WHERE
				1".($where ? " AND
				".implode(" AND\n\t\t\t\t", $where) : "")."
			GROUP BY a.news_id
			ORDER BY a.lastupdate DESC
			LIMIT ".(($this->page - 1)*20).", 20");
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, 20);
		$this->im_way_title();
		return $this->ilphp_fetch('news.php.overview.ilp');
	}
	
	
	
	protected function POST_edit(&$args) {
		if(!is_newswriter()) throw new iexception('403', $this);
		$this->news = db()->query("SELECT * FROM news WHERE news_id='".es($args['news_id'])."' LIMIT 1")->fetch_assoc();
		if(!$this->news) throw new iexception('404', $this);
		$update = array();
		if(!empty($args['name'])) $update[] = "name='".es(trim($args['name']))."'";
		if(!empty($args['cover']) and $args['cover'] != $this->news['cover']) {
			$error = array();
			$cover = image::download(remote_url_ready($args['cover']), true, $error);
			if($cover) $update[] = "cover='$cover'";
		}
		if(!empty($args['tags'])) $update[] = "tags='".es(trim($args['tags']))."'";
		if(!empty($args['status']) and in_array($args['status'], array('edit', 'public', 'deleted'))) {
			switch($args['status']) {
			case 'edit':
				if($this->news['thread_id']) db()->query("UPDATE forum_threads SET open=0 WHERE thread_id='".$this->news['thread_id']."' LIMIT 1");
				break;
			case 'public':
				if($this->news['thread_id']) db()->query("UPDATE forum_threads SET open=1 WHERE thread_id='".$this->news['thread_id']."' LIMIT 1");
				else {
					$lang_de = 1;
					$lang_en = 0;
					db()->query("INSERT INTO forum_threads SET section_id='".NEWS_SECTION_ID."', num_posts='1', lang_de='$lang_de', lang_en='$lang_en'");
					$thread_id = db()->insert_id;
					db()->query("
						INSERT INTO forum_posts
						SET
							thread_id='".$thread_id."',
							user_id='".$this->news['user_id']."',
							name='".es($this->news['name'])."',
							content='".es('[news_introduce]'.$this->news['news_id'].'[/news_introduce]')."'");
					$post_id = db()->insert_id;
					db()->query("UPDATE forum_threads SET firstpost='$post_id', lastpost='$post_id' WHERE thread_id='".$thread_id."' LIMIT 1");
					db()->query("
						UPDATE forum_sections
						SET
							num_threads=num_threads+1".($lang_de ? ",
							num_threads_de=num_threads_de+1" : "").($lang_en ? ",
							num_threads_en=num_threads_en+1" : "").",
							num_posts=num_posts+1".($lang_de ? ",
							num_posts_de=num_posts_de+1" : "").($lang_en ? ",
							num_posts_en=num_posts_en+1" : "").",
							lastthread='".$thread_id."'".($lang_de ? ",
							lastthread_de='".$thread_id."'" : "").($lang_en ? ",
							lastthread_en='".$thread_id."'" : "")."
						WHERE section_id='".NEWS_SECTION_ID."'
						LIMIT 1");
					db()->query("UPDATE LOW_PRIORITY users SET forum_posts=forum_posts+1 WHERE user_id='".$this->news['user_id']."' LIMIT 1");
					$update[] = "thread_id='".$thread_id."'";
					$update[] = "lastupdate=CURRENT_TIMESTAMP";
				}
				break;
			case 'delete':
				if($this->news['thread_id']) db()->query("UPDATE forum_threads SET open=0 WHERE thread_id='".$this->news['thread_id']."' LIMIT 1");
				db()->query("DELETE FROM user_bookmarks WHERE thing='news' AND thing_id='".$this->news['news_id']."'");
				break;
			}
			$update[] = "status='".es($args['status'])."'";
		}
		if(!empty($args['push_it'])) $update[] = "lastupdate=CURRENT_TIMESTAMP";
		if(!empty($args['introduce_content'])) $update[] = "introduce_content='".es(trim($args['introduce_content']))."'";
		if(!empty($args['content'])) $update[] = "content='".es(trim($args['content']))."'";
		if(!empty($args['source_text'])) $update[] = "source_text='".es(trim($args['source_text']))."'";
		if(!empty($args['source_image'])) $update[] = "source_image='".es(trim($args['source_image']))."'";
		if(!empty($args['source_video'])) $update[] = "source_video='".es(trim($args['source_video']))."'";
		if($update) db()->query("UPDATE news SET ".implode(", ", $update)." WHERE news_id='".$this->news['news_id']."' LIMIT 1");
		
		return IS_AJAX ?
			$this->news($this->news['news_id'], 'edit') :
			page_redir($this->url.$this->news['news_id'].'-'.urlenc($this->news['name']).'/edit/');
	}
	
	
	//$action = display|edit|forum
	public function news($news_id, $action = 'display') {
		$where = array();
		if(!is_newswriter()) $where[] = "status='public'";
		
		$this->news = db()->query("
			SELECT *
			FROM news
			WHERE
				news_id='".es($news_id)."'".($where ? " AND
				".implode(" AND\n\t\t\t\t", $where) : "")."
			LIMIT 1")->fetch_assoc();
		if(!$this->news) {
			throw new iexception('404', $this);
			if($action == 'display') return $this->overview();
			else return $this->ilphp_fetch('news.php.'.$action.'.ilp');
		}
		if($this->news['cover']) {
			$this->news['cover'] = image::querylinks($this->news['cover']);
			if($this->news['cover']) $this->news['cover'] = $this->news['cover'][0];
		}
		
		$this->url .= $this->news['news_id'].'-'.urlenc($this->news['name']).'/';
		$this->way[] = array($this->news['name'], $this->url);
		if($action == 'edit') {
			$this->url .= 'edit/';
			$this->way[] = array(LS('Bearbeiten'), $this->url);
		}
		else {
			$this->news['tags'] = array_map('trim', explode_arr_list($this->news['tags']));
		}
		
		if($action == 'display' and $this->news['thread_id']) {
			$this->posts = db()->query("
				SELECT SQL_CALC_FOUND_ROWS
					b.user_id, b.timeadded, b.content
				FROM forum_threads a, forum_posts b
				WHERE
					a.thread_id='".$this->news['thread_id']."' AND
					a.thread_id=b.thread_id AND
					b.post_id!=a.firstpost
				ORDER BY b.post_id
				LIMIT 5");
			$this->num_posts_total = db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num;
		}
		if($action != 'forum' and $action != 'display_small') $this->im_way_title();
		
		return $this->ilphp_fetch('news.php.'.$action.'.ilp');
	}
}

?>
