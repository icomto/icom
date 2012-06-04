<?php

/*

{$bla}						- echo
{~$bla}						- htmlspecialchars
{°$bla}						- urlencode
{^$bla}						- urlenc
{%$bla}						- number_format
{%2$bla}					- number_format (max. decimals)

[*text*]					- encode all with htmlspecialchars
[*!text*]					- no htmlspecialchars encoding
[*#text*]					- only text is htmlspecialchars encoded
[**text*]					- only variables are htmlspecialchars encoded

[*text {$bla} text*]		- with variable
[*|en:text*]				- text is in english

{set $bla = 1}							- sets variable to 1
{if $bla == 1}bla{else}blubb{/if}		- if block (elseif is also possible)
{switch $bla}{default}blabla{/default}{case 1}blubb{/case}{/switch}
{foreach $a as $b}bla {$a}{/foreach}	- foreach loop
{while $a == 1}bla {$a}{/while}			- while loop


*/

#require_once '../min/lib/Minify/HTML.php';
#require_once '../min/lib/Minify/CSS.php';
#require_once '../min/lib/JSMin.php';


#$_SERVER['REMOTE_ADDR'] = $argv[1];



trait ilphp_trait {
	use lock_mysql;
	
	public $ilphp_cache_method = 'L2';
	public $ilphp_cache_with_fallback = true;
	
	protected $ilphp_template_dir_home = 'templates/';
	protected $ilphp_template_dir = 'templates/';
	protected $ilphp_compiled_dir = '../templates_c/';
	protected $ilphp_cache_dir = '../templates_cache/';
	
	protected $ilphp_file = '';
	protected $ilphp_file_path = '';
	protected $ilphp_cache_id = '';
	protected $ilphp_cache_time = -1;
	protected $ilphp_sub = '';
	
	public function ilphp_construct($file = '', $cache_time = -1, $cache_id = '', $cache_method = 'L2') {
		$this->this = $this;
		$this->ilphp_cache_method = $cache_method;
		$this->ilphp_cache_id = LANG.'LANG%';
		$this->ilphp_init($file, $cache_time, $cache_id);
	}
	
	public function ilphp_clear() {
		$this->ilphp_file = '';
		$this->ilphp_file_path = '';
		$this->ilphp_cache_id = '';
		$this->ilphp_compiled_file = '';
		$this->ilphp_cache_time = -1;
		$this->ilphp_sub = '';
	}
	public function ilphp_init($file, $cache_time = -1, $cache_id = '', $template_dir = '') {
		if($file) {
			if(preg_match('~^(.+)\|(.+)$~', $file, $out)) {
				$file = $out[1];
				$this->ilphp_sub = $out[2];
			}
			else $this->ilphp_sub = '';
			$this->ilphp_file = $file;
		}
		if($cache_id) $this->ilphp_cache_id = LANG.'-'.$cache_id;
		if($cache_time != -1) $this->ilphp_cache_time = $cache_time;
		if($this->ilphp_file) {
			if(preg_match('~^\~/(.*)$~', $this->ilphp_file, $out)) {
				$template_dir = $this->ilphp_template_dir_home;
				$this->ilphp_file_path = $template_dir.$out[1];
			}
			else {
				$template_dir = $this->ilphp_template_dir;
				$this->ilphp_file_path = $template_dir.$this->ilphp_file;
			}
			
			if(!file_exists($this->ilphp_file_path)) throw new Exception('MISSING TEMPLATE '.$this->ilphp_file_path.' '.@$_SERVER['REQUEST_URI']);
			
			$compiled_base = preg_replace('~.ilp$~', sprintf('.%08x', filemtime($this->ilphp_file_path)), ltrim($this->ilphp_file, '.~/'));
			$this->ilphp_compiled_file = $this->ilphp_compiled_dir.sprintf('%08x.', crc32($template_dir)).$compiled_base.'.php';
			$this->ilphp_cache_file = $this->ilphp_cache_dir.$compiled_base.($this->ilphp_cache_id ? '.'.urlencode($this->ilphp_cache_id) : '').'-'.$this->ilphp_sub.'.ilc';
		}
	}
	
	public function ilphp_assign($a, $b) {
		$this->$a = $b;
	}
	
	public static function _ilphp_compile($_file, $compiled_dir, $_compiled_file) {
		/*$regex = "~^".preg_quote($_file)."\.\d+\.php$/i";
		$dir = opendir($compiled_dir);
		while($d = readdir($dir)) if(preg_match($regex, $d)) unlink($compiled_dir.$d);
		closedir($dir);*/
		//$this->ilphp_cache_delete(true);
		$data = file_get_contents($_file);
		$data = preg_replace("~\{\*.*\*}~sU", "", $data);
		$data = preg_replace("~\{#IMPOST (.*)}~sU", '{ILPHP_CLASS->IMODULE_POST_VAR(\1)}', $data);
		$data = preg_replace("~\{/(while|for|foreach|if|switch)\}~", "<? } ?>", $data);
		$data = preg_replace("~\{/(case|default)\}~", "<?break;?>", $data);
		$data = preg_replace("~\{%(\d*) *([^\}]+) *\}~", "{htmlspecialchars(number_format(\\2,'\\1'?'\\1':0,',','.'))}", $data);
		$data = preg_replace("~\{\~ *([^\}]+) *\}~", "{htmlspecialchars(\\1)}", $data);
		$data = preg_replace("~\{° *([^\}]+) *\}~", "{urlencode(\\1)}", $data);
		$data = preg_replace("~\{\^ *([^\}]+) *\}~", "{urlenc(\\1)}", $data);
		$data = preg_replace("~\{\{~", "{<}", $data);
		$data = preg_replace("~\}\}~", "{>}", $data);
		$data = preg_replace('~\{call ilphp_sub [\'"](.*?)[\'"]\}~', '!%CALL_ILPHP_SUB \1%!', $data);
		$data = preg_replace('~\{new ilphp_sub [\'"](.*?)[\'"]\}~', '!%NEW_ILPHP_SUB_START \1%!', $data);
		$data = preg_replace('~\{/new ilphp_sub\}~', '!%NEW_ILPHP_SUB_END%!', $data);
		$data = preg_replace('~\{define ilphp_sub [\'"](.*?)[\'"]\}~', '!%DEFINE_ILPHP_SUB_START \1%!', $data);
		$data = preg_replace('~\{/define ilphp_sub\}~', '!%DEFINE_ILPHP_SUB_END%!', $data);
		#$data = preg_replace('~\{ilphp_sub [\'"](.*?)[\'"]\}~', '!%ILPHP_SUB_START \1%!', $data);
		#$data = preg_replace('~\{/ilphp_sub\}~', '!%ILPHP_SUB_END%!', $data);
		
		preg_match_all("~\{([^\}]+)\}~iU", $data, $out);
		
		$fn_name = 'ILPHP_'.preg_replace('~[^a-z0-9]~i', '_', $_compiled_file);
		
		//type: 0 = var, 1 = func, 2 = none
		for($i = 0, $c = count($out[1]); $i < $c; $i++) {
			$orig = $out[0][$i];
			$val = $out[1][$i];
			if($val == "<") {
				$data = str_replace($orig, "{", $data);
				continue;
			}
			elseif($val == ">") {
				$data = str_replace($orig, "}", $data);
				continue;
			}
			if(preg_match("~^(if|elseif) ~i", $val)) {
				$val = preg_replace("~ not ~i", " !", $val);
			}
			$val = preg_replace("~^(if|elseif) not ~i", "\\1 !", $val);
			if(preg_match("~^else( do)?$~i", $val)) {
				$val = "}else{";
				$type = 1;
			}
			elseif(preg_match("~do$~i", $val)) {
				$val = preg_replace("~^/(.+)$~", "}", $val)."{";
				$type = 1;
			}
			else {
				while(preg_match_all('~(\\$[a-z_][a-z_\d]*([\[\]\'\\$a-z_\d]+)?){1}\.(\\$?[a-z_\d]+)~i', $val, $o2))
					for($j = 0, $c2 = count($o2[1]); $j < $c2; $j++)
						$val = str_replace($o2[0][$j], $o2[1][$j].(substr($o2[3][$j], 0, 1) == '$' ? "[".$o2[3][$j]."]" : "['".$o2[3][$j]."']"), $val);
				$val = preg_replace("~([^\\\])?\\\$~", "\\1\$ILPHP->", $val);
				$name = "";
				if(preg_match("~ \|([a-z\d_]+)$~i", $val, $o2)) {
					$name = $o2[1];
					$val = preg_replace("~ \|([a-z\d_]+)$~i", "", $val);
				}
				if(preg_match("~^(while|for|foreach|if|elseif|switch) .+$~i", $val, $o2)) {
					$type = 1;
					$val = preg_replace("~^(while|for|foreach|if|elseif|switch) (.+)$~i", "\\1(\\2){", $val);
					if(strtolower($o2[1]) == "elseif") $val = "}".$val;
					elseif($name and strtolower($o2[1]) != "if") {
						$var = "\$ILPHP->".$o2[1]."_".$name;
						$val = "$var=0;".$val."$var++;";
					}
				}
				elseif(preg_match("~^(default)$~i", $val, $o2)) {
					$type = 1;
					$val = preg_replace("~^(default)$~i", "\\1:", $val);
				}
				elseif(preg_match("~^(case) .+$~i", $val, $o2)) {
					$type = 1;
					$val = preg_replace("~^(case) (.+)$~i", "\\1 \\2:", $val);
				}
				elseif(preg_match("~^break$~i", $val, $o2)) {
					$type = 1;
				}
				elseif(preg_match("~^(continue)$~i", $val, $o2)) {
					$type = 1;
					$val = preg_replace("~^(continue)$~i", "\\1", $val);
				}
				elseif(preg_match("~^include ((['\"])?(.*?)(['\"])?)$~i", $val, $o2)) {
					/*if($o2[2] and $o2[4] and strpos($o2[3], '$') === false) {
						print_r($o2);
						$type = 2;
						$val = ilphp::___compile($o2[3], $compiled_dir, $template_dir, $_compiled_file);
						#äecho htmlspecialchars($var);
					}
					else {
						$type = 1;
						$val = "\$ILPHP->ilphp_display(".$o2[1].", -1, \"\", true);";
					}*/
					$type = 1;
					$val = "\$ILPHP->ilphp_display(".$o2[1].", -1, \"\", true);";
				}
				elseif(preg_match("~^include_real ['\"](.+)['\"]$~i", $val, $o2)) {
					$type = 1;
					$val = "include \"".$o2[1]."\";";
				}
				elseif(preg_match("~^set\s+(.+)$~is", $val, $o2)) {
					$type = 1;
					$val = $o2[1];
				}
				elseif(preg_match("~^call ilphp_sub ['\"](.+)['\"]$~i", $val, $o2)) {
					$type = 1;
					$val = $fn_name.'_'.$o2[1].'($ILPHP);';
				}
				else $type = 0;
			}
			switch($type) {
			case 0:
				$data = str_replace($orig, "<?=".$val.";?>", $data);
				break;
			case 1:
				$data = str_replace($orig, "<?".$val.";?>", $data);
				break;
			case 2:
				$data = str_replace($orig, $val, $data);
				break;
			}
		}
		
		$data = preg_replace('~ILPHP_CLASS|#THIS~', '$ILPHP', $data);
		
		if(preg_match_all('~\[\*(.*?)\*\]~s', $data, $out))
			for($i = 0, $num_i = count($out[1]); $i < $num_i; $i++)
				$data = str_replace($out[0][$i], lang::compile_template_string($out[1][$i], $_file), $data);
		
		$data = preg_replace("~\{\?>([\r\n\t]+)<\?=~", "{\\1echo ", $data);
		$data = preg_replace("~\?>([\r\n\t]*)<\?=~", ";\\1echo ", $data);
		
		$data = preg_replace("~\{\?>([\r\n\t]*)<\?~", "{\\1", $data);
		$data = preg_replace("~\?>([\r\n\t]*)<\?\}~", ";\\1}", $data);
		$data = preg_replace("~\?>([\r\n\t]*)<\?~", ";\\1", $data);
		
		$data = str_replace("{?><?=", "{echo ", $data);
		$data = str_replace("?><?=", ";echo ", $data);
		$data = str_replace("?><?}", ";}", $data);
		$data = str_replace("?><?", ";", $data);
		
		$data = str_replace(";;", ";", $data);
		$data = preg_replace("/\n[ \t]+/", "\n ", $data);
		
		/*$data = preg_replace("~\?>(&[^\n\r\t\"']{0,10};)<\?~", ";echo'\\1';", $data);*/
		
		$data = '<?function '.$fn_name.'(&$ILPHP){?>'.$data.'<?}?>';
		if(preg_match_all('~!%NEW_ILPHP_SUB_START (.*?)%!(.*?)!%NEW_ILPHP_SUB_END%!~s', $data, $out)) {
			for($i = 0, $num = count($out[0]); $i < $num; $i++) {
				$name = $fn_name.'_'.$out[1][$i];
				$data = '<?function '.$name.'(&$ILPHP){?>'.$out[2][$i].'<?}?>'.
					str_replace($out[0][$i], '<?'.$name.'($ILPHP)?>', $data);
			}
		}
		if(preg_match_all('~!%DEFINE_ILPHP_SUB_START (.*?)%!(.*?)!%DEFINE_ILPHP_SUB_END%!~s', $data, $out)) {
			for($i = 0, $num = count($out[0]); $i < $num; $i++) {
				$name = $fn_name.'_'.$out[1][$i];
				$data = '<?function '.$name.'(&$ILPHP){?>'.$out[2][$i].'<?}?>'.
					str_replace($out[0][$i], '', $data);
			}
		}
		if(preg_match_all('~!%CALL_ILPHP_SUB (.*?)%!~s', $data, $out)) {
			for($i = 0, $num = count($out[0]); $i < $num; $i++) {
				$name = $fn_name.'_'.$out[1][$i];
				$data = str_replace($out[0][$i], '<?'.$name.'($ILPHP);?>', $data);
			}
		}
		
		$fh = fopen($_compiled_file, "w");
		fwrite($fh, $data);
		fclose($fh);
		
		return $data;
	}
	public function ilphp_compile() {
		ilphp::_ilphp_compile($this->ilphp_file_path, $this->ilphp_compiled_dir, $this->ilphp_compiled_file);
	}
	
	
	public function ilphp_cache_display($file = "", $cache_time = -1, $cache_id = "") {
		return $this->ilphp_cache_load(true, $file, $cache_time, $cache_id);
	}
	public function ilphp_cache_load($display = false, $file = "", $cache_time = -1, $cache_id = "") {
		#return false;
		$this->ilphp_init($file, $cache_time, $cache_id);
		if($this->ilphp_cache_time == -1) return;
		elseif($this->ilphp_cache_method == 'L2') return $this->ilphp_cache_load_L2($display);
		else return $this->ilphp_cache_load_L1($display);
	}
	public function ilphp_cache_load_hdd($display, $retry = 0) {
		if(!file_exists($this->ilphp_cache_file)) {
			$this->lock_set_id($this->ilphp_cache_file);
			$this->lock_set();
			if(file_exists($this->ilphp_cache_file)) {
				$this->lock_release();
				return $this->ilphp_cache_load_hdd($display);
			}
		}
		else {
			$fh = fopen($this->ilphp_cache_file, "r");
			$timeout = trim(fgets($fh));
			if($timeout != 0 and $timeout < time()) {
				fclose($fh);
				@unlink($this->ilphp_cache_file);
				return $this->ilphp_cache_load_hdd($display);
			}
			else {
				$data = "";
				while(!feof($fh)) $data .= fread($fh, 16*1024);
				fclose($fh);
				if($display) {
					echo $data;
					return true;
				}
				else return $data;
			}
		}
	}
	public function ilphp_cache_load_L2($display, $fallback = false) {
		$data = cache_L2::get($this->ilphp_cache_file.($fallback ? '.fallback' : ''));
		if($data !== false) {
			if($display) {
				echo $data;
				return true;
			}
			else return $data;
		}
		elseif($this->ilphp_cache_with_fallback and !$fallback) {
			$this->lock_set_id($this->ilphp_cache_file);
			if($this->lock_is_locked()) return $this->ilphp_cache_load_L2($display, true);
			else $this->lock_set();
		}
		else {
			$this->lock_set_id($this->ilphp_cache_file);
			$this->lock_set();
		}
		return false;
	}
	
	public function ilphp_cache_delete($all = false) {
		if(!$all and $this->ilphp_cache_id) {
			if($this->ilphp_cache_method == 'L2') {
				cache_L2::del($this->ilphp_cache_file);
				if($this->ilphp_cache_with_fallback) cache_L2::del($this->ilphp_cache_file.'.fallback');
			}
			elseif(file_exists($this->ilphp_cache_file)) {
				@unlink($this->ilphp_cache_file);
				if($this->ilphp_cache_with_fallback) @unlink($this->ilphp_cache_file.'.fallback');
			}
		}
		else {
			if($this->ilphp_cache_method == 'L2') {
				if(!$all) {
					cache_L2::del($this->ilphp_cache_file);
					if($this->ilphp_cache_with_fallback) cache_L2::del($this->ilphp_cache_file.'.fallback');
				}
				else {
					db()->query("INSERT IGNORE INTO _errors SET id='".es("T ".@$_SERVER['REMOTE_ADDR']." - ".$this->ilphp_cache_file." - ".rand())."', err='CACHE DELETE TO ALL WITH LEVEL 2 CACHE NOT SUPPORTED!!!!!!'");
				}
			}
			else {
				$dir = opendir($this->ilphp_cache_dir);
				while($d = readdir($dir))
					if($d != '.' and $d != '..' and strpos($d, $this->ilphp_file) === 0)
						@unlink($this->ilphp_cache_dir.$d);
				closedir($dir);
			}
		}
	}
	
	public function ilphp_display($file = '', $cache_time = -1, $cache_id = '', $included = false) {
		return $this->ilphp_fetch($file, $cache_time, $cache_id, true, $included);
	}
	public function ilphp_fetch($file = '', $cache_time = -1, $cache_id = '', $display = false, $included = false) {
		if($included) $_old_file = $this->ilphp_file;
		
		$this->ilphp_init($file, $cache_time, $cache_id);
		
		#if($this->ilphp_cache_time != -1) {
		#	$cache = $this->ilphp_cache_load($display);
		#	if($cache) return $cache;
		#}
		
		if(!file_exists($this->ilphp_compiled_file)) $this->ilphp_compile();
		$fn = 'ILPHP_'.preg_replace('~[^a-z0-9]~i', '_', $this->ilphp_compiled_file).($this->ilphp_sub ? '_'.$this->ilphp_sub : '');
		
		#$this->ilphp_compile();
		include_once $this->ilphp_compiled_file;
		
		/*if(!function_exists($fn)) {
			trigger_error("FUNCTION NOT EXISTS 2222: $fn - ".$this->ilphp_3fc25a9f3d74e8b805dc9a4567bb4cda." / ".$this->ilphp_sub, E_USER_ERROR);
		}*/
		
		if($included or ($display and $this->ilphp_cache_time == -1)) {
			#include $this->ilphp_compiled_file;
			$fn($this);
			if($included) $this->ilphp_init($_old_file);
		} else {
			try {
				ob_start();
				#include $this->ilphp_compiled_file;
				$fn($this);
				$data = ob_get_contents();
				ob_end_clean();
				#$data = Minify_HTML::minify($data, array('cssMinifier' => array('Minify_CSS', 'minify'), 'jsMinifier' => array('JSMin', 'minify')));
			}
			catch(Exception $e) {
				ob_end_clean();
				throw $e;
			}
			
			if($this->ilphp_cache_method == 'hdd') {
				if($this->ilphp_cache_time != -1) {
					$fh = fopen($this->ilphp_cache_file, 'w');
					fwrite($fh, ($this->ilphp_cache_time == 0 ? 0 : time() + $this->ilphp_cache_time)."\n");
					fwrite($fh, $data);
					fflush($fh);
					fclose($fh);
					$this->lock_release();
				}
			}
			else {//L2
				if($this->ilphp_cache_time != -1) {
					cache_L2::set($this->ilphp_cache_file, $this->ilphp_cache_time, $data);
					$this->lock_release();
					if($this->ilphp_cache_with_fallback) {
						cache_L2::set($this->ilphp_cache_file.'.fallback', $this->ilphp_cache_time + 120, $data);
					}
				}
			}
			
			if($display) echo $data;
			else return $data;
		}
	}
}

?>
