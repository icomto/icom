<?function ILPHP____templates_c_58653ce4_users_php_4fcbffeb_php_row(&$ILPHP){?>
 <table width="100%" border="1" class="user-table">
 <tr>
 <td width="50" align="right">
 <?=user($ILPHP->i['user_id'])->html(-1);?>
 </td>
 <td width="155">
 <form method="post" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this,'~.user-table');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="rename">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('user_id');?>" value="<?=htmlspecialchars($ILPHP->i['user_id']);?>">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('nick');?>" style="width:100px;" value="<?=htmlspecialchars($ILPHP->i['nick']);?>">
 <button type="submit" class="button" style="width:35px;"><?=htmlspecialchars(lang::_LS_get_define('a51c4bd3'))?></button>
 </form>
 </td>
 <td>
 <?$ILPHP->while_g=0;while($ILPHP->g = $ILPHP->groups->fetch_assoc()){$ILPHP->while_g++;?>
 <a href="/<?=LANG;?>/community/users/groups/<?=$ILPHP->g['id'];?>/"><?=htmlspecialchars($ILPHP->g['name']);?></a>
 <form method="post" class="form-inline" onsubmit="return iC(this,'~.user-table');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="del_group">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('user_id');?>" value="<?=htmlspecialchars($ILPHP->i['user_id']);?>">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" style="width:100px;" value="<?=htmlspecialchars($ILPHP->g['id']);?>">
 <button type="submit" class="button" style="width:35px;">X</button>
 </form>
 <?if($ILPHP->while_g < $ILPHP->groups->num_rows){;?>, <? } ;
 } ?>
 </td>
 <td width="155" align="center">
 <form method="post" onsubmit="return iC_confirm('<?=htmlspecialchars(lang::_LS_get_define('28c26efd'))?>', this, '~.user-table');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="add_group">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('user_id');?>" value="<?=htmlspecialchars($ILPHP->i['user_id']);?>">
 <select name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" style="width:100px;">
 <option></option>
 <?while($ILPHP->g = $ILPHP->available_groups->fetch_assoc()){;?>
 <option value="<?=$ILPHP->g['id'];?>"><?=htmlspecialchars($ILPHP->g['name']);?></option>
 <? } ?>
 </select>
 <button type="submit" class="button" style="width:35px;"><?=htmlspecialchars(lang::_LS_get_define('9efb8185'))?></button>
 </form>
 </td>
 <td width="60">
 <?=htmlspecialchars(lang::_LS_get_define('5533445e'))?>
 </td>
 </tr>
 </table>
 <?}?><?function ILPHP____templates_c_58653ce4_users_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1><a href="/<?=LANG;?>/admin/users/<?=$ILPHP->page;?>/">Benutermanager</a></h1>
 <div style="height:auto;display:block;text-align:center;" class="module-content">
 <div class="pages"><?=$ILPHP->im_pages_html();?></div>
 <table width="100%">
 <?while($ILPHP->i = $ILPHP->users->fetch_assoc()){;?>
 <tr class="row">
 <td>
 <?=$ILPHP->row($ILPHP->i);?>
 
 </td>
 </tr>
 <? } ?>
 </table>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>