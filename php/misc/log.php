<?php

require '../init_session.inc.php';

echo '<style type="text/css">.pages a, .pages span { margin:0 4px 0 4px; }</style>';
echo '<pre>';

function gen_url($t = '', $v = '') {
	global $url;
	if($t) {
		$u = $url;
		$u[$t] = $v;
	}
	else $u =& $url;
	$r = array();
	foreach($u as $k=>$v) $r[] = urlencode($k).'='.urlencode($v);
	return '?'.join('&', $r);
}

$filter = array(1);
$url = array('uid'=>'', 'type'=>'');
if(isset($_GET['uid']) and $_GET['uid']) {
	$filter[] = "uid='".es($_GET['uid'])."'";
	$url['uid'] = $_GET['uid'];
	echo '<a href="'.gen_url('uid', '').'">User:</a> '.user(es($_GET['uid']))->html(1).'<br><br>';
}
if(isset($_GET['type']) and $_GET['type']) {
	$filter[] = "type='".es($_GET['type'])."'";
	$url['type'] = $_GET['type'];
	echo '<a href="'.gen_url('type', '').'">Typ:</a> '.htmlspecialchars($_GET['type']).'<br><br>';
}
$page = ((isset($_GET['page']) and (int)$_GET['page'] > 1) ? (int)$_GET['page'] : 1);

if($page == 1) {
	if(!isset($_GET['uid']) or !$_GET['uid']) {
		$users = db()->query("
			SELECT uid, COUNT(*) AS num
			FROM log
			WHERE ".join(" AND ", $filter)." AND NOT type='invite_request'
			GROUP BY uid
			ORDER BY num");
		echo '<table border="1">';
		while($user = $users->fetch_assoc())
			echo '<tr><td>'.user($user['uid'])->html(1).' <a href="'.gen_url('uid', $user['uid']).'">F</a></td><td align="right">'.$user['num'].'</td></tr>';
		echo '</table><br>';
	}
	
	if(!isset($_GET['type']) or !$_GET['type']) {
		$stats = db()->query("
			SELECT type, COUNT(*) AS num
			FROM log
			WHERE ".join(" AND ", $filter)."
			GROUP BY type
			ORDER BY type");
		echo '<table border="1">';
		while($stat = $stats->fetch_assoc()) echo '<tr><td><a href="'.gen_url('type', $stat['type']).'">'.htmlspecialchars($stat['type']).'</a></td><td align="right">'.$stat['num'].'</td></tr>';
		echo '</table><br>';
	}
}

$logs = db()->query("
	SELECT SQL_CALC_FOUND_ROWS *
	FROM log
	WHERE ".join(" AND ", $filter)."
	ORDER BY timeadded DESC
	LIMIT ".(($page - 1)*50).", 50");
$num_pages = calculate_pages(db()->query("SELECT FOUND_ROWS() AS num")->fetch_object()->num, 50);
if($num_pages > 1) echo '<div class="pages">'.create_pages($page, $num_pages - 1, gen_url().'&amp;page=%s').'</div><br><br>';

echo '<table border="1">';
while($log = $logs->fetch_assoc()) {
	$msg = explode(" - ", $log['message']);
	switch($log['type']) {
	default:
		$t = '???<br>'.$log['message'];
		break;
	case 'comment_deleted':
		$t = 'Release: <a href="/release/'.$msg[0].'/" target="_blank">'.htmlspecialchars($msg[0]).'</a><br>';
		$t .= 'ID: '.htmlspecialchars($msg[1]).'<br>';
		$t .= 'User: '.(@$msg[2] ? user($msg[2])->html(1) : 'n/a');
		break;
	case 'comment_offtopic':
		$log['type'] = 'comment_offtopic_deleted';
	case 'comment_offtopic_deleted':
		$t = 'Release: <a href="/release/'.$msg[0].'/" target="_blank">'.htmlspecialchars($msg[0]).'</a><br>';
		$t .= 'ID: '.htmlspecialchars($msg[1]).'<br>';
		$t .= 'User: '.(@$msg[2] ? user($msg[2])->html(1) : 'n/a');
		break;
		
	case 'shoutbox_changed':
		$t = 'ID: '.htmlspecialchars($msg[0]).'<br>';
		$t .= 'User: '.(@$msg[1] ? user($msg[1])->html(1) : 'n/a');
		break;
	case 'shoutbox_deleted':
		$t = 'ID: '.htmlspecialchars($msg[0]).'<br>';
		$t .= 'User: '.(@$msg[1] ? user($msg[1])->html(1) : 'n/a');
		break;
		
	case 'ls_chat_changed':
		$t = 'Stream: <a href="/livestream/'.$msg[0].'/" target="_blank">'.htmlspecialchars($msg[0]).'</a><br>';
		$t .= 'ID: '.htmlspecialchars($msg[1]).'<br>';
		$t .= 'User: '.(@$msg[2] ? user($msg[2])->html(1) : 'n/a');
		break;
	case 'ls_chat_delete':
		$t = 'Stream: <a href="/livestream/'.$msg[0].'/" target="_blank">'.htmlspecialchars($msg[0]).'</a><br>';
		$t .= 'ID: '.htmlspecialchars($msg[1]).'<br>';
		$t .= 'User: '.(@$msg[2] ? user($msg[2])->html(1) : 'n/a');
		break;
		
	case 'invite_request':
		$t = 'ID: '.htmlspecialchars($msg[0]).'<br>';
		$t .= 'Status: '.htmlspecialchars($msg[1]);
		break;
	
	case 'forum_thread_state':
	case 'forum_thread_openclose':
		$t = 'Thread: <a href="/thread/'.$msg[0].'/" target="_blank">'.htmlspecialchars($msg[0]).'</a><br>';
		$t .= 'Status: '.htmlspecialchars($msg[1]).'<br>';
		break;
	case 'forum_thread_move':
		$t = 'Thread: <a href="/thread/'.$msg[0].'/" target="_blank">'.htmlspecialchars($msg[0]).'</a><br>';
		$t .= 'From: <a href="/forum/'.$msg[1].'/" target="_blank">'.htmlspecialchars($msg[1]).'</a><br>';
		$t .= 'To: <a href="/forum/'.$msg[2].'/" target="_blank">'.htmlspecialchars($msg[2]).'</a><br>';
		break;
	case 'forum_thread_delete':
		$t = 'Section: <a href="/forum/'.$msg[0].'/" target="_blank">'.htmlspecialchars($msg[0]).'</a><br>';
		$t .= 'Thread: '.htmlspecialchars($msg[1]).'<br>';
		$t .= 'Posts: '.htmlspecialchars($msg[2]);
		break;
	case 'forum_post_edit':
		$t = 'Thread: <a href="/thread/'.$msg[0].'/" target="_blank">'.htmlspecialchars($msg[0]).'</a><br>';
		$t .= 'ID: '.htmlspecialchars($msg[1]).'<br>';
		$t .= 'User: '.(@$msg[2] ? user($msg[2])->html(1) : 'n/a');
		break;
	case 'forum_post_delete':
		$t = 'Thread: <a href="/thread/'.$msg[0].'/" target="_blank">'.htmlspecialchars($msg[0]).'</a><br>';
		$t .= 'ID: '.htmlspecialchars($msg[1]).'<br>';
		$t .= 'User: '.(@$msg[2] ? user($msg[2])->html(1) : 'n/a');
		break;
		
	case 'warning_created':
		$t = 'ID: '.htmlspecialchars($msg[0]).'<br>';
		$t .= 'User: '.(@$msg[1] ? user($msg[1])->html(1) : 'n/a').'<br>';
		$t .= 'Points: '.htmlspecialchars($msg[2]).'<br>';
		$t .= 'Days: '.htmlspecialchars($msg[3]);
		break;
	case 'warning_deleted':
		$t = 'ID: '.htmlspecialchars($msg[0]).'<br>';
		$t .= 'User: '.(@$msg[1] ? user($msg[1])->html(1) : 'n/a');
		break;
	}
	echo '
		<tr>
			<td valign="top">'.timeago($log['timeadded']).'</td>
			<td valign="top">'.user($log['uid'])->html(1).' <a href="'.gen_url('uid', $log['uid']).'">F</a></td>
			<td valign="top"><a href="'.gen_url('type', $log['type']).'">'.htmlspecialchars($log['type']).'</a></td>
			<td valign="top">'.$t.'</td>
		</tr>';
}
echo '</table></pre>';


?>
