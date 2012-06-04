<?php

define('ADMIN_REPORT_PAGE_STEP', 10);

class m_admin_report_page extends im_tabs {
	use ilphp_trait;
	use im_pages;
	
	public function __construct() {
		parent::__construct(__DIR__);
	}
	protected function INIT(&$args) {
		if(!has_privilege('report_page')) throw new iexception('ACCESS_DENIED', $this);
		
		$this->url = '/'.LANG.'/admin/report_page/';
		$this->way[] = [LS('Admin'), ''];
		$this->way[] = [LS('Gemeldete Seiten'), $this->url];
		
		$this->im_tabs_add('open', LS('Offene'), TAB_SELF);
		$this->im_tabs_add('accepted', LS('Angenommene'), TAB_SELF);
		$this->im_tabs_add('rejected', LS('Abgelehnte'), TAB_SELF);
		parent::INIT($args);
	}
	
	protected function TAB_open(&$args) {
		return $this->view_status('open', 'open');
	}
	public function TAB_open_POST_save(&$args) {
		$report_id = (int)@$args['report_id'];
		if(!$report_id) return 'ERROR';
		$report = db()->query("SELECT * FROM report_page WHERE report_id='".$report_id."' LIMIT 1")->fetch_assoc();
		if(!$report or $report['status'] != 'open') return 'ERROR';
		
		$update = array();
		$update['admin_id'] = USER_ID;
		$update['status'] = @$args['status'];
		$update['comment'] = trim(@$args['comment']);
		
		switch($update['status']) {
		default:
			return 'ERROR';
		case 'accepted':
			imail::mail($report['user_id'] ? user($report['user_id'])->email : $report['email'],
				"iCom Ticket [{$report_id}] - Seite gemeldet",
				"Hallo,\r\n".
				"\r\n".
				"Deine Meldung die Du unter http://".SITE_DOMAIN."/settings/tickets/ticket_id/{$report_id}/".($report['user_id'] ? "" : "pw/".$report['password']."/")." einsehen kannst wurde erfolgreich bearbeitet.\r\n".
				"\r\n".
				"Sollte trotzdem noch etwas unkorrekt sein schreibe bitte, mit angabe des oben stehenden Links, ein neues Ticket.\r\n".
				"\r\n".
				($update['comment'] ? "Kommentar des Bearbeiters:\r\n".$update['comment']."\r\n\r\n" : "").
				"Vielen Dank\r\n".
				"Dein iCom.to-Team",
				"From: iCom.to <".NOREPLY_EMAIL.">\n");
			db()->query("UPDATE report_page SET ".implode(',', hash_to_sql($update)).", edit_time=CURRENT_TIMESTAMP WHERE report_id='$report_id' LIMIT 1");
			return IS_AJAX ? LS('<p class="success">Angenommen</p>') : true;
		case 'rejected':
			imail::mail($report['user_id'] ? user($report['user_id'])->email : $report['email'],
				"iCom Ticket [{$report_id}] - Seite gemeldet",
				"Hallo,\r\n".
				"\r\n".
				"Deine Meldung die Du unter http://".SITE_DOMAIN."/settings/tickets/ticket_id/{$report_id}/".($report['user_id'] ? "" : "pw/".$report['password']."/")." einsehen kannst wurde abgelehnt.\r\n".
				"\r\n".
				"Solltest Du damit nicht einverstanden sein eroeffne bitte ein neues Ticket mit Angabe des oben stehenden Links.\r\n".
				"\r\n".
				($update['comment'] ? "Kommentar des Bearbeiters:\r\n".$update['comment']."\r\n\r\n" : "").
				"Vielen Dank\r\n".
				"Dein iCom.to-Team",
				"From: iCom.to <".NOREPLY_EMAIL.">\n");
			db()->query("UPDATE report_page SET ".implode(',', hash_to_sql($update)).", edit_time=CURRENT_TIMESTAMP WHERE report_id='$report_id' LIMIT 1");
			return IS_AJAX ? LS('<p class="error">Abgelehnt</p>') : true;
		}
	}
	
	protected function TAB_accepted() {
		return $this->view_status('accepted', 'closed');
	}
	protected function TAB_rejected() {
		return $this->view_status('rejected', 'closed');
	}
	protected function view_status($status, $template = 'closed') {
		$this->page = (int)@$_GET['page'];
		if($this->page <= 0) $this->page = 1;
		$this->tickets = db()->query("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM report_page
			WHERE status='$status'
			ORDER BY t DESC
			LIMIT ".(($this->page - 1) * ADMIN_REPORT_PAGE_STEP).", ".ADMIN_REPORT_PAGE_STEP);
		$this->num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, ADMIN_REPORT_PAGE_STEP);
		return $this->ilphp_fetch('report_page.php.'.$template.'.ilp');
	}
}

?>
