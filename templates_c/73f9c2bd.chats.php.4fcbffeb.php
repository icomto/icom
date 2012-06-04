<?function ILPHP____templates_c_73f9c2bd_chats_php_4fcbffeb_php(&$ILPHP){?><div class="module-item">
 <h1><?=$ILPHP->im_way_html();?></h1>
 <div class="module-content">
 <?if(!$ILPHP->category_id and IS_LOGGED_IN){;?>
 <p>
 <?=htmlspecialchars(lang::_LS_get_define('99fe1c30'))?>
 <form method="post" action="/<?=LANG;?>/chats/" onsubmit="return iC(this, '~.module-item');">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="new">
 <?=htmlspecialchars(lang::_LS_get_define('f4c14433'))?> <input type="text" size="80" name="<?=$ILPHP->IMODULE_POST_VAR('name');?>"> <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('bf712da5'))?></button>
 </form>
 </p>
 <? } ?>
 <p>
 <?if($ILPHP->error){;?>
 <div class="error">
 <?switch($ILPHP->error){;
 default:;echo htmlspecialchars(lang::_handle_template_string(lang::_LS_get_define('be1030f4'),$ILPHP->error));break;
 case 'CATEGORY_NOT_FOUND':;echo htmlspecialchars(lang::_LS_get_define('014e2594'));break;
 case 'SUB_CATEGORY_NOT_FOUND':;echo htmlspecialchars(lang::_LS_get_define('1da2f5e5'));break;
 case 'NO_POSSIBLE_CATEGORY':;echo htmlspecialchars(lang::_LS_get_define('0bf725aa'));break;
 } ?>
 </div>
 <?}elseif($ILPHP->chats->num_rows == 0){;?>
 <p class="info">
 <?=htmlspecialchars(lang::_LS_get_define('e04d6948'))?>
 </p>
 <?}else{;?>
 <div class="forum">
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->chats->fetch_assoc()){$ILPHP->while_i++;;
 if($ILPHP->while_i == 1 or $ILPHP->current_category != $ILPHP->i['category_id']){;
 $ILPHP->current_category = $ILPHP->i['category_id'];
 if($ILPHP->while_i > 1){;?>
 </table>
 <? } ;
 if(!$ILPHP->category_id){;?>
 <div class="root-section-header">
 <a href="/<?=LANG;?>/chats/<?=$ILPHP->i['category_id'];?>-<?=urlenc($ILPHP->i['category_name']);?>/"><?=htmlspecialchars($ILPHP->i['category_name']);?></a>
 </div>
 <? } ?>
 <table class="forum-sections" border="1">
 <tr class="head">
 <?if($ILPHP->i['has_sub_categorys'] and !$ILPHP->sub_category_id){;?><th width="70"><?=htmlspecialchars(lang::_LS_get_define('0a539882'))?></th><? } ?>
 <th><?=htmlspecialchars(lang::_LS_get_define('0cfdc80e'))?></th>
 <th width="100" style="text-align:center;"><?=htmlspecialchars(lang::_LS_get_define('f36cf2e0'))?></th>
 <th width="80" style="text-align:center;"><?=htmlspecialchars(lang::_LS_get_define('a30f74c6'))?></th>
 <th width="100" style="text-align:center;"><?=htmlspecialchars(lang::_LS_get_define('98f494e2'))?></th>
 </tr>
 <? } ?>
 <tr class="forum-row">
 <?if($ILPHP->i['has_sub_categorys'] and !$ILPHP->sub_category_id){;?>
 <td>
 <?if($ILPHP->i['sub_category_id']){;?><a href="/<?=LANG;?>/chats/<?=$ILPHP->i['category_id'];?>-<?=urlenc($ILPHP->i['category_name']);?>/sub/<?=$ILPHP->i['sub_category_id'];?>-<?=urlenc($ILPHP->i['sub_category_name']);?>/"><?=htmlspecialchars($ILPHP->i['sub_category_name']);?></a><?}else{;?>-<? } ?>
 </td>
 <? } ?>
 <td>
 <a href="/<?=LANG;?>/chat/<?=urlencode($ILPHP->i['id']);?>-<?=urlenc($ILPHP->i['name']);?>/"><?=get_sitelang_flag($ILPHP->i['lang']);?> <?=htmlspecialchars($ILPHP->i['name']);?></a><?if($ILPHP->i['status'] == 'closed'){;?> (<?=htmlspecialchars(lang::_LS_get_define('dd5ffbf5'))?>)<?}elseif($ILPHP->i['status'] == 'deleted'){;?> (<?=htmlspecialchars(lang::_LS_get_define('77a1b0e4'))?>)<? } ?>
 </td>
 <td style="text-align:center;"><?if($ILPHP->i['online_users']){;echo htmlspecialchars(number_format($ILPHP->i['online_users'],''?'':0,',','.'));}else{;?>-<? } ?> / <?if($ILPHP->i['online_guests']){;echo htmlspecialchars(number_format($ILPHP->i['online_guests'],''?'':0,',','.'));}else{;?>-<? } ?></td>
 <td style="text-align:center;"><?if($ILPHP->i['num_messages']){;echo htmlspecialchars(number_format($ILPHP->i['num_messages'],''?'':0,',','.'));}else{;?>-<? } ?></td>
 <td style="text-align:center;"><?if($ILPHP->i['lastmessage']){;echo timeago($ILPHP->i['lastmessage']);}else{;?>-<? } ?></td>
 </tr>
 <? } ?>
 </table>
 </div>
 <? } ?>
 </p>
 </div>
 <div class="module-footer"></div>
</div>
<?}?>