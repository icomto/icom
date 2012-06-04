<?function ILPHP____templates_c_73f9c2bd_chat_base_php_row_4fcbffeb_php(&$ILPHP){?><div class="chat-row chat-row-<?=$ILPHP->i['id'];?>">
 <?=user($ILPHP->i['user_id'])->html();?> <?=timeago($ILPHP->i['timeadded']);?><br>
 <?if($ILPHP->has_mod_rights and $ILPHP->type != '_archive'){;?>
 <div class="chat-admin">
 <form method="post" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.chat-row');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="edit">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR($ILPHP->imodule_name);?>" value="<?=$ILPHP->subid;?>">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('content_id');?>" value="<?=$ILPHP->i['id'];?>">
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('message');?>"><?=htmlspecialchars($ILPHP->i['message']);?></textarea>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></button>
 </form>
 <form method="post" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.chat-row');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="delete">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR($ILPHP->imodule_name);?>" value="<?=$ILPHP->subid;?>">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('content_id');?>" value="<?=$ILPHP->i['id'];?>">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('5533445e'))?></button>
 </form>
 <button type="button" class="button" onclick="$(this).parent().hide().next().show();"><?=htmlspecialchars(lang::_LS_get_define('6c213429'))?></button>
 </div>
 <div class="user-entry" style="cursor:pointer;" title="<?=htmlspecialchars(lang::_LS_get_define('939bce9e'))?>" ondblclick="$(this).hide().prev().show();">
 <?=ubbcode::add_smileys(ubbcode::compile($ILPHP->i['message'], $ILPHP->ubb_width));?>&nbsp;
 </div>
 <?}else{;?>
 <div class="user-entry">
 <?=ubbcode::add_smileys(ubbcode::compile($ILPHP->i['message'], $ILPHP->ubb_width));?>
 </div>
 <? } ?>
</div>
<?}?>