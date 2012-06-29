<?php

require_once '../config.inc.php';

/*
CREATE TABLE `ipcounter` (
  `ip` int(10) unsigned NOT NULL,
  `hits` bigint(20) unsigned NOT NULL DEFAULT '1',
  `all_hits` bigint(20) unsigned NOT NULL DEFAULT '1',
  `firsttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lasttime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `time_on_site` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ip`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

#full page reload
db()->query("INSERT INTO ipcounter SET ip='".ip2long($_SERVER['REMOTE_ADDR'])."', lasttime=CURRENT_TIMESTAMP ON DUPLICATE KEY UPDATE hits=hits+1, all_hits=all_hits+1, time_on_site=time_on_site+IF(lasttime>SUBTIME(CURRENT_TIMESTAMP,'00:02:05.00000'),UNIX_TIMESTAMP(CURRENT_TIMESTAMP)-UNIX_TIMESTAMP(lasttime),0), lasttime=CURRENT_TIMESTAMP");

#page idle
db()->query("INSERT INTO ipcounter SET ip='".ip2long($_SERVER['REMOTE_ADDR'])."', lasttime=CURRENT_TIMESTAMP ON DUPLICATE KEY UPDATE all_hits=all_hits+1, time_on_site=time_on_site+IF(lasttime>SUBTIME(CURRENT_TIMESTAMP,'00:02:05.00000'),UNIX_TIMESTAMP(CURRENT_TIMESTAMP)-UNIX_TIMESTAMP(lasttime),0), lasttime=CURRENT_TIMESTAMP");

*/

function content() {
	$online = db()->query("SELECT COUNT(*) AS num FROM ipcounter WHERE lasttime>SUBTIME(CURRENT_TIMESTAMP,'00:12:00.00000')")->fetch_object()->num;
	$hit_eq_1 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits=1")->fetch_object();
	$hit_eq_2 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits=2")->fetch_object();
	$hit_eq_3 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits=3")->fetch_object();
	$hit_eq_4 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits=4")->fetch_object();
	$hit_eq_5 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits=5")->fetch_object();
	$hit_le_10 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits>5 AND hits<11")->fetch_object();
	$hit_le_20 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits>10 AND hits<21")->fetch_object();
	$hit_le_30 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits>20 AND hits<31")->fetch_object();
	$hit_le_40 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits>30 AND hits<41")->fetch_object();
	$hit_le_50 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits>40 AND hits<51")->fetch_object();
	$hit_le_100 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits>50 AND hits<101")->fetch_object();
	$hit_gt_100 = db()->query("SELECT COUNT(*) AS num, SUM(time_on_site) AS time_on_site FROM ipcounter WHERE hits>100")->fetch_object();
	$today = db()->query("SELECT COUNT(*) AS num, MAX(hits) AS best_num, SUM(hits) AS hits, SUM(all_hits) AS all_hits, SUM(time_on_site) AS time_on_site FROM ipcounter")->fetch_object();
	$time = db()->query("SELECT UNIX_TIMESTAMP(CURRENT_TIMESTAMP) AS current, UNIX_TIMESTAMP(MIN(firsttime)) AS first FROM ipcounter")->fetch_object();
	$day = strtotime(date("Y-m-d"));
	echo "\n".date("Y-m-d H:i:s")."\n\n";
	?><table align="center">
		<tr><td width="160">Online: </td><td align="right"><?=@number_format($online, 0, ",", ".");?></td><td width="85">&nbsp;</td><td width="85">&nbsp;</td></tr>
		<tr><td>Users: </td><td align="right"><?=@number_format($today->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round($today->num/($time->current-$time->first), 2), 2, ",", ".");?>/sec</td><td>&nbsp;</td></tr>
		<tr><td>Calculated users: </td><td align="right"><?=@number_format(($today->num/($time->current-$time->first))*60*60*24, 0, ",", ".");?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
		<tr><td>Pageviews: </td><td align="right"><?=@number_format($today->hits, 0, ",", ".");?></td><td align="right"><?=@number_format(round($today->hits/($time->current-$time->first), 2), 2, ",", ".");?>/sec</td><td>&nbsp;</td></tr>
		<tr><td>All hits: </td><td align="right"><?=@number_format($today->all_hits, 0, ",", ".");?></td><td align="right"><?=@number_format(round($today->all_hits/($time->current-$time->first), 2), 2, ",", ".");?>/sec</td><td>&nbsp;</td></tr>
		<tr><td>PI: </td><td align="right"><?=@number_format(round($today->hits/$today->num, 2), 2, ",", ".");?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
		<!-- <tr><td>Best PI: </td><td align="right"><?=@number_format($today->best_num, 0, ",", ".");?></td><td>&nbsp;</td><td>&nbsp;</td></tr> -->
		<tr><td>Time (min): </td><td align="right"><?=@number_format(round(($today->time_on_site/$today->num)/60, 2), 2, ",", ".");?></td><td>&nbsp;</td><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		<tr><th align="left">PI</th><th align="right">Users</th><th align="right">%</th><th align="right">Time</th></tr>
		<tr><td>Bounce</td><td align="right"><?=@number_format($hit_eq_1->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_eq_1->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_eq_1->time_on_site/$hit_eq_1->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>== 2</td><td align="right"><?=@number_format($hit_eq_2->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_eq_2->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_eq_2->time_on_site/$hit_eq_2->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>== 3</td><td align="right"><?=@number_format($hit_eq_3->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_eq_3->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_eq_3->time_on_site/$hit_eq_3->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>== 4</td><td align="right"><?=@number_format($hit_eq_4->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_eq_4->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_eq_4->time_on_site/$hit_eq_4->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>== 5</td><td align="right"><?=@number_format($hit_eq_5->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_eq_5->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_eq_5->time_on_site/$hit_eq_5->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>&lt;= 10</td><td align="right"><?=@number_format($hit_le_10->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_le_10->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_le_10->time_on_site/$hit_le_10->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>&lt;= 20</td><td align="right"><?=@number_format($hit_le_20->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_le_20->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_le_20->time_on_site/$hit_le_20->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>&lt;= 30</td><td align="right"><?=@number_format($hit_le_30->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_le_30->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_le_30->time_on_site/$hit_le_30->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>&lt;= 40</td><td align="right"><?=@number_format($hit_le_40->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_le_40->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_le_40->time_on_site/$hit_le_40->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>&lt;= 50</td><td align="right"><?=@number_format($hit_le_50->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_le_50->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_le_50->time_on_site/$hit_le_50->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>&lt;= 100</td><td align="right"><?=@number_format($hit_le_100->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_le_100->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_le_100->time_on_site/$hit_le_100->num)/60, 2), 2, ",", ".");?></td></tr>
		<tr><td>&gt;= 100</td><td align="right"><?=@number_format($hit_gt_100->num, 0, ",", ".");?></td><td align="right"><?=@number_format(round(($hit_gt_100->num/$today->num)*100, 2), 2, ",", ".");?>%</td><td align="right"><?=@number_format(round(($hit_gt_100->time_on_site/$hit_gt_100->num)/60, 2), 2, ",", ".");?></td></tr>
	</table>
	<?php 
	if(true or isset($_GET['ips'])) {
		$ips = db()->query("SELECT * FROM ipcounter ORDER BY hits DESC LIMIT 10");
		echo '<table align="center">';
		while($ip = $ips->fetch_object()) {
			$ip->ip = long2ip($ip->ip);
			echo '<tr><td width="160"><a href="http://whois.domaintools.com/'.$ip->ip.'" target="_blank">W</a> <a href="http://'.$ip->ip.'/" target="_blank">'.$ip->ip.'</a></td><td align="right" width="85">'.@number_format($ip->hits, 0, ",", ".").'</td><td align="center" width="200">'.$ip->lasttime.'</td><td><a href="?delete='.ip2long($ip->ip).'">Delete</a></tr>';
		}
		echo '</table>';
	}
}

if(isset($_GET['ajax'])) die(content());
if(isset($_GET['delete'])) db()->query("DELETE FROM ipcounter WHERE ip='".db()->escape_string($_GET['delete'])."' LIMIT 1");

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta HTTP-EQUIV="Content-Type" Content="text/html; charset=utf-8">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
	</head>
	<body>
		<pre style="text-align:center;">
			<?=content();?>
		</pre>
		<script type="text/javascript">
			setInterval(function(){$.ajax({type:'GET',url:'?ajax=1',dataType:'html',async:true,success:function(d){$('pre').html(d);}})},500);
		</script>
	</body>
</html>
