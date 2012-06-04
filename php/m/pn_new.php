<?php

class m_pn_new extends imodule {
	use ilphp_trait;
	use im_way;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function INIT(&$args) {
		if(!IS_LOGGED_IN) throw new iexception('ACCESS_DENIED', $this);
		$user_id = (int)@$args[$this->imodule_name];
		if(!$user_id) throw new iexception('USER_NOT_FOUND', $this);
		$this->user =& user($user_id)->i;
		if(user($user_id)->deleted) throw new iexception('USER_DELETED', $this);
	}
	
	protected function POST_send(&$args) {
		if(!$args['name'] or !$args['message']) return;
		$pn_id = user()->pn_new([$this->user['user_id']], $args['name'], $args['message']);
		page_redir('/'.LANG.'/pn/'.$pn_id.'-'.urlenc($args['name']).'/');
	}
	
	protected function MODULE(&$args) {
		$this->url .= '/'.LANG.'/pn_new/'.$this->user['user_id'].'-'.urlenc($this->user['nick']).'/';
		$this->way[] = array(LS('Private Nachricht an %1% schicken', $this->user['nick']), $this->url);
		$this->im_way_title();
		return $this->ilphp_fetch('pn_new.php.ilp');
	}
}

?>
