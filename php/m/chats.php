<?php

class m_chats extends imodule {
	use ilphp_trait;
	use im_way;
	
	public $error = '';
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		$this->url = '/'.LANG.'/chats/';
		$this->way[] = array(LS('Chats'), $this->url);
	}
	
	protected function POST_new(&$args) {
		if(!IS_LOGGED_IN) throw new iexception('403', $this);
		if(!$args['name']) return;
		
		$category = db()->query("SELECT id, has_sub_categorys FROM user_chat_categorys WHERE ".(m_chat_global::is_ultra_admin() ? "1" : "MATCH (groups) AGAINST ('".implode(' ', user()->groups)."' IN BOOLEAN MODE)")." ORDER BY place, ".LQ('name_LL')." LIMIT 1")->fetch_assoc();
		if(!$category) {
			$this->error = 'NO_POSSIBLE_CATEGORY';
			return IS_AJAX ? $this->MODULE($args) : NULL;
		}
		else {
			if(!$category['has_sub_categorys']) $sub_category['id'] = 0;
			else {
				$sub_category = db()->query("SELECT id FROM user_chat_sub_categorys WHERE category_id='".$category['id']."' ORDER BY ".LQ('name_LL')." LIMIT 1")->fetch_assoc();
				if(!$sub_category) $sub_category['id'] = 0;
			}
			db()->query("
				INSERT INTO user_chats
				SET
					category_id='".es($category['id'])."',
					sub_category_id='".es($sub_category['id'])."',
					name='".es($this->args['name'])."',
					creator='".USER_ID."',
					admins='".USER_ID."'");
			$chat_id = db()->insert_id;
			page_redir('/'.LANG.'/chat/'.$chat_id.'-'.urlenc($this->args['name']).'/settings/');
		}
	}
	
	protected function MODULE(&$args) {
		try {
			$this->category_id = (int)$args[$this->imodule_name];
			$this->sub_category_id = (isset($args['sub']) ? (int)$args['sub'] : 0);
			#define('CURRENT_USER_CHAT_SUBCATEGORY', $this->sub_category_id);
			
			$cache_id = (IS_LOGGED_IN ? USER_ID : '0').'-'.implode_arr_list(user()->groups).'-'.$this->category_id.'-'.$this->sub_category_id;
			$this->ilphp_init('chats.php.ilp', 30, $cache_id);
			
			if(($data = $this->ilphp_cache_load()) !== false) return $data;;
			$this->category_id = $this->category_id;
			
			if($this->category_id) {
				$this->category = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM user_chat_categorys WHERE id='".$this->category_id."' LIMIT 1")->fetch_assoc();
				if(!$this->category) throw new iexception('CATEGORY_NOT_FOUND', $this);
				$this->way[] = array($this->category['name'], $this->url.$this->category['id'].'-'.urlenc($this->category['name']).'/');
				if($this->sub_category_id) {
					$this->sub_category = db()->query("SELECT id, ".LQ('name_LL')." AS name FROM user_chat_sub_categorys WHERE id='".$this->sub_category_id."' AND category_id='".$this->category_id."' LIMIT 1")->fetch_assoc();
					if(!$this->sub_category) throw new iexception('SUB_CATEGORY_NOT_FOUND', $this);
					$this->way[] = array($this->sub_category['name'], $this->url .= 'sub/'.$this->sub_category['id'].'-'.urlenc($this->sub_category['name']).'/');
				}
			}
			
		}
		catch(Exception $e) {
			$this->error = $e->getMessage();
			$this->im_way_title();
			$this->ilphp_clear();
			return $this->ilphp_fetch('chats.php.ilp');
		}
		
		db()->query("DELETE FROM user_chat_online_users WHERE lasttime<SUBTIME(CURRENT_TIMESTAMP,'".LS_USER_ALIVE_TIME."')");
		db()->query("DELETE FROM user_chat_online_guests WHERE lasttime<SUBTIME(CURRENT_TIMESTAMP,'".LS_USER_ALIVE_TIME."')");
		
		$this->chats = db()->query("
			SELECT
				a.id AS id, a.lang AS lang, a.name AS name, a.status AS status,
				COALESCE(MAX(b.timeadded),a.timecreated) AS lastmessage,
				COUNT(b.id) AS num_messages,
				(SELECT COUNT(*) FROM user_chat_online_users ou WHERE ou.chat_id=a.id) AS online_users,
				(SELECT COUNT(*) FROM user_chat_online_guests og WHERE og.chat_id=a.id) AS online_guests,
				c.id AS category_id, ".LQ('c.name_LL')." AS category_name, c.has_sub_categorys AS has_sub_categorys,
				d.id AS sub_category_id, ".LQ('d.name_LL')." AS sub_category_name
			FROM user_chat_categorys c, user_chats a
			LEFT JOIN user_chat_content b ON a.id=b.subid
			LEFT JOIN user_chat_sub_categorys d ON a.sub_category_id=d.id
			WHERE
				".m_chat_global::build_where().($this->category_id ? " AND
				a.category_id='".$this->category_id."'" : "").(($this->category_id and $this->sub_category_id) ? " AND
				a.sub_category_id='".$this->sub_category_id."'" : "")." AND
				c.id=a.category_id
			GROUP BY a.id
			ORDER BY
				c.place,
				a.place,
				IF(
					c.order_by='name',
					".LQ('d.name_LL').",
					TIMESTAMPDIFF(SECOND,COALESCE(MAX(b.timeadded),a.timecreated),CURRENT_TIMESTAMP)*0.000000000001
				),
				IF(c.order_by='name',a.name,0)");
		
		return $this->ilphp_fetch();
	}
	
	protected function MENU(&$args) {
		$category_ids =& $args['category_ids'];
		$cache_id = (IS_LOGGED_IN ? USER_ID : '0').'-'.implode_arr_list(user()->groups).'-'.$category_ids;
		$this->ilphp_init('chats.php.menu.ilp', 30, $cache_id);
		if(($data = cache_L1::get($this->ilphp_cache_file)) !== false) return $data;
		if(($data = $this->ilphp_cache_load()) !== false) {
			cache_L1::set($this->ilphp_cache_file, 15, $data);
			return $data;
		}
		
		$this->chats = db()->query("
			SELECT
				a.id, a.name, a.status,
				c.id AS category_id, ".LQ('c.name_LL')." AS category_name, c.has_sub_categorys AS has_sub_categorys,
				d.id AS sub_category_id, ".LQ('d.name_LL')." AS sub_category_name
			FROM user_chat_categorys c, user_chats a
			LEFT JOIN user_chat_sub_categorys d ON a.sub_category_id=d.id
			WHERE
				".m_chat_global::build_where('open')." AND
				a.category_id IN ($category_ids) AND
				c.id=a.category_id
			GROUP BY a.id
			ORDER BY a.place, ".LQ("d.name_LL").", a.name");
		return $this->ilphp_fetch();
	}
}

?>
