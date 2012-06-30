<?php

class imodule_exception extends Exception {
}

class iengine {
	protected static $instances = [];

	public static $engine = NULL; //our engine
	public static $get = NULL; //module with GET data
	public static $post = NULL; //module with POST data
	public static $idle = []; //module with IDLE data

	public static function INITIALIZE() {
		unset($_GET['_ajax']);
		unset($_GET['_cutted']);
		unset($_GET['_extended']);

		if(isset($_GET['_engine'])) {
			$engine_module = $_GET['_engine'];
			unset($_GET['_engine']);
		}
		else $engine_module = 'index';

		//convert POST and FILE variables to php array
		$out = [];
		foreach($_POST as $k=>$v) self::parse_post_vars($out, $k, $v);
		foreach($_FILES as $k=>$v) self::parse_post_vars($out, $k, $v);

		if(!empty($out['imodules'])) $IMODULES =& $out['imodules'];
		else $IMODULES = [];

		//apply GET variables to module data
		switch(@$_GET['_action']) {
		default:
			$get_module = $_GET['_action'];
			break;
		case '':
			$get_module = $engine_module;
			break;
		}
		unset($_GET['_action']);

		foreach($_GET as $k=>$v)
			if(!isset($IMODULES[$get_module][$k]))
				$IMODULES[$get_module][$k] = $v;
		if(!$IMODULES) {
			$get_module = $engine_module;
			$IMODULES[$get_module] = [];
		}

		$names = array_keys($IMODULES);
		foreach($names as $name) {
			if(isset($IMODULES[$name])) {
				if(empty($IMODULES[$name]['action'])) $IMODULES[$name]['action'] = '';
				else $IMODULES[$name]['action'] = preg_replace('~[^a-z0-9_]~i', '', $IMODULES[$name]['action']);
			}
		}

		//apply our module data global
		$_POST['imodules'] =& $IMODULES;

		//create engine module
		self::$engine = self::GET($engine_module, []);
		if(!self::$engine) throw new imodule_exception('404');

		//create GET module
		if($engine_module == $get_module) {
			self::$get =& self::$engine;
			if(!empty($IMODULES[self::$get->imodule_name]))
				self::$get->args = $IMODULES[self::$get->imodule_name];
		}
		else self::$get = self::GET($get_module, $IMODULES[$get_module]);

		if(self::$get) {
			if(!isset(self::$get->args['action'])) self::$get->args['action'] = '';
			if(isset(self::$get->args['IDLE'])) {
				self::$get->idle = self::$get->args['IDLE'];
				unset(self::$get->args['IDLE']);
				self::$idle[] = self::$get;
			}
		}
		unset($IMODULES[$get_module]);

		if(!empty(self::$engine->args['action'])) self::$post =& self::$engine;
		elseif(self::$get and self::$get->args['action']) self::$post =& self::$get;
		else {
			//search for POST module
			foreach($IMODULES as $name=>$args) {
				if(!empty($args['action'])) {
					self::$post = self::GET($name, $args);
					unset($IMODULES[$name]);
					break;
				}
			}
		}
		if(self::$post and isset(self::$post->args['IDLE'])) {
			self::$post->idle = self::$post->args['IDLE'];
			unset(self::$post->args['IDLE']);
			self::$idle[] = self::$post;
		}

		//search for IDLE modules
		foreach($IMODULES as $name=>$args) {
			if(!empty($args['IDLE'])) {
				$module = self::GET($name, $args, false);
				if($module) {
					$module->idle = $module->args['IDLE'];
					unset($module->args['IDLE']);
					self::$idle[] = $module;
				}
			}
		}

		if(has_privilege('developer')) {
			echo 'TIME: '.time().'<br>';
			if(self::$engine) self::$engine->_DEBUG_PRINT('ENGINE', 'args');
			if(self::$post) self::$post->_DEBUG_PRINT('POST', 'args');
			if(self::$get) self::$get->_DEBUG_PRINT('GET', 'args');
			foreach(self::$idle as $module) $module->_DEBUG_PRINT('IDLE', 'idle', false);
		}

		self::$engine->RUN_ONCE('INIT');
		self::$engine->RUN('ENGINE');
	}

	private static function parse_post_vars(&$arr, &$k, &$v) {
		$way = explode('/', $k);
		$end = array_pop($way);
		$a =& $arr;
		foreach($way as $k) {
			if(!isset($a[$k])) $a[$k] = [];
			$a =& $a[$k];
		}
		if(!$end) $a[] = $v;
		else $a[$end] = $v;
	}

	private static function GET_VARS($module) {
		$retval = [];
		if(!is_array($module)) $module = explode('__', $module);
		$retval['module'] = array_filter($module, function($v) { return preg_replace('~[^a-z0-9_\-%]~i', '', trim($v)); } );
		$retval['name'] = implode('__', $retval['module']);

		$way = array_filter(array_merge(array('m'), $retval['module']));
		$top = array_pop($way);
		while($way) {
			$retval['file'] = ltrim(implode('/', $way).'/'.$top.'/'.$top.'.php', '/');
			if(file_exists($retval['file'])) break;
			$retval['file'] = ltrim(implode('/', $way).'/'.$top.'.php', '/');
			if(file_exists($retval['file'])) break;
			$top = array_pop($way).'_'.$top;
		}
		if(!file_exists($retval['file'])) return NULL;
		include_once $retval['file'];
		$retval['class'] = ($way ? implode('_', $way).'_' : '').$top;
		return $retval;
	}
	public static function GET_CLASS(&$module, &$args) {
		$class = new $module['class'];
		$class->args =& $args;
		$class->imodule_name = $module['name'];
		self::$instances[count(self::$instances)] =& $class;
		return $class;
	}
	public static function GET($module, $args = []) {
		if(!($module = self::GET_VARS($module))) return;
		return self::GET_CLASS($module, $args);
	}

	public static function TO_JSON() {
		$arr = [];
		foreach(self::$instances as $module) {
			if($module->imodule_args) {
				if(!isset($arr[$module->imodule_name]['IDLE']))
					$arr[$module->imodule_name]['IDLE'] = [];
				self::array_merge($arr[$module->imodule_name]['IDLE'], $module->imodule_args);
			}
		}
		return json_encode($arr);
	}

	public static function array_merge(&$a, &$b) {
		foreach($b as $k=>$v) {
			if(is_array($v)) {
				if(empty($a[$k])) $a[$k] = $v;
				else {
					if(!is_array($a[$k])) $a[$k] = [];
					self::array_merge($a[$k], $v);
				}
			}
			else $a[$k] = $v;
		}
	}
}

?>
