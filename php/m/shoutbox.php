<?php

require_once 'chat_base.php';

class m_shoutbox extends m_chat_base {
	public function __construct() {
		imodule::__construct(__DIR__);
		
		$this->url = '/'.LANG.'/shoutbox/0-Shoutbox/';
		$this->way[] = array(LS('Shoutbox'), '/'.LANG.'/shoutbox/');
		$this->im_way_title = true;
		
		$this->allow_post = IS_LOGGED_IN;
		#if($this->allow_post and isset(session::$s['denie_entrance']['shoutbox'])) {
		if($this->allow_post and ($denie = user()->denie_entrance('shoutbox'))) {
			$this->allow_post = false;
			$this->has_mod_rights = false;
			$this->reason =
				($denie['timeending'] == 0 ?
					LS('Du darfst nie wieder in die Shoutbox schreiben.') :
					LS('Du darfst erst wieder %1% in die Shoutbox schreiben.', timeago($denie['timeending'], false))).
				($denie['reason'] ? '<br>'.LS('Grund: ').ubbcode::compile($denie['reason']) : '');
		}
		#elseif($this->allow_post and isset(session::$s['denie_entrance']['chat'])) {
		elseif($this->allow_post and ($denie = user()->denie_entrance('chat'))) {
			$this->allow_post = false;
			$this->has_mod_rights = false;
			$this->reason =
				($denie['timeending'] == 0 ?
					LS('Du darfst nie wieder in einen Chat schreiben.') :
					LS('Du darfst erst wieder %1% in einen Chat schreiben.', timeago($denie['timeending'], false))).
				($denie['reason'] ? '<br>'.LS('Grund: ').ubbcode::compile($denie['reason']) : '');
		}
		else $this->has_mod_rights = has_privilege('shoutboxmaster');
		$this->link = 'shoutbox';
		$this->table = 'shoutbox_'.LANG;
		$this->default_text = LS('Keine Support-Fragen und Account-Anfragen in der Shoutbox posten!');
		$this->limit_menu = 10;
	}
	
	/*protected function INIT(&$args) {
		$args['subid'] = 0;
		return parent::INIT($args);
	}*/
}

?>
