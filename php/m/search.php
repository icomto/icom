<?php

class m_search extends imodule {
	use ilphp_trait;
	
	public function __construct() {
		parent::__construct(__DIR__);
		
		if(!isset(session::$s['m_search'])) {
			session::$s['m_search'] = [
				'top' => isset(session::$s['search_category_top']) ? session::$s['search_category_top'] : 'for',
				'sub' => isset(session::$s['search_category_sub']) ? session::$s['search_category_sub'] : 0,
				'text' => ''
			];
			unset(session::$s['search_category']);
			unset(session::$s['search_category_top']);
			unset(session::$s['search_category_sub']);
		}
	}
	
	protected function POST_top(&$args) {
		if(!in_array($args['top'], ['for', 'wiki', 'user'])) $args['top'] = 'for';
		session::$s->m_search['top'] = $args['top'];
	}
	protected function POST_search(&$args) {
		$top = session::$s['p'] = (!empty($args['p']) ? es($args['p']) : 'for');
		$text = preg_replace('~^(Suche|Search)\.\.\.~', '', isset($args['q']) ? $args['q'] : '');
		
		if(!in_array($top, ['for', 'wiki', 'user'])) $top = 'for';
		session::$s['m_search']['top'] = $top;
		session::$s['m_search']['text'] = $text;
		
		if($text) db()->query("INSERT LOW_PRIORITY INTO searches SET id=CRC32(LOWER('".es($text)."')), str='".es($text)."', num=1 ON DUPLICATE KEY UPDATE num=num+1");
		
		switch($top) {
		case 'for': page_redir('/'.LANG.'/forum/0/search/'.urlencode($text ? $text : '-').'/options/1,1,1/');
		case 'wiki': page_redir('/'.LANG.'/wiki/'.LS('Spezial').':'.LS('Suche').'/'.($text ? 'q/'.wiki_urlencode($text).'/' : ''));
		case 'user': page_redir('/'.LANG.'/community/users/'.($text ? 'search/'.urlencode($text).'/' : ''));
		}
	}
	
	protected function MENU(&$args) {
		$this->search =& session::$s['m_search'];
		return $this->ilphp_fetch('search.php.menu.ilp');
	}
}

?>
