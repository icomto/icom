<?function ILPHP____templates_c_58653ce4_radio_php_4fcbffeb_php_channel(&$ILPHP){?>
 <?if(!$ILPHP->i){;?><p class="error"><?=htmlspecialchars(lang::_LS_get_define('ce326d81'))?></p>
 <?}else{;
 echo $ILPHP->prepare_channel();?>
 <div class="admin-radio-channel">
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('e681aa41'),$ILPHP->i['channel']))?></h3><div></div></div></div>
 <table>
 <tr>
 <td width="180"><?=htmlspecialchars(lang::_LS_get_define('a8e1cfe5'))?></td>
 <td><?if($ILPHP->i['is_admin']){;echo htmlspecialchars(lang::_LS_get_define('7bf4bda0'));}else{;echo htmlspecialchars(lang::_LS_get_define('2832ec02')); } ?></td>
 </tr>
 <tr><td colspan="2">&nbsp;</td></tr>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('f778717c'))?></td>
 <td>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.admin-radio-channel');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="server">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('channel');?>" value="<?=htmlspecialchars($ILPHP->i['channel']);?>">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('host');?>" value="<?=$ILPHP->i['host'];?>" size="20" data-tooltip="Hostname / IP">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('port');?>" value="<?=$ILPHP->i['port'];?>" size="5" data-tooltip="Port">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 </td>
 </tr>
 <tr><td colspan="2">&nbsp;</td></tr>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('2c26a208'))?></td>
 <td>
 <?if($ILPHP->i['admins']){;
 $ILPHP->num_admins = count($ILPHP->i['admins']);
 $ILPHP->foreach_admin=0;foreach($ILPHP->i['admins'] as $ILPHP->admin){$ILPHP->foreach_admin++;;
 if(has_privilege('radio_admin')){;?>
 <form style="display:inline;" method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.admin-radio-channel');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_admin">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('channel');?>" value="<?=htmlspecialchars($ILPHP->i['channel']);?>">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('admin_id');?>" value="<?=$ILPHP->admin;?>">
 <button type="submit" class="button"><?=htmlspecialchars(user($ILPHP->admin)->nick);?></button>
 </form>
 <?}else{;
 echo user($ILPHP->admin)->html(-1);if($ILPHP->foreach_admin < $ILPHP->num_admins){;?>, <? } ;
 } ;
 } ;
 }else{;
 echo htmlspecialchars(lang::_LS_get_define('42fe71da'));
 } ?>
 </td>
 </tr>
 <?if(has_privilege('radio_admin')){;?>
 <tr><td colspan="2">&nbsp;</td></tr>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('6f26100a'))?></td>
 <td>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~.admin-radio-channel');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_admin">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('channel');?>" value="<?=htmlspecialchars($ILPHP->i['channel']);?>">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('admin');?>" size="40">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5912af1b'))?></button>
 </form>
 </td>
 </tr>
 <? } ?>
 <tr><td colspan="2">&nbsp;</td></tr>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('ccb091ed'))?></td>
 <td>
 <?if($ILPHP->i['guests']){;
 $ILPHP->num_guests = count($ILPHP->i['guests']);
 $ILPHP->foreach_guest=0;foreach($ILPHP->i['guests'] as $ILPHP->guest){$ILPHP->foreach_guest++;;
 if($ILPHP->i['is_admin']){;?>
 <form style="display:inline;" method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.admin-radio-channel');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="remove_guest">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('channel');?>" value="<?=htmlspecialchars($ILPHP->i['channel']);?>">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('guest_id');?>" value="<?=$ILPHP->guest;?>">
 <button type="submit" class="button"><?=htmlspecialchars(user($ILPHP->guest)->nick);?></button>
 </form>
 <?}else{;
 echo user($ILPHP->guest)->html(-1);if($ILPHP->foreach_guest < $ILPHP->num_guests){;?>, <? } ;
 } ;
 } ;
 }else{;
 echo htmlspecialchars(lang::_LS_get_define('42fe71da'));
 } ?>
 </td>
 </tr>
 <?if($ILPHP->i['is_admin']){;?>
 <tr><td colspan="2">&nbsp;</td></tr>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('6f277bb7'))?></td>
 <td>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~.admin-radio-channel');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_guest">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('channel');?>" value="<?=htmlspecialchars($ILPHP->i['channel']);?>">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('guest');?>" size="40">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5912af1b'))?></button>
 </form>
 </td>
 </tr>
 <? } ?>
 <tr><td colspan="2">&nbsp;</td></tr>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('cf0d9f5a'))?></td>
 <td>
 <form style="display:inline;" method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~.admin-radio-channel');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="dj">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('channel');?>" value="<?=htmlspecialchars($ILPHP->i['channel']);?>">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('dj');?>" value="auto">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('7cd3aaed'))?></button>
 </form>
 <?if($ILPHP->i['djs']){;
 foreach($ILPHP->i['djs'] as $ILPHP->dj){;?>
 <form style="display:inline;" method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~.admin-radio-channel');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="dj">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('channel');?>" value="<?=htmlspecialchars($ILPHP->i['channel']);?>">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('dj');?>" value="<?=$ILPHP->dj;?>">
 <button type="submit" class="button"><?=htmlspecialchars(user($ILPHP->dj)->nick);?></button>
 </form>
 <? } ;
 } ?>
 </td>
 </tr>
 <tr><td colspan="2">&nbsp;</td></tr>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('e6bed212'))?></td>
 <td>
 <?if($ILPHP->i['current_dj']){;echo user($ILPHP->i['current_dj'])->html();
 }else{;echo htmlspecialchars(lang::_LS_get_define('1797bbe6'));
 } ?>
 </td>
 </tr>
 <tr><td colspan="2">&nbsp;</td></tr>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('216c0fed'))?></td>
 <td>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC(this, '~.admin-radio-channel');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="infos">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('channel');?>" value="<?=htmlspecialchars($ILPHP->i['channel']);?>">
 <div><textarea name="<?=$ILPHP->IMODULE_POST_VAR('infos');?>" style="width:100%;" rows="5"><?=htmlspecialchars($ILPHP->i['infos']);?></textarea></div>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 </td>
 </tr>
 <tr><td colspan="2">&nbsp;</td></tr>
 <?if($ILPHP->i['is_admin']){;?>
 <tr>
 <td><?=htmlspecialchars(lang::_LS_get_define('bfbc66c6'))?></td>
 <td>
 <form method="post" action="<?=htmlspecialchars($ILPHP->url);?>" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.admin-radio-channel');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="chat">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('channel');?>" value="<?=htmlspecialchars($ILPHP->i['channel']);?>">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('chat');?>" size="40" value="<?=htmlspecialchars($ILPHP->i['chat_id'] ? $ILPHP->i['chat_id'] : '');?>">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 </td>
 </tr>
 <? } ?>
 </table>
 </div>
 <? } ?>
 <?}?><?function ILPHP____templates_c_58653ce4_radio_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1><?=$ILPHP->im_way_html();?></h1>
 <div class="module-content module-admin-radio">
 <?if(!$ILPHP->channels->num_rows){;?><p class="error"><?=htmlspecialchars(lang::_LS_get_define('71486bd4'))?></p>
 <?}else{;
 $ILPHP->while_i=0;while($ILPHP->i = $ILPHP->channels->fetch_assoc()){$ILPHP->while_i++;?>
 
 <?ILPHP____templates_c_58653ce4_radio_php_4fcbffeb_php_channel($ILPHP)?>
 <? } ;
 } ?>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>