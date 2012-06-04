<?php

class menu {
	public static function radio_get_infos($only_stats = false, $channel_id = NULL) {
		if($channel_id === NULL) $channel_id = session::$s['menu_radio_tab'];
		
		$channel = db()->query("SELECT SQL_CACHE * FROM radio WHERE channel='".es($channel_id)."' LIMIT 1")->fetch_assoc();
		if(!$channel and $channel_id == session::$s['menu_radio_tab']) session::$s['menu_radio_tab'] = RADIO_DEFAULT_CHANNEL;
		
		$tpl = new ilphp('menu.radio.tab'.($only_stats ? '.stats' : '').'.ilp', 20, $channel_id);
		#if($site = $tpl->ilphp_cache_load()) return $site;
		$tpl->error = 'ERR_LOADING';
		
		if($channel) $tpl->channel =& $channel;
		else $tpl->channel = db()->query("SELECT SQL_CACHE * FROM radio WHERE channel='".es($channel_id)."' LIMIT 1")->fetch_assoc();
		if(!$tpl->channel) return $tpl->ilphp_fetch();
		
		if($tpl->channel['admins']) $tpl->channel['admins'] = db()->query("SELECT SQL_CACHE user_id, nick FROM users WHERE user_id IN (".es($tpl->channel['admins']).") AND NOT user_id IN (1) ORDER BY nick");
		if($tpl->channel['guests']) $tpl->channel['guests'] = db()->query("SELECT SQL_CACHE user_id, nick FROM users WHERE user_id IN (".es($tpl->channel['guests']).") AND NOT user_id IN (1) ORDER BY nick");
		
		if(!$tpl->channel['online']) {
			$tpl->error = "ERR_OFFLINE";
			return $tpl->ilphp_fetch();
		}
		
		$tpl->error = '';
		if($tpl->channel['current_dj']) $tpl->channel['current_dj'] =& user(es($tpl->channel['current_dj']))->i;
		
		return $tpl->ilphp_fetch();
	}
	public static function radio() {
		if(isset($_GET['_radiotab']) and db()->query("SELECT 1 FROM radio WHERE channel='".es($_GET['_radiotab'])."' LIMIT 1")->num_rows)
			session::$s['menu_radio_tab'] = $_GET['_radiotab'];
		if(session::$s['_ucs']['radio']) {
			$tpl = new ilphp('menu.radio.ilp');
			$tpl->location = rebuild_location();
			$tpl->current = session::$s['menu_radio_tab'];
			$tpl->channels = db()->query("SELECT SQL_CACHE channel, online FROM radio ORDER BY channel");
			return $tpl->ilphp_fetch();
		}
	}
	public static function get_radio_channels() {
		if(($channels = cache_L1::get('radio_channels')) !== false) return $channels;
		$aa = db()->query("SELECT SQL_CACHE channel, online, host, port FROM radio ORDER BY channel");
		$channels = array();
		while($a = $aa->fetch_assoc()) $channels[] = $a;
		cache_L1::set('radio_channels', 10, $channels);
		return $channels;
	}
	public static function radio2() {
		if(($data = cache_L1::get('menu_radio2')) !== false) return $data;
		$tpl = new ilphp('menu.radio2.ilp');
		$tpl->channels = self::get_radio_channels();
		$data = $tpl->ilphp_fetch();
		cache_L1::set('menu_radio2', 10, $data);
		return $data;
	}
	public static function module_radio() {
		if(@$_GET['radio'] and db()->query("SELECT 1 FROM radio WHERE channel='".es($_GET['radio'])."' LIMIT 1")->num_rows)
			session::$s['menu_radio_tab'] = $_GET['radio'];
		$tpl = new ilphp('radio.content.ilp');
		$tpl->current = session::$s['menu_radio_tab'];
		$tpl->channels = self::get_radio_channels();
		return $tpl->ilphp_fetch();
	}
}

?>
