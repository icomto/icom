<?php

global $CONFIG;
if(!empty($CONFIG['memcached']))
	cache_L2::connect($CONFIG['memcached']['host'], $CONFIG['memcached']['port']);

class cache_L2 {
	public static $handle = NULL;
	
	public static function connect($host, $port) {
		self::$handle = @memcache_pconnect($host, $port) or trigger_error('Could not connect to memcached', E_USER_WARNING);
	}
	
	public static function get($id) {
		if(self::$handle) return @memcache_get(self::$handle, $id);
		else return false;
	}
	public static function set($id, $timeout, $site) {
		if(self::$handle) @memcache_set(self::$handle, $id, $site, false, $timeout);
		return $site;
	}
	public static function del($id) {
		if(self::$handle) @memcache_delete(self::$handle, $id, $timeout);
	}
}

?>
