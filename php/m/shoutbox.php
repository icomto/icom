<?php

class m_shoutbox extends imodule {
	use ilphp_trait;
	use im_pages;
	use im_way;
	use im_chat;

	public function __construct() {
		parent::__construct(__DIR__);

		$this->chat = $this->im_chat_add(LANG, [
			'INIT_function' => 'INIT',
			'table' => 'shoutbox_'.LANG,
			'id_field' => 'id',
			'time_field' => 'timeadded',
			'url' => '/'.LANG.'/shoutbox/0/',
			'default_text' => LS('Keine Support-Fragen und Account-Anfragen in der Shoutbox posten!'),
			'deny_post' => (IS_LOGGED_IN ? false : true)
		]);
		$this->chat->places->menu->limit = 10;
		if(!$this->chat->deny_post) {
			if($denie = user()->denie_entrance('shoutbox')) {
				$this->chat->deny_post =
					($denie['timeending'] == 0 ?
						LS('Du darfst nie wieder in die Shoutbox schreiben.') :
						LS('Du darfst erst wieder %1% in die Shoutbox schreiben.', timeago($denie['timeending'], false))).
					($denie['reason'] ? '<br>'.LS('Grund: ').ubbcode::compile($denie['reason']) : '');
			}
			elseif($denie = user()->denie_entrance('chat')) {
				$this->chat->deny_post = true;
				$this->chat->deny_post =
					($denie['timeending'] == 0 ?
						LS('Du darfst nie wieder in einen Chat schreiben.') :
						LS('Du darfst erst wieder %1% in einen Chat schreiben.', timeago($denie['timeending'], false))).
					($denie['reason'] ? '<br>'.LS('Grund: ').ubbcode::compile($denie['reason']) : '');
			}
			elseif(has_privilege('shoutboxmaster')) {
				$this->chat->has_mod_rights = true;
			}
		}
	}

	public function INIT(&$args) {
		$this->chat->INIT($args);
	}

	protected function IDLE(&$idle) {
		$this->chat->IDLE($idle);
	}

	protected function MENU(&$args) {
		$this->INIT($args);
		return $this->chat->RENDER('menu').'<p class="all-entries" style="border-top:0"><a href="'.htmlspecialchars($this->chat->url).'">'.LS('Alle Eintr&auml;ge').'</a>';
	}
	protected function MODULE(&$args) {
		$this->way[] = [LS('Shoutbox'), $this->chat->url];

		$this->im_way_title();
		return $this->ilphp_fetch('shoutbox.php.ilp');
	}
}

?>
