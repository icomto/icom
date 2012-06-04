<?function ILPHP____templates_c_73f9c2bd_chat_php_head_data_stats_4fcbffea_php(&$ILPHP){?><div>
 <?if($ILPHP->num_admins == 1){;echo htmlspecialchars(lang::_LS_get_define('6bb16702'))?> <?}else{;echo htmlspecialchars(lang::_LS_get_define('2c26a208'))?> <? } ;
 if($ILPHP->num_admins == 0){;echo htmlspecialchars(lang::_LS_get_define('42fe71da'));
 }else{;$ILPHP->foreach_i=0;foreach($ILPHP->stats_admins as $ILPHP->i){$ILPHP->foreach_i++;;echo user($ILPHP->i)->html(-1);if($ILPHP->foreach_i < $ILPHP->num_admins){;?>, <? } ; } ;
 } ?>
</div>
<?if($ILPHP->stats_admin_groups->num_rows){;?>
<div>
 <?=htmlspecialchars(lang::_LS_get_define('150413a7'))?> 
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->stats_admin_groups->fetch_assoc()){$ILPHP->while_i++;;if($ILPHP->i['id']){;?><a href="/<?=LANG;?>/community/users/groups/<?=$ILPHP->i['id'];?>-<?=urlenc($ILPHP->i['name']);?>/"><?=htmlspecialchars($ILPHP->i['name']);?></a><?}else{;echo htmlspecialchars($ILPHP->i['name']); } ;if($ILPHP->while_i < $ILPHP->stats_admin_groups->num_rows){;?>, <? } ; } ?>
</div>
<? } ;
if($ILPHP->stats_allowed_groups->num_rows){;?>
<div>
 <span style="cursor:pointer;" onclick="vUserChat<?=$ILPHP->data['id'];?>AG=!vUserChat<?=$ILPHP->data['id'];?>AG;$(this).next().toggle().next().toggle();"><?=htmlspecialchars(lang::_LS_get_define('44138883'))?> </span>
 <span style="cursor:pointer;" onclick="vUserChat<?=$ILPHP->data['id'];?>AG=!vUserChat<?=$ILPHP->data['id'];?>AG;$(this).toggle().next().toggle();"><?=htmlspecialchars(number_format($ILPHP->stats_allowed_groups->num_rows,''?'':0,',','.'));?></span>
 <span id="vUserChat<?=$ILPHP->data['id'];?>AG" style="display:none;">
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->stats_allowed_groups->fetch_assoc()){$ILPHP->while_i++;;if($ILPHP->i['id']){;?><a href="/<?=LANG;?>/community/users/groups/<?=$ILPHP->i['id'];?>-<?=urlenc($ILPHP->i['name']);?>/"><?=htmlspecialchars($ILPHP->i['name']);?></a><?}else{;echo htmlspecialchars($ILPHP->i['name']); } ;if($ILPHP->while_i < $ILPHP->stats_allowed_groups->num_rows){;?>, <? } ; } ?>
 </span>
 <script>if(vUserChat<?=$ILPHP->data['id'];?>AG===undefined)var vUserChat<?=$ILPHP->data['id'];?>AG=false;$(function(){if(vUserChat<?=$ILPHP->data['id'];?>AG==true)$('#vUserChat<?=$ILPHP->data['id'];?>AG').show().prev().hide();});</script>
</div>
<? } ;
if($ILPHP->num_allowed_users){;?>
<div>
 <span style="cursor:pointer;" onclick="vUserChat<?=$ILPHP->data['id'];?>AU=!vUserChat<?=$ILPHP->data['id'];?>AU;$(this).next().toggle().next().toggle();"><?=htmlspecialchars(lang::_LS_get_define('073771fa'))?> </span>
 <span style="cursor:pointer;" onclick="vUserChat<?=$ILPHP->data['id'];?>AU=!vUserChat<?=$ILPHP->data['id'];?>AU;$(this).toggle().next().toggle();"><?=htmlspecialchars(number_format($ILPHP->num_allowed_users,''?'':0,',','.'));?></span>
 <span id="vUserChat<?=$ILPHP->data['id'];?>AU" style="display:none;">
 <?$ILPHP->foreach_i=0;foreach($ILPHP->stats_allowed_users as $ILPHP->i){$ILPHP->foreach_i++;;echo user($ILPHP->i)->html(-1);if($ILPHP->foreach_i < $ILPHP->num_allowed_users){;?>, <? } ; } ?>
 </span>
 <script>if(vUserChat<?=$ILPHP->data['id'];?>AU===undefined)var vUserChat<?=$ILPHP->data['id'];?>AU=false;$(function(){if(vUserChat<?=$ILPHP->data['id'];?>AU==true)$('#vUserChat<?=$ILPHP->data['id'];?>AU').show().prev().hide();});</script>
</div>
<? } ;
if($ILPHP->data['points_from'] and $ILPHP->data['points_to']){;?>
<div><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('3f1fb687'),number_format($ILPHP->data['points_from'],''?'':0,',','.'),number_format($ILPHP->data['points_to'],''?'':0,',','.')))?></div>
<?}elseif($ILPHP->data['points_from']){;?>
<div><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('b5283e76'),number_format($ILPHP->data['points_from'],''?'':0,',','.')))?></div>
<?}elseif($ILPHP->data['points_to']){;?>
<div><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('4a6fef3f'),number_format($ILPHP->data['points_to'],''?'':0,',','.')))?></div>
<? } ;
if($ILPHP->num_banned_users){;?>
<div>
 <span style="cursor:pointer;" onclick="vUserChat<?=$ILPHP->data['id'];?>BU=!vUserChat<?=$ILPHP->data['id'];?>BU;$(this).next().toggle().next().toggle();"><?=htmlspecialchars(lang::_LS_get_define('ca1ec958'))?> </span>
 <span style="cursor:pointer;" onclick="vUserChat<?=$ILPHP->data['id'];?>BU=!vUserChat<?=$ILPHP->data['id'];?>BU;$(this).toggle().next().toggle();"><?=htmlspecialchars(number_format($ILPHP->num_banned_users,''?'':0,',','.'));?></span>
 <span id="vUserChat<?=$ILPHP->data['id'];?>BU" style="display:none;">
 <?$ILPHP->foreach_i=0;foreach($ILPHP->stats_banned_users as $ILPHP->i){$ILPHP->foreach_i++;;echo user($ILPHP->i)->html(-1);if($ILPHP->foreach_i < $ILPHP->num_banned_users){;?>, <? } ; } ?>
 </span>
 <script>if(vUserChat<?=$ILPHP->data['id'];?>BU===undefined)var vUserChat<?=$ILPHP->data['id'];?>BU=false;$(function(){if(vUserChat<?=$ILPHP->data['id'];?>BU==true)$('#vUserChat<?=$ILPHP->data['id'];?>BU').show().prev().hide();});</script>
</div>
<? } ;
if(in_array(0, $ILPHP->stats_allowed_groups_array)){;?>
<div>
 <?=htmlspecialchars(lang::_LS_get_define('91a917b1'))?> <?if($ILPHP->stats_guests == 0){;echo htmlspecialchars(lang::_LS_get_define('42fe71da'));}else{;echo htmlspecialchars(number_format($ILPHP->stats_guests,''?'':0,',','.')); } ?>
</div>
<? } ?>
<div>
 <?if($ILPHP->stats_users->num_rows == 0){;
 echo htmlspecialchars(lang::_LS_get_define('6616b92f'))?> <?=htmlspecialchars(lang::_LS_get_define('42fe71da'));
 }else{;?>
 <span style="cursor:pointer;" onclick="vUserChat<?=$ILPHP->data['id'];?>UO=!vUserChat<?=$ILPHP->data['id'];?>UO;$(this).next().toggle().next().toggle();"><?=htmlspecialchars(lang::_LS_get_define('6616b92f'))?> </span>
 <span style="cursor:pointer;" onclick="vUserChat<?=$ILPHP->data['id'];?>UO=!vUserChat<?=$ILPHP->data['id'];?>UO;$(this).toggle().next().toggle();"><?=htmlspecialchars(number_format($ILPHP->stats_users->num_rows,''?'':0,',','.'));?></span>
 <span id="vUserChat<?=$ILPHP->data['id'];?>UO" style="display:none;">
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->stats_users->fetch_assoc()){$ILPHP->while_i++;;echo user($ILPHP->i['user_id'])->html(-1);if($ILPHP->while_i < $ILPHP->stats_users->num_rows){;?>, <? } ; } ?>
 </span>
 <script>if(vUserChat<?=$ILPHP->data['id'];?>UO===undefined)var vUserChat<?=$ILPHP->data['id'];?>UO=false;if(vUserChat<?=$ILPHP->data['id'];?>UO==true)$('#vUserChat<?=$ILPHP->data['id'];?>UO').show().prev().hide();</script>
 <? } ?>
</div>
<?}?>