<?php

define('COMMUNITY_USERS_PER_PAGE', 30);
define('COMMUNITY_BOOKMARKS_PER_PAGE', 30);
define('COMMUNITY_POLLS_PER_PAGE', 30);

class m_community extends im_tabs {
	use ilphp_trait;
	use im_pages;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	protected function INIT(&$args) {
		$this->url = '/'.LANG.'/community/';
		$this->way[] = [LS('Community'), $this->url];
		
		$this->im_tabs_add('groups', LS('Gruppen'), IS_LOGGED_IN ? TAB_SELF : false);
		$this->im_tabs_add('users', LS('Benutzer'), IS_LOGGED_IN ? TAB_SELF : false);
		$this->im_tabs_add('bookmarks', LS('Lesezeichen'), IS_LOGGED_IN ? TAB_SELF : false);
		$this->im_tabs_add('polls', LS('Umfragen'), IS_LOGGED_IN ? TAB_SELF : false);
		
		parent::INIT($args);
	}
	
	protected function TAB_users(&$args) {
		$where = array();
		$group = (int)@$_GET['group'];
		if(!$group) $this->group = array('id' => 'all', 'name' => LS('Alle Mitglieder'));
		else {
			$this->group = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM groups WHERE ".(has_privilege('groupmanager') ? "" : "public AND ")."id='$group' LIMIT 1")->fetch_assoc();
			if(!$this->group) return LS('Die Gruppe mit der ID %1% konnte nicht gefunden werden.', $group);
			$where[] = "FIND_IN_SET('".$this->group['id']."', groups)";
			$this->url .= 'group/'.$this->group['id'].'-'.urlenc($this->group['name']).'/';
		}
		if(isset($_POST['search'])) $this->search = $_POST['search'];
		elseif(isset($_GET['search'])) $this->search = urldecode($_GET['search']);
		else $this->search = '';
		if($this->search) {
			//set_search_category_text('user', '', $this->search);
			$where[] = "nick LIKE '%".str_replace(" ", "%", es($this->search))."%'";
			$this->url .= 'search/'.urlencode(htmlspecialchars($this->search)).'/';
		}
		$this->im_pages_get(@$args['page']);
		$this->users = db()->query("
			SELECT SQL_CALC_FOUND_ROWS user_id, nick, groups
			FROM users".($where ? "
			WHERE ".implode(" AND ", $where) : "")."
			ORDER BY nick
			LIMIT ".(($this->page - 1)*COMMUNITY_USERS_PER_PAGE).", ".COMMUNITY_USERS_PER_PAGE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, COMMUNITY_USERS_PER_PAGE);
		user::init_group_cache();
		$this->groups =& user::$group_cache;
		return $this->ilphp_fetch('community.php.users.ilp');
	}
	
	protected function TAB_groups(&$args) {
		$this->groups = db()->query("
			SELECT g.id AS id, ".LQ('g.name_LL')." AS name, COUNT(u.user_id) AS num
			FROM groups g
			LEFT JOIN users u ON FIND_IN_SET(g.id,u.groups)
			WHERE u.user_id ".(has_privilege('groupmanager') ? "" : "AND public ")."
			GROUP BY g.id
			ORDER BY name");
		return $this->ilphp_fetch('community.php.groups.ilp');
	}
	
	protected function TAB_bookmarks(&$args) {
		$this->im_pages_get(@$args['page']);
		$where = array();
		$this->bookmarks = db()->query("
			SELECT SQL_CALC_FOUND_ROWS a.user_id, a.nick, COUNT(b.thing_id) AS num
			FROM user_bookmarks b, users a".(IS_LOGGED_IN ? "
			LEFT JOIN user_friends c ON c.user_id='".USER_ID."' AND c.friend_id=a.user_id" : "")."
			WHERE
				a.user_id=b.user_id".(IS_LOGGED_IN ? " AND
				a.user_id!='".USER_ID."'" : "")." AND
				(
					a.priv_bookmarks='public'".(IS_LOGGED_IN ? " OR
					a.priv_bookmarks='users' OR
					(
						a.priv_bookmarks='friends' AND
						c.status='accepted'
					)" : "")."
				)
			GROUP BY a.user_id
			ORDER BY a.nick
			LIMIT ".(($this->page - 1)*COMMUNITY_BOOKMARKS_PER_PAGE).", ".COMMUNITY_BOOKMARKS_PER_PAGE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, COMMUNITY_BOOKMARKS_PER_PAGE);
		return $this->ilphp_fetch('community.php.bookmarks.ilp');
	}
	
	protected function TAB_polls_POST_new(&$args) {
		db()->query("INSERT INTO user_polls SET question='".es($args['question'])."', creator='".USER_ID."'");
		$poll_id = db()->insert_id;
		page_redir('/'.LANG.'/poll/'.$poll_id.'-'.urlenc($args['question']).'/settings/');
	}
	protected function TAB_polls(&$args) {
		$this->im_pages_get(@$args['page']);
		$cache_id = (IS_LOGGED_IN ? USER_ID : '0').'-'.$this->page.'-'.implode_arr_list(user()->groups);
		$this->ilphp_init('community.php.polls.ilp', 30, $cache_id);
		if(($data = $this->ilphp_cache_load()) !== false) return $data;;
		$this->polls = db()->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM user_polls a
			WHERE ".m_poll_global::build_where()."
			ORDER BY a.timecreated DESC
			LIMIT ".(($this->page - 1)*COMMUNITY_POLLS_PER_PAGE).", ".COMMUNITY_POLLS_PER_PAGE);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, COMMUNITY_POLLS_PER_PAGE);
		return $this->ilphp_fetch();
	}
}

?>
