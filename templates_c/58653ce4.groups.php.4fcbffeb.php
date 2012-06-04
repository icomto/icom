<?function ILPHP____templates_c_58653ce4_groups_php_4fcbffeb_php_row(&$ILPHP){?>
 <form method="post" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="save">
 <table>
 <tr style="border:1px #aaa solid;" class="row"><th><?=htmlspecialchars(lang::_LS_get_define('ccaf0665'))?></th><td align="right"><input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('id');?>" value="<?=$ILPHP->i['id'];?>"><a href="/<?=LANG;?>/community/users/groups/<?=$ILPHP->i['id'];?>/"><?=htmlspecialchars($ILPHP->i['id']);?></a></td></tr>
 <tr style="border:1px #aaa solid;" class="row"><th style="cursor:pointer;" onclick="$(this).parent().parent().children('.v').toggle();"><?=htmlspecialchars(lang::_LS_get_define('b8663b8b'))?></th><td><input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name_de');?>" value="<?=htmlspecialchars($ILPHP->i['name_de']);?>"></td></tr>
 <tr style="border:1px #aaa solid;" class="row"><th style="cursor:pointer;" onclick="$(this).parent().parent().children('.v').toggle();"><?=htmlspecialchars(lang::_LS_get_define('173f93d3'))?></th><td><input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name_en');?>" value="<?=htmlspecialchars($ILPHP->i['name_en']);?>"></td></tr>
 <tr style="border:1px #aaa solid;<?if($ILPHP->display != $ILPHP->i['id']){;?>display:none;<? } ?>" class="row v"><th><?=htmlspecialchars(lang::_LS_get_define('fb69bfa9'))?></th><td align="right"><input type="checkbox" name="<?=$ILPHP->IMODULE_POST_VAR('public');?>"<?if($ILPHP->i['public']){;?> checked="checked"<? } ?>></td></tr>
 <?foreach($ILPHP->DEFAULT_PRIVILEGES as $ILPHP->k=>$ILPHP->v){;?>
 <tr style="border:1px #aaa solid;<?if($ILPHP->display != $ILPHP->i['id']){;?>display:none;<? } ?>" class="row v"><th><?=htmlspecialchars($ILPHP->k);?></th><td align="right"><input type="checkbox" name="<?=$ILPHP->IMODULE_POST_VAR('priv', $ILPHP->k);?>"<?if($ILPHP->i[$ILPHP->k]){;?> checked="checked"<? } ?>></td></tr>
 <? } ?>
 <tr style="border:1px #aaa solid;<?if($ILPHP->display != $ILPHP->i['id']){;?>display:none;<? } ?>" class="row v"><th><?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?></th><td align="right"><input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?>"></td></tr>
 <tr style="border:1px #aaa solid;<?if($ILPHP->display != $ILPHP->i['id']){;?>display:none;<? } ?>" class="row v"><th><?=htmlspecialchars(lang::_LS_get_define('5533445e'))?></th><td align="right"><input type="button" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('5533445e'))?>" onclick="if(confirm('Sicher?'))iC(this.form, '<?=$ILPHP->IMODULE_POST_VAR('action');?>=delete&<?=$ILPHP->IMODULE_POST_VAR('delete');?>=<?=$ILPHP->i['id'];?>');"></td></tr>
 </table>
 </form>
 <?}?><?function ILPHP____templates_c_58653ce4_groups_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1><a href="/<?=LANG;?>/admin/groups/"><?=htmlspecialchars(lang::_LS_get_define('a3013176'))?></a></h1>
 <div class="module-content" style="text-align:center;padding-bottom:5px;">
 <form method="post" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="new">
 <table>
 <tr style="border:1px #aaa solid;" class="row"><th><?=htmlspecialchars(lang::_LS_get_define('b8663b8b'))?></th><td><input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name_de');?>"></td></tr>
 <tr style="border:1px #aaa solid;" class="row"><th><?=htmlspecialchars(lang::_LS_get_define('173f93d3'))?></th><td><input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name_en');?>"></td></tr>
 </table>
 <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('c971521a'))?></button>
 </form>
 <table width="100%">
 <tr>
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->groups->fetch_assoc()){$ILPHP->while_i++;;
 if($ILPHP->while_i % 2 == 1){;?></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr><tr><? } ?>
 <td align="center">
 <?=$ILPHP->row($ILPHP->i);?>
 
 </td>
 <? } ?>
 </tr>
 </table>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>