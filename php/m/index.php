<?php

class m_index extends imodule {
	use ilphp_trait;
	
	private $menus = [];
	private $rethrow = NULL;
	
	public function __construct() {
		parent::__construct(__DIR__);
		
		if(!isset(session::$s['m_index'])) session::$s['m_index'] = [];
		if(isset(session::$s['_ucs'])) {
			$ucs = session::$s['_ucs']->toArray();
			session::$s['m_index']['online_users'] = $ucs['userstats'];
			session::$s['m_index']['chat_137'] = $ucs['team_shoutbox'];
			session::$s['m_index']['shoutbox'] = $ucs['shoutbox'];
			session::$s['m_index']['admin_menu'] = $ucs['admin'];
			session::$s['m_index']['forum_team_10'] = $ucs['forum_team'];
			session::$s['m_index']['forum_news_5'] = $ucs['forum_news'];
			session::$s['m_index']['forum_def_15'] = $ucs['forum'];
			unset(session::$s['_ucs']);
		}
		session::$s->changed = true;
	}
	public function __destruct() {
		if($this->rethrow) throw $this->rethrow;
	}
	
	protected function ENGINE(&$args) {
		if(!defined('IS_AJAX')) define('IS_AJAX', false);
		
		G::$USER_INTERACTED = true;
		
		if(USING_COOKIES) db()->query("INSERT INTO ipcounter SET ip='".ip2long($_SERVER['REMOTE_ADDR'])."', lasttime=CURRENT_TIMESTAMP ON DUPLICATE KEY UPDATE hits=hits+1, all_hits=all_hits+1, time_on_site=time_on_site+IF(lasttime>SUBTIME(CURRENT_TIMESTAMP,'00:02:05.00000'),UNIX_TIMESTAMP(CURRENT_TIMESTAMP)-UNIX_TIMESTAMP(lasttime),0), lasttime=CURRENT_TIMESTAMP");
		
		theme::init($this);
		$this->LANG_TIME =& G::$LANG_TIME;
		
		try {
			if(!iengine::$get) throw new iexception('404', $this);
			iengine::$get->RUN_ONCE('INIT');
			
			if(iengine::$post) {
				iengine::$post->RUN_ONCE('INIT');
				$data = iengine::$post->RUN('POST');
				if($data === true) throw new iexception(iengine::$post->imodule_name.' POST RETURNED true', $this);
				elseif($data === 'RELOAD') page_redir($_SERVER['REQUEST_URI']);
				elseif($data !== NULL) throw new iexception(iengine::$post->imodule_name.' POST RETURNED data IN INDEX MODE', $this);
			}
			
			$this->MODULE_CONTENT = iengine::$get->RUN('MODULE');
		}
		catch(iexception $e) {
			switch($e->msg) {
			default:
				throw $e;
			case '403':
			case 'ACCESS_DENIED':
				iengine::$get = iengine::GET(['error', '403']);
				break;
			case '404':
				iengine::$get = iengine::GET(['error', '404']);
				break;
			case '403_404':
				iengine::$get = iengine::GET(['error', '403_404']);
				break;
			case '404_501':
			case 'USER_NOT_FOUND':
				iengine::$get = iengine::GET(['error', '404_501']);
				if(!iengine::$get) throw $e;
				$this->rethrow = $e;
				break;
			}
			iengine::$get->RUN_ONCE('INIT');
			$this->MODULE_CONTENT = iengine::$get->RUN('MODULE');
		}
		
		$this->SITE_TITLE = stripslashes(implode(utf8_encode(' « '), array_reverse(array_merge(array(SITE_NAME), G::$SITE_TITLE))));
		$this->META_KEYWORDS =& G::$META_KEYWORDS;
		$this->META_DESCRIPTION =& G::$META_DESCRIPTION;
		
		$this->ilphp_display('index.php.ilp');
	}
	
	protected function MODULE(&$args) {
		$site = '';
		
		$SITE_TITLE = G::$SITE_TITLE;
		
		$news = iengine::GET('news', ['action' => 'latest']);
		if($news) {
			if(($news_id = cache_L1::get('news_lastest_top')) === false) {
				$where[] = "a.status='public'";
				$a = db()->query("SELECT news_id FROM news WHERE status='public' ORDER BY lastupdate DESC LIMIT 1")->fetch_assoc();
				$news_id = ($a ? $a['news_id'] : 0);
				cache_L1::set('news_lastest_top', 30, $news_id);
			}
			if($news_id) $site .= $news->news($news_id, 'display_small');
		}
		
		$forum = iengine::GET('forum', ['forum' => 0]);
		if($forum) {
			$forum->RUN('INIT');
			$site .= $forum->RUN('MODULE');
		}
		
		G::$SITE_TITLE = $SITE_TITLE;
		
		return $site;
	}
	
	
	public function POST_slide(&$args) {
		$module = $args['module'].($args['id'] == '_' ? '' : ($args['id'] ? '_'.$args['id'] : ''));
		if(!isset(session::$s['m_index'][$module])) {
			try {
				if(!iengine::GET($args['module'], [], $args['id']))
					return;
			}
			catch(imodule_exception $e) {
				return;
			}
		}
		session::$s['m_index'][$module] = ($args['display'] == 'no' ? 0 : 1);
	}
	
	
	public function add_menu($group, $title, $classes, $module, $args = []) {
		$this->ilphp_init('index.php.menu_item.ilp');
		$M = iengine::GET($module, $args);
		$this->content = $M->RUN('MENU');
		if(!$this->content) return;
		if(preg_match('~^MODULE\->(.*)$~', $title, $out)) $title = $M->$out[1];
		if(is_array($module)) $module = implode('_', $module);
		$this->module = $module;
		$this->args_ = ($args ? implode('_', $args) : '');
		$this->name = $module.($this->args_ ? '_'.$this->args_ : '');
		if(!$this->args_) $this->args_ = '_';
		$this->title =& $title;
		$this->classes =& $classes;
		if(!isset($this->menus[$group])) {
			$this->first = true;
			$this->menus[$group] = true;
		}
		$this->display = (isset(session::$s['m_index'][$this->name]) ? !empty(session::$s['m_index'][$this->name]) : true);
		return $this->ilphp_fetch();
	}
}

?>
