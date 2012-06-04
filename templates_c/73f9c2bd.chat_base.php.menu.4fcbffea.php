<?function ILPHP____templates_c_73f9c2bd_chat_base_php_menu_4fcbffea_php(&$ILPHP){?><?if($ILPHP->allow_post){;?>
<div class="error" id="MenuChat<?=$ILPHP->html_id;?>Error"><?=htmlspecialchars($ILPHP->error);?></div>
<form class="<?=$ILPHP->imodule_name;?>_form" method="post" action="" onsubmit="return chat_base_submit(this, '<?=htmlspecialchars(str_replace("'", "\\'", $ILPHP->default_text));?>', '<?=htmlspecialchars(lang::_LS_get_define('f2e66049'))?>', 'Shouting...');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="new">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR($ILPHP->imodule_name);?>" value="<?=$ILPHP->subid;?>">
 <textarea name="<?=$ILPHP->IMODULE_POST_VAR('message');?>" rows="5" cols="25" onfocus="if(this.value=='<?=htmlspecialchars($ILPHP->default_text);?>')this.value='';" onblur="if(this.value=='')this.value='<?=htmlspecialchars($ILPHP->default_text);?>';"><?=htmlspecialchars($ILPHP->default_text);?></textarea>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('005e7873'))?></button>
</form>
<?}else{;?>
<div class="warning">
 <?if($ILPHP->reason){;echo $ILPHP->reason;
 }else{;
 echo lang::_handle_template_string(lang::_LS_get_define('81eda930'),LANG);
 } ?>
</div>
<? } ?>
<div id="Menu<?=$ILPHP->html_id;?>Content">
 <div class="menu-chat-top" id="Menu<?=$ILPHP->html_id;?>Top"></div>
 <?$ILPHP->last_id = 0;
 $ILPHP->while_i=0;while($ILPHP->i = $ILPHP->shouts->fetch_assoc()){$ILPHP->while_i++;;
 if($ILPHP->last_id < $ILPHP->i['id']){;$ILPHP->last_id = $ILPHP->i['id']; } ;
 $ILPHP->ilphp_display('chat_base.php.row.ilp', -1, "", true);;
 } ?>
</div>
<p class="all-entries">
 <a href="<?=htmlspecialchars($ILPHP->url);?>"><?=htmlspecialchars(lang::_LS_get_define('e888f213'))?></a>
</p>
<?}?>