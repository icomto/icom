<?function ILPHP____templates_c_71d4bd6a_radio_php_module_stats_4fcbffeb_php(&$ILPHP){?><?if(!$ILPHP->channel['online']){;?><div class="error"><?=htmlspecialchars(lang::_LS_get_define('e989391e'))?></div>
<?}else{;?>
<table>
 <tr><td style="font-weight:bold;"<?if(strlen($ILPHP->channel['currentsong']) > 24){;?> title="<?=htmlspecialchars($ILPHP->channel['currentsong']);?>"<? } ?>><marquee scrolldelay="150"><?=htmlspecialchars($ILPHP->channel['currentsong']);?></marquee></td></tr>
</table>
<table>
 <tr>
 <td width="108" style="padding-left:2px;">DJ</td>
 <td width="58">
 <?if($ILPHP->channel['current_dj']){;echo user($ILPHP->channel['current_dj'])->html(-1);
 }else{;echo htmlspecialchars(lang::_LS_get_define('07d3b39a'));
 } ?>
 </tr>
 </tr>
 <tr>
 <td style="padding-left:2px;"><?=htmlspecialchars(lang::_LS_get_define('3d942707'))?></td>
 <td><?=htmlspecialchars($ILPHP->channel['listeners']);?> / <?=htmlspecialchars($ILPHP->channel['maxlisteners']);?></tr>
 </tr>
 <tr>
 <td style="padding-left:2px;"><?=htmlspecialchars(lang::_LS_get_define('5afa6f32'))?></td>
 <td><?=htmlspecialchars($ILPHP->channel['peaklisteners']);?></td>
 </tr>
 <tr>
 <td style="padding-left:2px;"><?=htmlspecialchars(lang::_LS_get_define('01dc48ea'))?></td>
 <td><?=htmlspecialchars($ILPHP->channel['bitrate']);?> kbps</td>
 </tr>
</table>
<? } ;

if($ILPHP->channel['infos']){;?>
<table><tr><td><?=ubbcode::add_smileys(ubbcode::compile($ILPHP->channel['infos'],152));?></td></tr></table>
<? } ;

if($ILPHP->channel['admins'] or $ILPHP->channel['guests']){;?>
<table><tr><td>	
 <?if($ILPHP->channel['admins']){;
 if($ILPHP->channel['admins']->num_rows > 1){;echo htmlspecialchars(lang::_LS_get_define('ee2674d8'));}else{;echo htmlspecialchars(lang::_LS_get_define('7bf4bda0')); } ?>: 
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->channel['admins']->fetch_assoc()){$ILPHP->while_i++;;
 echo user($ILPHP->i['user_id'])->html(-1);if($ILPHP->while_i < $ILPHP->channel['admins']->num_rows){;?>, <? } ;
 } ?>
 <br>
 <? } ;
 if($ILPHP->channel['guests']){;
 if($ILPHP->channel['admins']->num_rows > 1){;echo htmlspecialchars(lang::_LS_get_define('0925f11f'));}else{;echo htmlspecialchars(lang::_LS_get_define('2832ec02')); } ?>: 
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->channel['guests']->fetch_assoc()){$ILPHP->while_i++;;
 echo user($ILPHP->i['user_id'])->html(-1);if($ILPHP->while_i < $ILPHP->channel['guests']->num_rows){;?>, <? } ;
 } ?>
 <br>
 <? } ?>
</td></tr></table>
<? } ?>
<?}?>