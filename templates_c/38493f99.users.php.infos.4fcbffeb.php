<?function ILPHP____templates_c_38493f99_users_php_infos_4fcbffeb_php(&$ILPHP){?><div class="module-user-infos">
 <?if(!IS_LOGGED_IN){;?><p class="info"><?=htmlspecialchars(lang::_LS_get_define('5dbc522a'))?></p>
 <?}elseif(!$ILPHP->user){;?><p class="error"><?=htmlspecialchars(lang::_LS_get_define('b1bbc6d9'))?></p>
 <?}else{;
 
 if($ILPHP->errors){;
 foreach($ILPHP->errors as $ILPHP->error){;?>
 <p class="error">
 <?switch($ILPHP->error){;
 default:;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('be1030f4'),$ILPHP->error));break;
 case 'FRIENDSHIP_ALREADY_EXISTS':;echo htmlspecialchars(lang::_LS_get_define('c25ec1bb'));break;
 case 'FRIENDSHIP_REQUEST_ALREADY_SENT':;echo htmlspecialchars(lang::_LS_get_define('a85bc5be'));break;
 case 'FIRENDSHIP_CANCELED':;echo htmlspecialchars(lang::_LS_get_define('29857fd2'));break;
 case 'FRIENDSHIP_NOT_RECEIVED':;echo htmlspecialchars(lang::_LS_get_define('a330ef05'));break;
 case 'FRIENDSHIP_REJECTED':;echo htmlspecialchars(lang::_LS_get_define('01c831c6'));break;
 case 'FRIENDSHIP_ENDED':;echo htmlspecialchars(lang::_LS_get_define('1acc85f7'));break;
 case 'TRUST_LIMIT_REACHED':;echo htmlspecialchars(lang::_LS_get_define('3a088159'));break;
 case 'NOT_ENOUGH_WARNING_POINTS':;echo htmlspecialchars(lang::_LS_get_define('43dcd8d9'));break;
 case 'WARNING_NOT_FOUND':;echo htmlspecialchars(lang::_LS_get_define('5f3986cc'));break;
 } ?>
 </p>
 <? } ;
 } ;
 
 if($ILPHP->user['avatar']){;?>
 <div class="user-avatar" width="<?=htmlspecialchars(AVATAR_MAX_WIDTH + 10);?>">
 <img src="<?=htmlspecialchars(get_avatar_url($ILPHP->user['avatar']));?>" alt="">
 </div>
 <? } ?>
 
 <style>
 .T-base-white table.user-infos tr {
 border-bottom:1px #ccc solid;
 }
 .T-base-black table.user-infos tr {
 border-bottom:1px #333 solid;
 }
 </style>
 <table class="user-infos">
 <tr>
 <th width="150"><?=htmlspecialchars(lang::_LS_get_define('b62623c4'))?></th>
 <td><a href="/<?=LANG;?>/users/<?=$ILPHP->user['user_id'];?>-<?=urlenc($ILPHP->user['nick']);?>/"><?=htmlspecialchars($ILPHP->user['nick']);?></a></td>
 </tr>
 <tr>
 <th><?=htmlspecialchars(lang::_LS_get_define('86c1865b'))?></th>
 <td>(<a href="/<?=LANG;?>/wiki/Jabber_Server/" class="info">Infos</a>) <a href="xmpp:<?=$ILPHP->user['nick_jabber'];?>@jabber.icom.to/icom" target="_blank"><?=htmlspecialchars($ILPHP->user['nick_jabber']);?>@jabber.icom.to</a></td>
 </tr>
 <tr>
 <th><?=htmlspecialchars(lang::_LS_get_define('2ff6fdd3'))?></th>
 <td><?=user($ILPHP->user['user_id'])->html_groups(0, ', ', array(), true);?></td>
 </tr>
 <tr>
 <th><?=htmlspecialchars(lang::_LS_get_define('30aca9c0'))?></th>
 <td>
 <?foreach($ILPHP->user['languages'] as $ILPHP->lang){;?>
 <img src="<?=STATIC_CONTENT_DOMAIN;?>/img/countryflags/<?=htmlspecialchars($ILPHP->lang);?>.gif" alt="<?=htmlspecialchars($ILPHP->lang);?>" title="<?if($ILPHP->lang == 'de'){;echo htmlspecialchars(lang::_LS_get_define('e650da0f'));}else{;echo htmlspecialchars(lang::_LS_get_define('7b83c6b1')); } ?>">
 <? } ?>
 </td>
 </tr>
 <?$ILPHP->rank = user($ILPHP->user['user_id'])->html_rank();?>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('d1e95416'))?></th><td><span style="<?=htmlspecialchars($ILPHP->rank['css']);?>"><?=htmlspecialchars($ILPHP->rank['de']);?></span></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('2b0d9634'))?></th><td><?=timeago($ILPHP->user['regtime']);?></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('8e982b36'))?></th><td><?if(!$ILPHP->user['lastvisit']){;echo htmlspecialchars(lang::_LS_get_define('bfc65bb8'));}else{;echo timeago($ILPHP->user['lastvisit']); } ?></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('c4646efe'))?></th><td><?if(!$ILPHP->user['lastaction']){;echo htmlspecialchars(lang::_LS_get_define('bfc65bb8'));}else{;echo timeago($ILPHP->user['lastaction']); } ?></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('0edbf2fe'))?></th><td><span title="Seit 29. Oktober 2009 17:30"><?if($ILPHP->user['time_on_page'] > 60*60){;echo htmlspecialchars(number_format($ILPHP->user['time_on_page']/(60*60),''?'':0,',','.'));?> <?=htmlspecialchars(lang::_LS_get_define('c5c2350b'));}elseif($ILPHP->user['time_on_page'] > 60){;echo htmlspecialchars(number_format($ILPHP->user['time_on_page']/60,''?'':0,',','.'));?> <?=htmlspecialchars(lang::_LS_get_define('a0bcf9fd'));}else{;echo htmlspecialchars(number_format($ILPHP->user['time_on_page'],''?'':0,',','.'));?> <?=htmlspecialchars(lang::_LS_get_define('e5aabea5')); } ?></span></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('95174274'))?></th><td><?=htmlspecialchars(number_format($ILPHP->user['profile_views'],''?'':0,',','.'));?></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('6b6f1808'))?></th><td><?=htmlspecialchars(number_format($ILPHP->user['points'],''?'':0,',','.'));?></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('95102319'))?></th><td<?if($ILPHP->user['open_warnings']){;?> class="error"<? } ?>><?=htmlspecialchars(number_format($ILPHP->user['open_warnings'],''?'':0,',','.'));?> / <?=htmlspecialchars(number_format(MAX_WARNING_POINTS,''?'':0,',','.'));?></td></tr>
 <tr>
 <th class="user-thrusted-by" colspan="2">
 <?if($ILPHP->user['trusted_by']){;
 echo htmlspecialchars(lang::_LS_get_define('dde18015'))?><br>
 <?$ILPHP->num = count($ILPHP->user['trusted_by']);
 $ILPHP->foreach_u=0;foreach($ILPHP->user['trusted_by'] as $ILPHP->u){$ILPHP->foreach_u++;;if($ILPHP->visitor_user_id == $ILPHP->user['user_id']){;?><div style="display:inline;" onmouseover="$(this).children('.tufrm').css('display','inline');" onmouseout="$(this).children('.tufrm').css('display','none');"><?=user($ILPHP->u)->html(1, '');?><form class="tufrm" method="post" style="width:15px;height:15px;display:none;" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');"><input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_thrusted_user"><input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('user_id');?>" value="<?=$ILPHP->u;?>"><button type="submit" title="<?=htmlspecialchars(lang::_LS_get_define('5533445e'))?>" class="button" style="display:inline;">X</button></form></div><?}else{;echo user($ILPHP->u)->html(1); } ;if($ILPHP->foreach_u < $ILPHP->num){;?>, <? } ; } ;
 }else{;?>
 <p class="info">
 <?=lang::_handle_template_string(lang::_LS_get_define('bd85fe33'),htmlspecialchars($ILPHP->user['nick']))?>
 </p>
 <? } ?>
 </th>
 </tr>
 </table>
 
 <div class="user-controls">
 <?if($ILPHP->visitor_user_id and $ILPHP->visitor_user_id != $ILPHP->user['user_id']){;?>
 <a href="/<?=LANG;?>/pn_new/<?=$ILPHP->user['user_id'];?>-<?=urlenc($ILPHP->user['nick']);?>/" class="button"><?=htmlspecialchars(lang::_LS_get_define('6401e4b4'))?></a>
 <? } ;
 
 if($ILPHP->friend_status == ''){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="frendship_request">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('b9250d14'))?></button>
 </form>
 <?}elseif($ILPHP->friend_status == 'request_sent'){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="frendship_cancel">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5638a6a1'))?></button>
 </form>
 <?}elseif($ILPHP->friend_status == 'request_received'){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="frendship_accept">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('8b48d078'))?></button>
 </form>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="frendship_ignore">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('df3d3349'))?></button>
 </form>
 <?}elseif($ILPHP->friend_status == 'accepted'){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="frendship_end">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('1bb69bbd'))?></button>
 </form>
 <? } ;
 
 if($ILPHP->visitor_user_id and $ILPHP->visitor_user_id != $ILPHP->user['user_id']){;
 if(in_array($ILPHP->visitor_user_id, $ILPHP->user['trusted_by'])){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_thrusted_user">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('user_id');?>" value="<?=USER_ID;?>">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('e466a1a3'))?></button>
 </form>
 <?}elseif(count($ILPHP->user['trusted_by']) < 5){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_thrusted_user">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('0d47581e'))?></button>
 </form>
 <? } ;
 } ;
 
 if(has_privilege('community_master')){;?>
 <a href="/<?=LANG;?>/admin/warnings/<?=$ILPHP->user['user_id'];?>-<?=urlenc($ILPHP->user['nick']);?>/" class="button"><?=htmlspecialchars(lang::_LS_get_define('feb00a0c'))?></a>
 <? } ;
 
 if(in_array(LEVEL2_GROUPID, $ILPHP->allowed_groups_to_change) and (!in_array(LEVEL2_HIDDEN_GROUPID, $ILPHP->group_ids) or $ILPHP->user['points'] < LEVEL2_POINTS)){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" value="<?=LEVEL2_GROUPID;?>">
 <?if(!in_array(LEVEL2_HIDDEN_GROUPID, $ILPHP->group_ids)){;?>
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_group">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('f782437b'))?></button>
 <?}else{;?>
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_group">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('36103fec'))?></button>
 <? } ?>
 </form>
 <? } ;
 
 if(in_array(GUEST_DJ_GROUPID, $ILPHP->allowed_groups_to_change)){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" value="<?=GUEST_DJ_GROUPID;?>">
 <?if(!in_array(GUEST_DJ_GROUPID, $ILPHP->group_ids)){;?>
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_group">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('e0505f92'))?></button>
 <?}else{;?>
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_group">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('1302f799'))?></button>
 <? } ?>
 </form>
 <? } ;
 
 if(in_array(BIRTHDAY_GROUPID, $ILPHP->allowed_groups_to_change)){;?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" value="<?=BIRTHDAY_GROUPID;?>">
 <?if(!in_array(BIRTHDAY_GROUPID, $ILPHP->group_ids)){;?>
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_group">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('7a64a88e'))?></button>
 <?}else{;?>
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_group">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('394c470e'))?></button>
 <? } ?>
 </form>
 <? } ?>
 </div>
 <div class="clear"></div>
 
 
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('1d8a3da0'))?></h3><div></div></div></div>
 <table class="user-infos">
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('61bfba62'))?></th><td><?=htmlspecialchars(number_format($ILPHP->forum_threads,''?'':0,',','.'));?></td><td><a href="/<?=LANG;?>/activities/<?=$ILPHP->user['user_id'];?>-<?=urlenc($ILPHP->user['nick']);?>/upat/threads/" class="info">anzeigen</a></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('afa84ddb'))?></th><td><?=htmlspecialchars(number_format($ILPHP->forum_posts,''?'':0,',','.'));?></td><td><a href="/<?=LANG;?>/activities/<?=$ILPHP->user['user_id'];?>-<?=urlenc($ILPHP->user['nick']);?>/upat/posts/" class="info">anzeigen</a></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('4a78bb04'))?></th><td><?=htmlspecialchars(number_format($ILPHP->wiki_stats['articles'],''?'':0,',','.'));?></td><td></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('1a4ab150'))?></th><td><?=htmlspecialchars(number_format($ILPHP->wiki_stats['changes'],''?'':0,',','.'));?></td><td><a href="/<?=LANG;?>/activities/<?=$ILPHP->user['user_id'];?>-<?=urlenc($ILPHP->user['nick']);?>/upat/wiki/" class="info">anzeigen</a></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('b6b2fdac'))?></th><td><?=htmlspecialchars(number_format($ILPHP->shouts,''?'':0,',','.'));?></td></tr>
 <tr><th>&nbsp;</th><td></td><td></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('5246407e'))?></th><td><?=htmlspecialchars(number_format($ILPHP->bookmarks,''?'':0,',','.'));?></td><td><?if(user()->has_priv($ILPHP->user['priv_bookmarks'], user($ILPHP->user['user_id']))){;?><a href="/<?=LANG;?>/users/<?=$ILPHP->user['user_id'];?>-<?=urlenc($ILPHP->user['nick']);?>/profile/bookmarks/" class="info">anzeigen</a><? } ?></td></tr>
 <tr><th>&nbsp;</th><td></td><td></td></tr>
 <tr><th><?=htmlspecialchars(lang::_LS_get_define('df63c126'))?></th><td><span class="success" title="Angenommene Meldungen"><?=htmlspecialchars(number_format($ILPHP->forum_reported_posts['good'],''?'':0,',','.'));?></span> / <span class="error" title="Abgelehnte Meldungen"><?=htmlspecialchars(number_format($ILPHP->forum_reported_posts['bad'],''?'':0,',','.'));?></span></td><td></td></tr>
 </table>
 <div class="clear"></div>
 
 <?if(has_privilege('forum_super_mod')){;?>
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('4a923dfc'))?></h3><div></div></div></div>
 <div class="user-notes">
 <?while($ILPHP->i = $ILPHP->notes->fetch_assoc()){;?>
 <table class="user-message">
 <tr>
 <td class="user-message-infos">
 <?=lang::_handle_template_string(htmlspecialchars(lang::_LS_get_define('d767ddcc')),user($ILPHP->i['writer_id'])->html(-1))?><br>
 <?=timeago($ILPHP->i['timeadded']);?>
 </td>
 <td>
 <?if(USER_ID == $ILPHP->i['writer_id'] or has_privilege('forum_super_mod')){;?>
 <div class="user-message-controls user-entry">
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.user-message');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_note">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('note_id');?>" value="<?=$ILPHP->i['id'];?>">
 <button class="button">X</button>
 </form>
 </div>
 <? } ;
 echo ubbcode::add_smileys(ubbcode::compile($ILPHP->i['message'], 622));?>
 </td>
 </tr>
 </table>
 <? } ?>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_note">
 <div><textarea class="bbcodeedit" name="<?=$ILPHP->IMODULE_POST_VAR('message');?>" style="height:80px;"></textarea></div>
 <button type="submit" class="big-button lonely-button"><?=htmlspecialchars(lang::_LS_get_define('bf712da5'))?></button>
 </form>
 </div>
 <? } ;
 
 if($ILPHP->warnings->num_rows){;?>
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('bbd1049b'))?></h3><div></div></div></div>
 <div class="user-warnings">
 <?$ILPHP->has_ended_warnings = false;
 while($ILPHP->i = $ILPHP->warnings->fetch_assoc()){;?>
 <table class="user-message<?if($ILPHP->i['ended']){;?> user-warning-ended<?$ILPHP->has_ended_warnings = true; } ?>">
 <tr>
 <td class="user-message-infos">
 <?=lang::_handle_template_string(htmlspecialchars(lang::_LS_get_define('d767ddcc')),user($ILPHP->i['warner_id'])->html(-1))?><br>
 <?=timeago($ILPHP->i['timeadded']);?><br>
 <?=htmlspecialchars(number_format($ILPHP->i['points'],''?'':0,',','.'));?> Punkt<?if($ILPHP->i['points'] != 1){;?>e<? } ?><br>
 <?if($ILPHP->i['ended']){;echo htmlspecialchars(lang::_LS_get_define('8671bbc8'));}else{;echo htmlspecialchars(lang::_LS_get_define('55cc9a75')); } ?> <?if(!$ILPHP->i['timeending'] or $ILPHP->i['timeending'] == '0000-00-00 00:00:00'){;echo htmlspecialchars(lang::_LS_get_define('20bf679e'));}else{;echo timeago($ILPHP->i['timeending'], $ILPHP->i['ended']); } ?><br>
 </td>
 <td>
 <?if(has_privilege('user_warnings')){;?>
 <div class="user-message-controls user-entry">
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.user-message');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_warning">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('warning_id');?>" value="<?=$ILPHP->i['warning_id'];?>">
 <button type="submit" class="button">X</button>
 </form>
 </div>
 <? } ;
 echo ubbcode::add_smileys(ubbcode::compile($ILPHP->i['reason'], 622));?>
 </td>
 </tr>
 </table>
 <? } ;
 if($ILPHP->has_ended_warnings){;?>
 <button type="button" class="big-button lonely-button" onclick="$('.user-warning-ended').show();$(this).hide();"><?=htmlspecialchars(lang::_LS_get_define('db144c80'))?></button>
 <? } ?>
 </div>
 <? } ;
 
 if($ILPHP->denied_entrances->num_rows){;?>
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('c3ea42dd'))?></h3><div></div></div></div>
 <?$ILPHP->has_ended_denied_entrances = false;
 while($ILPHP->i = $ILPHP->denied_entrances->fetch_assoc()){;?>
 <div class="denied-entrances<?if($ILPHP->i['ended']){;?> user-denied-entrance-ended<?$ILPHP->has_ended_denied_entrances = true; } ?>">
 <table class="user-message">
 <tr>
 <td class="user-message-infos">
 <?=lang::_handle_template_string(htmlspecialchars(lang::_LS_get_define('d767ddcc')),user($ILPHP->i['mod_id'])->html(-1))?><br>
 <?=timeago($ILPHP->i['timeadded']);?><br>
 <?if($ILPHP->i['ended']){;echo htmlspecialchars(lang::_LS_get_define('8671bbc8'));}else{;echo htmlspecialchars(lang::_LS_get_define('55cc9a75')); } ?> <?if(!$ILPHP->i['timeending'] or $ILPHP->i['timeending'] == '0000-00-00 00:00:00'){;echo htmlspecialchars(lang::_LS_get_define('20bf679e'));}else{;echo timeago($ILPHP->i['timeending'], $ILPHP->i['ended']); } ?>
 </td>
 <td>
 <?if(has_privilege('shoutboxmaster')){;?>
 <div class="user-message-controls user-entry">
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.denied-entrances');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_denied_entrance">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('denied_entrance_id');?>" value="<?=$ILPHP->i['id'];?>">
 <button type="submit" class="button">X</button>
 </form>
 </div>
 <? } ?>
 <p class="user-message-topic">
 <?switch($ILPHP->i['place']){;
 default:;echo htmlspecialchars(lang::_LS_get_define('7faeb1b1'));break;
 case 'shoutbox':;echo htmlspecialchars(lang::_LS_get_define('5de5733e'));break;
 case 'chat':;echo htmlspecialchars(lang::_LS_get_define('7630a6ce'));break;
 } ?>
 </p>
 <?=ubbcode::add_smileys(ubbcode::compile($ILPHP->i['reason'], 622));?>
 </td>
 </tr>
 </table>
 <br>
 </div>
 <? } ;
 if($ILPHP->has_ended_denied_entrances){;?>
 <button type="button" class="big-button lonely-button" onclick="$('.user-denied-entrance-ended').show();$(this).hide();"><?=htmlspecialchars(lang::_LS_get_define('bf3d2457'))?></button>
 <? } ;
 } ;
 
 if(has_privilege('user_warnings')){;?>
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('a988be69'))?></h3><div></div></div></div>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_warning">
 <div style="text-align:left;width:230px;">
 <?=htmlspecialchars(lang::_LS_get_define('424af5b4'))?> <input type="text" onfocus="this.select();" name="<?=$ILPHP->IMODULE_POST_VAR('points');?>" style="text-align:right;width:50px;" value="10"><br>
 <?=htmlspecialchars(lang::_LS_get_define('0aac800f'))?> <input type="text" onfocus="this.select();" name="<?=$ILPHP->IMODULE_POST_VAR('days');?>" style="text-align:right;width:50px;" value="30"> <?=htmlspecialchars(lang::_LS_get_define('88e916e1'))?><br>
 </div>
 <div><textarea class="bbcodeedit" name="<?=$ILPHP->IMODULE_POST_VAR('reason');?>" style="height:80px;"></textarea></div>
 <button type="submit" class="big-button lonely-button"><?=htmlspecialchars(lang::_LS_get_define('bf712da5'))?></button>
 </form>
 <? } ;
 
 if(has_privilege('shoutboxmaster')){;?>
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('94f0e6e1'))?></h3><div></div></div></div>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');" style="text-align:left;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_denied_entrance">
 <?=htmlspecialchars(lang::_LS_get_define('825d2cb9'))?>
 <select name="<?=$ILPHP->IMODULE_POST_VAR('place');?>">
 <option value="shoutbox"><?=htmlspecialchars(lang::_LS_get_define('d66fda7c'))?></option>
 <option value="chat"><?=htmlspecialchars(lang::_LS_get_define('3ac11320'))?></option>
 </select><br>
 <?=htmlspecialchars(lang::_LS_get_define('0aac800f'))?> <input type="text" onfocus="this.select();" name="<?=$ILPHP->IMODULE_POST_VAR('days');?>" style="text-align:right;width:30px;" value="0"> <?=htmlspecialchars(lang::_LS_get_define('aae8b39f'))?>, <input type="text" onfocus="this.select();" name="<?=$ILPHP->IMODULE_POST_VAR('hours');?>" style="text-align:right;width:30px;" value="0"> <?=htmlspecialchars(lang::_LS_get_define('c5c2350b'))?>, <input type="text" onfocus="this.select();" name="<?=$ILPHP->IMODULE_POST_VAR('minutes');?>" style="text-align:right;width:30px;" value="30"> <?=htmlspecialchars(lang::_LS_get_define('047fef06'))?><br>
 <?=htmlspecialchars(lang::_LS_get_define('2b8a4fc5'))?> <input type="text" onfocus="this.select();" name="<?=$ILPHP->IMODULE_POST_VAR('reason');?>" value="" size="64"><br>
 <button type="submit" class="big-button"><?=htmlspecialchars(lang::_LS_get_define('bf712da5'))?></button>
 </form>
 <? } ;
 
 if(has_privilege('usermanager')){;?>
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('6babd2ab'))?></h3><div></div></div></div>
 <div id="user<?=htmlspecialchars($ILPHP->user['user_id']);?>" class="row"><?=iengine::GET(['admin', 'users'])->row($ILPHP->user);?></div>
 <? } ;
 
 if(has_privilege('groupmanager')){;?>
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('3bb497cf'))?></h3><div></div></div></div>
 <table border="1">
 <?foreach(array_keys($ILPHP->privileges) as $ILPHP->k){;?>
 <tr style="border:1px #aaa solid;" class="row">
 <th><?=htmlspecialchars($ILPHP->k);?></th>
 <td><?if(@user($ILPHP->user['user_id'])->privileges[$ILPHP->k]){;echo htmlspecialchars(lang::_LS_get_define('2f50cf0a'));}else{;echo htmlspecialchars(lang::_LS_get_define('c27eaf32')); } ?></td>
 </tr>
 <? } ?>
 </table>
 <? } ;
 
 if(has_privilege('forum_admin')){;?>
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('d12a6f71'))?></h3><div></div></div></div>
 <form id="admin_change_sig" method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.module-user-infos');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="change_signature">
 <div><textarea class="bbcodeedit" name="<?=$ILPHP->IMODULE_POST_VAR('signature');?>" rows="6" cols="30" style="width:100%;height:120px;"><?=htmlspecialchars($ILPHP->user['signature']);?></textarea></div>
 <button type="submit" class="big-button lonely-button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 <? } ;
 } ?>
</div>
<?}?>