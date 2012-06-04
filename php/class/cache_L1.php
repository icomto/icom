<?php

class cache_L1 {
	public static function get($id) {
		return apc_fetch($id);
	}
	public static function set($id, $timeout, $site) {
		return apc_store($id, $site, $timeout);
	}
	public static function exists($id) {
		return apc_exists($id);
	}
	public static function del($id) {
		return apc_delete($id);
	}
	public static function inc($id) {
		return apc_inc($id);
	}
	public static function dec($id) {
		return apc_dec($id);
	}
}

/*class cache_L1 {
	public static function get($id) {
		if(!xcache_isset($id)) return false;
		return xcache_get($id);
	}
	public static function set($id, $timeout, $site) {
		return xcache_set($id, $site, $timeout);
	}
	public static function exists($id) {
		return xcache_isset($id);
	}
	public static function del($id) {
		return xcache_unset($id);
	}
	public static function inc($id) {
		return xcache_inc($id);
	}
	public static function dec($id) {
		return xcache_dec($id);
	}
}*/

/*class cache_L1 {
	public static function get($id) {
		return cache_L2::get('L1_'.$id);
	}
	public static function set($id, $timeout, $site) {
		return cache_L2::set('L1_'.$id, $timeout, $site);
	}
	public static function exists($id) {
		return cache_L2::get('L1_'.$id) === false ? false : true;
	}
	public static function del($id) {
		return cache_L2::del('L1_'.$id);
	}
	public static function inc($id) {
	}
	public static function dec($id) {
	}
}*/

?>
