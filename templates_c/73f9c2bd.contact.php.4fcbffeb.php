<?function ILPHP____templates_c_73f9c2bd_contact_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1><?=$ILPHP->im_way_html();?></h1>
 <div class="module-content module-contact">
 <?if(IS_LOGGED_IN){;
 echo htmlspecialchars(lang::_LS_get_define('cfdbcc3a'))?>
 <ul>
 <li><?=lang::_handle_template_string(lang::_LS_get_define('21261793'),htmlspecialchars(LANG))?></li>
 </ul>
 <?}else{;
 if($ILPHP->invite_request){;
 if($ILPHP->invite_request['status'] == 'requested'){;?>
 <p class="info"><?=htmlspecialchars(lang::_LS_get_define('2f74adbd'))?></p>
 <?}elseif($ILPHP->invite_request['status'] == 'rejected'){;?>
 <p class="error"><?=htmlspecialchars(lang::_LS_get_define('d035116c'))?></p>
 <?}else{;?>
 <p class="info"><?=htmlspecialchars(lang::_LS_get_define('26adcb52'))?></p>
 <p>Dein Invite-Code: <input class="invite-code" type="text" value="<?=htmlspecialchars($ILPHP->invite_request['code']);?>" onfocus="this.select();"></p>
 <p><a href="/<?=LANG;?>/register/"><?=htmlspecialchars(lang::_LS_get_define('06cbf28e'))?></a></p>
 <? } ;
 }elseif($ILPHP->message){;?>
 <p><?=$ILPHP->message;?><p>
 <?}else{;
 if($ILPHP->error){;?>
 <p class="error"><?=$ILPHP->error;?></p>
 <? } ?>
 <p class="important">
 <?=htmlspecialchars(lang::_LS_get_define('a55d10f3'))?>
 </p>
 <?if(REGISTER_CLOSED){;?>
 <p class="important">
 <?=lang::_LS_get_define('46075294')?>
 </p>
 <?}elseif(REGISTER_NEED_INVITE_CODE){;?>
 <p>
 <u class="important">Um einen Invite-Code zu beantragen Beachte folgendes:</u><br>
 - <?=htmlspecialchars(lang::_LS_get_define('b7524259'))?><br>
 - <?=htmlspecialchars(lang::_LS_get_define('9844a6c7'))?>
 </p>
 <? } ?>
 <p>
 <a href="http://icom.to/de/report_page" target="_blank">Hier klicken f&uuml;r Abuse oder DMCA-Memeldung</a>
 </p>
 <form method="post" action="/<?=LANG;?>/contact/" onsubmit="if($('#ModuleContactEmail')[0].value.length<=0)alert('<?=htmlspecialchars(lang::_LS_get_define('e9c06811'))?>');else if($('#ModuleContactMessage').attr('value').length<=0)alert('<?=htmlspecialchars(lang::_LS_get_define('f2e66049'))?>');else return iC(this, '~.module-item');return false;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="contact">
 <table>
 <tr>
 <th><?=htmlspecialchars(lang::_LS_get_define('12373380'))?></th>
 <td><input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('email');?>" id="ModuleContactEmail"></td>
 </tr>
 <tr>
 <th><?=htmlspecialchars(lang::_LS_get_define('e6feaa4a'))?></th>
 <td>
 <select name="<?=$ILPHP->IMODULE_POST_VAR('reason');?>">
 <option value="admin"<?if($ILPHP->reason == 'admin'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('85178658'))?></option>
 <?if(!REGISTER_CLOSED and REGISTER_NEED_INVITE_CODE){;?>
 <option value="invite"<?if($ILPHP->reason == 'invite'){;?> selected="selected"<? } ?>><?=htmlspecialchars(lang::_LS_get_define('2a6d77d5'))?></option>
 <? } ?>
 </select>
 </td>
 </tr>
 </table>
 <textarea id="ModuleContactMessage" name="<?=$ILPHP->IMODULE_POST_VAR('message');?>"></textarea>
 <button type="submit" class="button lonely-button"><?=htmlspecialchars(lang::_LS_get_define('e864f7e0'))?></button>
 </form>
 <div class="SEP"><div><div></div><h3><?=htmlspecialchars(lang::_LS_get_define('2b4e8492'))?></h3><div></div></div></div>
 <form method="post" action="/<?=LANG;?>/contact/" onsubmit="if(String($('#invite_status_email').attr('value')).length<=0)alert('<?=htmlspecialchars(lang::_LS_get_define('90e19bf8'))?>');else return iC(this, '~.module-item');return false;">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="invite">
 <?=htmlspecialchars(lang::_LS_get_define('12373380'))?>
 <input type="text" id="<?=$ILPHP->IMODULE_POST_VAR('invite_status_email');?>" name="<?=$ILPHP->IMODULE_POST_VAR('email');?>">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('61923af1'))?></button>
 </form>
 <? } ;
 } ?>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>