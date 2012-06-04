<?function ILPHP____templates_c_58653ce4_forum_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1>
 <?$ILPHP->foreach_i=0;foreach($ILPHP->way as $ILPHP->i){$ILPHP->foreach_i++;;
 if($ILPHP->i['1']){;?><a href="/<?=LANG;echo htmlspecialchars($ILPHP->i['1']);?>"><?=htmlspecialchars($ILPHP->i['0']);?></a><?}else{;echo htmlspecialchars($ILPHP->i['0']); } ;if($ILPHP->foreach_i < count($ILPHP->way)){;?> &raquo; <? } ;
 } ?>
 </h1>
 <div style="height:auto;display:block;text-align:center;" class="module-content">
 <br>
 <form method="post" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="new">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('section_id');?>" value="<?=$ILPHP->parent;?>">
 <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('772f222c'))?>">
 </form>
 <br>
 
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->sections->fetch_assoc()){$ILPHP->while_i++;;
 $ILPHP->positions[] = array($ILPHP->while_i, $ILPHP->i['section_id']);?>
 <table width="100%" class="row">
 <tr>
 <td>
 <div id="forumpos<?=$ILPHP->while_i;?>">
 <?=$ILPHP->row($ILPHP->i);?>
 </div>
 </td>
 <td align="center" width="40">
 <?if($ILPHP->while_i > 1){;?><input type="button" class="button" style="width:29px;" value="UU" onclick="forumswap(<?=$ILPHP->while_i;?>,<?=$ILPHP->while_i-1;?>);"><? } ?>
 <br>
 <br>
 <br>
 <br>
 <?if($ILPHP->while_i < $ILPHP->sections->num_rows){;?><input type="button" class="button" style="width:29px;" value="DD" onclick="forumswap(<?=$ILPHP->while_i;?>,<?=$ILPHP->while_i+1;?>);">
 <?}else{;?><br>
 <? } ?>
 </td>
 </tr>
 </table>
 <hr>
 <? } ?>
 
 <br>
 <form method="post" onsubmit="return iC(this);">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="positions">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('section_id');?>" value="<?=$ILPHP->parent;?>">
 <?foreach($ILPHP->positions as $ILPHP->p){;?>
 <input type="hidden" id="forumposid<?=$ILPHP->p['0'];?>" name="<?=$ILPHP->IMODULE_POST_VAR('positions', $ILPHP->p['0']);?>" value="<?=htmlspecialchars($ILPHP->p['1']);?>">
 <? } ?>
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('num_positions');?>" value="<?=$ILPHP->while_i;?>">
 <input type="submit" class="button" value="<?=htmlspecialchars(lang::_LS_get_define('4b5c0aad'))?>">
 </form>
 <br>
 
 <script>
 var forumswap=function(s,t){
 var i=$('#forumposid'+s)[0].value;
 $('#forumposid'+s)[0].value=$('#forumposid'+t)[0].value;
 $('#forumposid'+t)[0].value=i;
 var m=$('#forumpos'+s).html();
 $('#forumpos'+s).html($('#forumpos'+t).html());
 $('#forumpos'+t).html(m);
 }
 </script>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>