<?function ILPHP____templates_c_58653ce4_forum_php_row_4fcbffeb_php(&$ILPHP){?><form method="post" name="<?=$ILPHP->IMODULE_POST_VAR('section_id');?>" value="<?=$ILPHP->i['section_id'];?>" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="save">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('section_id');?>" value="<?=$ILPHP->parent;?>">
 <table width="100%" border="1">
 <tr>
 <td valign="top" width="40">
 <a href="/<?=LANG;?>/admin/forum/<?=$ILPHP->i['section_id'];?>/"><?=htmlspecialchars('> '.$ILPHP->i['section_id'].' <');?></a>
 </td>
 <td valign="top" width="135">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name_de');?>" style="width:100%;" value="<?=htmlspecialchars($ILPHP->i['name_de']);?>" title="<?=htmlspecialchars(lang::_LS_get_define('adf9edf5'))?>">
 </td>
 <td valign="top" width="100%">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('description_de');?>" style="width:100%;" value="<?=htmlspecialchars($ILPHP->i['description_de']);?>" title="<?=htmlspecialchars(lang::_LS_get_define('98ab55db'))?>">
 </td>
 </tr>
 <tr>
 <td valign="top">
 &nbsp;
 </td>
 <td valign="top">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('name_en');?>" style="width:100%;" value="<?=htmlspecialchars($ILPHP->i['name_en']);?>" title="<?=htmlspecialchars(lang::_LS_get_define('024e3c47'))?>">
 </td>
 <td valign="top">
 <input type="text" name="<?=$ILPHP->IMODULE_POST_VAR('description_en');?>" style="width:100%;" value="<?=htmlspecialchars($ILPHP->i['description_en']);?>" title="<?=htmlspecialchars(lang::_LS_get_define('64f0e23d'))?>">
 </td>
 </tr>
 <tr>
 <td valign="top">
 <input type="text" style="width:100%;" name="<?=$ILPHP->IMODULE_POST_VAR('namespace');?>" value="<?=htmlspecialchars($ILPHP->i['namespace']);?>" onfocus="this.select();" title="<?=htmlspecialchars(lang::_LS_get_define('ef7259ed'))?>">
 </td>
 <td valign="top">
 <select style="width:100%;" name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" onchange="if(this.value!='')iC(this, this.form, '<?=$ILPHP->IMODULE_POST_VAR('action');?>=add_read_group');return false;">
 <option value="" style="font-style:italic;">Lesegruppen</option>
 <?if(!in_array('0', $ILPHP->i['read_groups'])){;?><option value="0">G&auml;ste</option><? } ;
 while($ILPHP->g = $ILPHP->available_read_groups->fetch_assoc()){;?>
 <option value="<?=htmlspecialchars($ILPHP->g['id']);?>"><?=htmlspecialchars($ILPHP->g['name']);?></option>
 <? } ?>
 </select>
 </td>
 <td valign="top">
 <?$ILPHP->num = count($ILPHP->i['read_groups']);
 $ILPHP->foreach_g=0;foreach($ILPHP->i['read_groups'] as $ILPHP->g){$ILPHP->foreach_g++;;
 if($ILPHP->g == ''){;continue; } ;
 if($ILPHP->g == 0){;?>G&auml;ste
 <?}else{;
 $ILPHP->grp = $ILPHP->_afr_get_group($ILPHP->g);?>
 <a href="/<?=LANG;?>/community/users/groups/<?=$ILPHP->grp['id'];?>-<?=urlenc($ILPHP->grp['name']);?>/"><?=htmlspecialchars($ILPHP->grp['name']);?></a>
 <? } ?>
 <a name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" value="<?=$ILPHP->g;?>" onclick="return iC(this, '~form', '<?=$ILPHP->IMODULE_POST_VAR('action');?>=del_read_group');">X</a>
 <?if($ILPHP->foreach_g < $ILPHP->num){;?>, <? } ;
 } ?>
 </td>
 </tr>
 <tr>
 <td>&nbsp;</td>
 <td valign="top">
 <select style="width:100%;" name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" onchange="if(this.value!='')iC(this, this.form, '<?=$ILPHP->IMODULE_POST_VAR('action');?>=add_write_group');return false;">
 <option value="" style="font-style:italic;">Schreibgruppen</option>
 <?while($ILPHP->g = $ILPHP->available_write_groups->fetch_assoc()){;?>
 <option value="<?=htmlspecialchars($ILPHP->g['id']);?>"><?=htmlspecialchars($ILPHP->g['name']);?></option>
 <? } ?>
 </select>
 </td>
 <td valign="top">
 <?$ILPHP->num = count($ILPHP->i['write_groups']);
 $ILPHP->foreach_g=0;foreach($ILPHP->i['write_groups'] as $ILPHP->g){$ILPHP->foreach_g++;;
 if($ILPHP->g == ""){;continue; } ;
 $ILPHP->grp = $ILPHP->_afr_get_group($ILPHP->g);?>
 <a href="/<?=LANG;?>/community/users/groups/<?=$ILPHP->grp['id'];?>-<?=urlenc($ILPHP->grp['name']);?>/"><?=htmlspecialchars($ILPHP->grp['name']);?></a>
 <a name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" value="<?=$ILPHP->g;?>" onclick="return iC(this, '~form', '<?=$ILPHP->IMODULE_POST_VAR('action');?>=del_write_group');">X</a>
 <?if($ILPHP->foreach_g < $ILPHP->num){;?>, <? } ;
 } ?>
 </td>
 </tr>
 <tr>
 <td>&nbsp;</td>
 <td valign="top">
 <select style="width:100%;" name="<?=$ILPHP->IMODULE_POST_VAR('user_id');?>" onchange="if(this.value!='')iC(this, this.form, '<?=$ILPHP->IMODULE_POST_VAR('action');?>=add_mod');return false;">
 <option value="" style="font-style:italic;">Moderatoren</option>
 <?foreach($ILPHP->possible_mods as $ILPHP->m){;?>
 <option value="<?=$ILPHP->m['user_id'];?>"><?=htmlspecialchars($ILPHP->m['nick']);?></option>
 <? } ?>
 </select>
 </td>
 <td valign="top">
 <?if($ILPHP->mods){;
 $ILPHP->while_m=0;while($ILPHP->m = $ILPHP->mods->fetch_assoc()){$ILPHP->while_m++;;
 echo user($ILPHP->m['user_id'])->html(-1);?>
 <a name="<?=$ILPHP->IMODULE_POST_VAR('user_id');?>" value="<?=$ILPHP->m['user_id'];?>" onclick="return iC(this, '~form', '<?=$ILPHP->IMODULE_POST_VAR('action');?>=del_mod');">X</a>
 <?if($ILPHP->while_m < $ILPHP->mods->num_rows){;?>, <? } ;
 } ;
 } ?>
 </td>
 </tr>
 <tr>
 <td>&nbsp;</td>
 <td valign="top">
 <select style="width:100%;" name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" onchange="if(this.value!='')iC(this, this.form, '<?=$ILPHP->IMODULE_POST_VAR('action');?>=add_mod_group');return false;">
 <option value="" style="font-style:italic;">Modgruppen</option>
 <?while($ILPHP->g = $ILPHP->available_mod_groups->fetch_assoc()){;?>
 <option value="<?=htmlspecialchars($ILPHP->g['id']);?>"><?=htmlspecialchars($ILPHP->g['name']);?></option>
 <? } ?>
 </select>
 </td>
 <td valign="top">
 <?$ILPHP->num = count($ILPHP->i['mod_groups']);
 $ILPHP->foreach_g=0;foreach($ILPHP->i['mod_groups'] as $ILPHP->g){$ILPHP->foreach_g++;;
 if($ILPHP->g == ''){;continue; } ;
 $ILPHP->grp = $ILPHP->_afr_get_group($ILPHP->g);?>
 <a href="/<?=LANG;?>/community/users/groups/<?=$ILPHP->grp['id'];?>-<?=urlenc($ILPHP->grp['name']);?>/"><?=htmlspecialchars($ILPHP->grp['name']);?></a>
 <a name="<?=$ILPHP->IMODULE_POST_VAR('group_id');?>" value="<?=$ILPHP->g;?>" onclick="return iC(this, '~form', '<?=$ILPHP->IMODULE_POST_VAR('action');?>=del_mod_group');">X</a>
 <?if($ILPHP->foreach_g < $ILPHP->num){;?>, <? } ;
 } ?>
 </td>
 </tr>
 <tr>
 <td valign="middle" align="center" colspan="2">
 <input type="submit" class="button" style="width:70px;" value="<?=htmlspecialchars(lang::_LS_get_define('76f4b80c'))?>">
 </td>
 <td valign="top" align="center">
 <label for="allow_threads<?=$ILPHP->i['section_id'];?>">Threads</label> <input type="checkbox" id="allow_threads<?=$ILPHP->i['section_id'];?>" name="<?=$ILPHP->IMODULE_POST_VAR('allow_threads');?>"<?if($ILPHP->i['allow_threads']){;?> checked="checked"<? } ?>> |
 <label for="allow_content<?=$ILPHP->i['section_id'];?>">Content</label> <input type="checkbox" id="allow_content<?=$ILPHP->i['section_id'];?>" name="<?=$ILPHP->IMODULE_POST_VAR('allow_content');?>"<?if($ILPHP->i['allow_content']){;?> checked="checked"<? } ?>> |
 Punkte: <input type="text" style="width:23px;text-align:right;" name="<?=$ILPHP->IMODULE_POST_VAR('points');?>" value="<?=htmlspecialchars(round($ILPHP->i['points'],1));?>"> |
 Subs: <?if($ILPHP->i['has_childs']){;?>ja<?}else{;?>nein<? } ?>
 <br>
 </td>
 </tr>
 </table>
</form>
<?}?>