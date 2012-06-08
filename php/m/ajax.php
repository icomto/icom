<?php

class m_ajax extends imodule {
	use im_way;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	
	protected function ENGINE(&$args) {
		if(!defined('IS_AJAX')) define('IS_AJAX', true);
		
		if(iengine::$get) iengine::$get->RUN_ONCE('INIT');
		
		if(iengine::$post) {
			G::$USER_INTERACTED = true;
			iengine::$post->RUN_ONCE('INIT');
			$data = iengine::$post->RUN('POST');
			if($data === true) throw new iexception(iengine::$post->imodule_name.' POST RETURNED true', $this);
			elseif($data === 'RELOAD') {
				G::$json_data['s1'][] = 'window.location.reload();';
				echo json_encode(G::$json_data);
				return;
			}
			elseif($data !== NULL) G::$json_data['e']['__obj__'] = $data;
		}
		
		foreach(iengine::$idle as $module)
			$module->RUN_IDLE();
		
		if(USING_COOKIES) {
			if(G::$USER_INTERACTED) db()->query("INSERT INTO ipcounter SET ip='".ip2long($_SERVER['REMOTE_ADDR'])."', lasttime=CURRENT_TIMESTAMP ON DUPLICATE KEY UPDATE hits=hits+1, all_hits=all_hits+1, time_on_site=time_on_site+IF(lasttime>SUBTIME(CURRENT_TIMESTAMP,'00:02:05.00000'),UNIX_TIMESTAMP(CURRENT_TIMESTAMP)-UNIX_TIMESTAMP(lasttime),0), lasttime=CURRENT_TIMESTAMP");
			else db()->query("INSERT INTO ipcounter SET ip='".ip2long($_SERVER['REMOTE_ADDR'])."', lasttime=CURRENT_TIMESTAMP ON DUPLICATE KEY UPDATE all_hits=all_hits+1, time_on_site=time_on_site+IF(lasttime>SUBTIME(CURRENT_TIMESTAMP,'00:02:05.00000'),UNIX_TIMESTAMP(CURRENT_TIMESTAMP)-UNIX_TIMESTAMP(lasttime),0), lasttime=CURRENT_TIMESTAMP");
		}
		
		self::set_ajax_update_val();
		
		G::$json_data['s1'][] = 'ilrtd.imodules='.iengine::TO_JSON();
		G::$json_data['s1'][] = 'user_tooltips=$.extend(user_tooltips, '.user::USER_TOOLTIPS_JSON().');';
		
		echo json_encode(G::$json_data);
	}
	
	protected function POST_select_module(&$args) {
		try {
			if(!IS_AJAX) throw new iexception('501', $this);
			if(!iengine::$get) throw new iexception('404', $this);
			G::$USER_INTERACTED = true;
			theme::init($this);
			iengine::$get->RUN_ONCE('INIT');
			$CONTENT = iengine::$get->RUN('MODULE');
			G::$json_data['title'] = stripslashes(implode(utf8_encode(' « '), array_reverse(array_merge(array(SITE_NAME), G::$SITE_TITLE))));
			return '<div class="module" id="Module">'.$CONTENT.'</div>';
		}
		catch(iexception $e) {
			page_redir(rebuild_location());
		}
	}
	
	protected function MODULE(&$args) {
		throw new iexception('m_ajax can not be called as module', $this);
	}
	
	private function calc_update_val($id, $time_window) {
		return $time_window;
		$t = explode(' ', microtime());
		$t = round((($t[0] + $t[1]) - 1295565000)*1000);
		db()->query("SELECT GET_LOCK('calc_update_val_$id',2)");
		$ajax = db()->query("SELECT * FROM ajax_update WHERE id=$id LIMIT 1")->fetch_assoc();
		
		if(!$ajax) {
			db()->query("INSERT IGNORE INTO ajax_update SET id=$id, Tc=$t, i=1");
			db()->query("SELECT RELEASE_LOCK('calc_update_val_$id')");
			return $time_window;
		}
		
		if($t - $ajax['Tc'] > $time_window*1) {
			db()->query("UPDATE ajax_update SET Tc=$t, N=(N + i)/2, i=1 WHERE id=$id AND Tc<".round($t - $time_window*0.3));
			db()->query("SELECT RELEASE_LOCK('calc_update_val_$id')");
			return $time_window;
		}
		
		db()->query("UPDATE ajax_update SET i=i+1 WHERE id=$id");
		db()->query("SELECT RELEASE_LOCK('calc_update_val_$id')");
		if(!$ajax['N']) return $time_window;
		
		$rv = round(($time_window*1 + ((($ajax['i'] + 1)*(($time_window*1)/$ajax['N'])) - ($t - $ajax['Tc'])))/1);
		if($rv < $time_window) return $time_window;
		return $rv;
	}
	
	private function calc_update_val2($id, $Tr) {
		#return $Tr;
		$t = explode(' ', microtime());
		$t = round((($t[0] + $t[1]) - 1295565000)*1000);
		db()->query("CALL calc_ajax_update($id, $t, $Tr, @rv)");
		return db()->query("SELECT @rv AS rv")->fetch_object()->rv;
	}
	
	private function set_ajax_update_val() {
		if(IS_LOGGED_IN) {
			if(($time_window = cache_L1::get('ajax_update_user')) == false) {
				$time_window = cache_L2::get('ajax_update_user');
				if($time_window <= 1) {
					$time_window = 2;
					cache_L2::set('ajax_update_user', 30, $time_window);
				}
				cache_L1::set('ajax_update_user', 30, $time_window);
			}
			$fast_ajax_update = do_fast_ajax_update();
			if(!$fast_ajax_update) $id = 1;
			else {
				$id = $fast_ajax_update;
				$time_window /= $fast_ajax_update;
			}
		}
		else {
			if(($time_window = cache_L1::get('ajax_update_guest')) == false) {
				$time_window = cache_L2::get('ajax_update_guest');
				if($time_window <= 20) {
					$time_window = 20;
					cache_L2::set('ajax_update_guest', 30, $time_window);
				}
				cache_L1::set('ajax_update_guest', 30, $time_window);
			}
			$id = 100;
		}
		$t = round($time_window*1000);
		#$t = self::calc_update_val2($id, $time_window*1000);
		G::$json_data['s'][] = 'ajaxUpdateInterval='.$t.';';
	}
}

/*
DROP PROCEDURE IF EXISTS calc_ajax_update;
DELIMITER ;;
CREATE PROCEDURE calc_ajax_update(
		IN _id INT(10) UNSIGNED,
		IN _t BIGINT(20) UNSIGNED,
		IN _Tr FLOAT UNSIGNED,
		OUT _retval BIGINT UNSIGNED) LANGUAGE SQL NOT DETERMINISTIC READS SQL DATA
BEGIN
	DECLARE _b INT(10);
	DECLARE _Tc BIGINT(20) UNSIGNED DEFAULT 0;
	DECLARE _N BIGINT(20) UNSIGNED DEFAULT 0;
	DECLARE _i BIGINT(20) UNSIGNED DEFAULT 0;

	SELECT GET_LOCK(CONCAT("calc_update_val_",_id),2) INTO _b;
	SELECT Tc, N, i INTO _Tc, _N, _i FROM ajax_update WHERE id=_id LIMIT 1;
	SET _retval=_Tc;
	IF _Tc = 0 THEN
		INSERT IGNORE INTO ajax_update SET id=_id, Tc=_t, i=1;
		SELECT RELEASE_LOCK(CONCAT("calc_update_val_",_id)) INTO _b;
		SET _retval=_Tr;
	ELSEIF _t - _Tc > _Tr THEN
		UPDATE ajax_update SET Tc=_t, N=(N + i)/2, i=1 WHERE id=_id;
		SELECT RELEASE_LOCK(CONCAT("calc_update_val_",_id)) INTO _b;
		SET _retval=_Tr;
	ELSE
		UPDATE ajax_update SET i=i+1 WHERE id=_id;
		SELECT RELEASE_LOCK(CONCAT("calc_update_val_",_id)) INTO _b;
		SET _retval=_Tr + ((_i + 1)*(_Tr/_N)) - (_t - _Tc);
		IF _retval < _Tr THEN
			SET _retval = _Tr;
		END IF;
	END IF;
END ;;
DELIMITER ;
CALL calc_ajax_update(100, 12656873, 2243, @rv);
SELECT @rv;
SELECT * FROM ajax_update ORDER BY id;
DELETE FROM ajax_update WHERE id IN (0,100);
*/

?>
