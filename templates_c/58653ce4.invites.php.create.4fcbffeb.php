<?function ILPHP____templates_c_58653ce4_invites_php_create_4fcbffeb_php(&$ILPHP){?><?if($ILPHP->create){;?>
<input type="text" style="text-align:center;width:100px;" onfocus="this.select();" value="<?=htmlspecialchars($ILPHP->create);?>">
<?}else{;?>
<form method="post" action="<?=$ILPHP->url;?>" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="create">
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('1ad99c80'))?></button>
</form>
<? } ?>
<?}?>