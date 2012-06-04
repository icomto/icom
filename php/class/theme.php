<?php

class theme {
	public static function init(&$tpl) {
		$tpl->THEME_INI = parse_ini_file(THEME_INI_DIRECTORY.'/'.session::$s['theme_ini'].'.ini', true);
		if($tpl->THEME_INI['THEME_VERSION'] < 4 or (session::$s['theme_preset'] and !isset($tpl->THEME_INI['THEME_SETS'][session::$s['theme_preset']]))) {
			session::$s['theme_ini'] = DEFAULT_THEME_INI;
			session::$s['theme_preset'] = DEFAULT_THEME_PRESET;
			unset(session::$s['theme_userset']);
			page_redir(rebuild_location());
			return;
		}
		
		define('THEME_VERSION', $tpl->THEME_INI['THEME_VERSION']);
		define('THEME_STYLE_DIRECTORY', $tpl->THEME_INI['STYLE_DIRECTORY']);
		
		if(session::$s['theme_preset'] == '' and !empty(session::$s['theme_userset']) and is_array(session::$s['theme_userset']->data)) $set = session::$s['theme_userset'];
		else $set = theme_explode_set(@$tpl->THEME_INI['THEME_SETS'][session::$s['theme_preset']]);
		
		if(!$set) {
			session::$s['theme_preset'] = DEFAULT_THEME_PRESET;
			unset(session::$s['theme_userset']);
			$set = theme_explode_set($tpl->THEME_INI['THEME_SETS'][session::$s['theme_preset']]);
		}
		
		function theme_get_classes(&$ini, $name, $set) {
			if(@$ini[$name][$set[$name]]) $classes = $ini[$name][$set[$name]];
			else {
				$temp = $ini;
				reset($temp);
				$classes = current($temp[$name]);
			}
			return 'T-'.implode(' T-', explode(' ', trim($classes)));
		}
		
		$tpl->THEME = array();
		$tpl->THEME['background'] = theme_get_classes($tpl->THEME_INI, 'background', $set);
		$tpl->THEME['mm_style'] = theme_get_classes($tpl->THEME_INI, 'mm-style', $set);
		$tpl->THEME['mm_logo'] = theme_get_classes($tpl->THEME_INI, 'mm-logo', $set);
		$tpl->THEME['mm_icons'] = theme_get_classes($tpl->THEME_INI, 'mm-icons', $set);
		$tpl->THEME['mm_places'] = theme_get_classes($tpl->THEME_INI, 'mm-places', $set);
		$tpl->THEME['side_menu'] = theme_get_classes($tpl->THEME_INI, 'side-menu', $set);
		$tpl->THEME['module'] = theme_get_classes($tpl->THEME_INI, 'module', $set);
		$tpl->THEME['tooltip'] = theme_get_classes($tpl->THEME_INI, 'tooltip', $set);
		
		if(session::$s['theme_preset'] == -1 or (isset($_GET['settings']) and $_GET['settings'] == 'kit')) {
			$vt_id = 'multi';
			$vt = '../themes/_vt/'.session::$s['theme_ini'].'-'.$vt_id;
		}
		else {
			$vt_id = session::$s['theme_preset'];
			$vt = '../themes/_vt/'.session::$s['theme_ini'].'-'.$vt_id;
			if(!file_exists($vt)) {
				$vt_id = 'multi';
				$vt = '../themes/_vt/'.session::$s['theme_ini'].'-'.$vt_id;
			}
		}
		if(file_exists($vt)) $tpl->THEME_INI['THEME_CSS'] = array(THEME_STYLE_DIRECTORY.'/_vt/'.file_get_contents($vt).'-'.$vt_id.'.css');
	}
	
	
	public static function change($baseset, $preset = NULL, $userset = NULL) {
		$theme = trim(str_replace('.', '', str_replace('/', '', str_replace('\\', '', $baseset))));
		if(!file_exists(THEME_INI_DIRECTORY.'/'.$theme.'.ini')) return;
		$ini = parse_ini_file(THEME_INI_DIRECTORY.'/'.$theme.'.ini', true);
		if($ini['THEME_VERSION'] < 4 or !isset($ini['THEME_SETS']) or !is_array($ini['THEME_SETS'])) {
			session::$s['theme_ini'] = DEFAULT_THEME_INI;
			return true;
		}
		session::$s['theme_ini'] = $theme;
		if(!empty($preset)) {
			$preset = trim($preset);
			if(isset($ini['THEME_SETS'][$preset])) {
				session::$s['theme_preset'] = $preset;
				unset(session::$s['theme_userset']);
				return false;
			}
		}
		elseif(isset($userset)) {
			$userset = theme_explode_set(str_replace(',', ' ', $userset));
			function theme_fix_key(&$ini, &$userset, $name) {
				if(!isset($ini[$name][$userset[$name]]) or empty($ini[$name][$userset[$name]])) {
					$keys = array_keys($ini[$name]);
					$userset[$name] = current($keys);
				}
			}
			theme_fix_key($ini, $userset, 'background');
			theme_fix_key($ini, $userset, 'mm-style');
			theme_fix_key($ini, $userset, 'mm-logo');
			theme_fix_key($ini, $userset, 'mm-icons');
			theme_fix_key($ini, $userset, 'side-menu');
			theme_fix_key($ini, $userset, 'module');
			theme_fix_key($ini, $userset, 'tooltip');
			session::$s['theme_preset'] = '';
			session::$s['theme_userset'] = $userset;
			return false;
		}
		return true;
	}
}

?>
