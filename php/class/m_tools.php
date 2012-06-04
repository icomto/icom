<?php

class m_tools {
	public static function view_error($message) {
		return self::view_error_ex(htmlspecialchars($message));
	}
	public static function view_error_ex($message) {
		$site = '<div class="module-item">';
		$site .= '<h1>'.LS('Fehler').'</h1>';
		$site .= '<div class="module-content module-misc">';
		$site .= '<p class="error">'.$message.'</p>';
		$site .= '</div><div class="module-footer"></div></div>';
		return $site;
	}
	public static function view_module_box($headline, $content) {
		$site = '<div class="module-item">';
		$site .= '<h1>'.$headline.'</h1>';
		$site .= '<div class="module-content module-misc">';
		$site .= '<center><br><br>'.$content.'<br><br><br></center>';
		$site .= '</div><div class="module-footer"></div></div>';
		return $site;
	}
}

?>