<?function ILPHP____templates_c_58653ce4_invites_php_send_4fcbffeb_php(&$ILPHP){?><?if($ILPHP->sent){;?>
<p class="info"><?=htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('56996139'),$ILPHP->sent))?></p>
<?}else{;?>
<form method="post" action="<?=$ILPHP->url;?>" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="send">
 E-Mail Addresse: <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('email');?>" size="35"><br>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('ed291aa7'))?></button>
</form>
<? } ?>
<?}?>