<?function ILPHP____templates_c_16853d12_community_php_polls_4fcbffeb_php(&$ILPHP){?><style>
.community-groups table {
 margin:0 auto;
 width:50%;
}
.community-groups table .num-members {
 text-align:right;
 width:30%;
}
</style>
<div class="community-polls">
 <?if(IS_LOGGED_IN){;?>
 <p>
 <?=htmlspecialchars(lang::_LS_get_define('d2f60563'))?>
 <form method="post" action="/<?=LANG;?>/community/polls/">
 <input type="hidden" name="<?=$ILPHP->IMODULE_POST_VAR('action');?>" value="new">
 <?=htmlspecialchars(lang::_LS_get_define('feaccc60'))?> <input type="text" size="80" name="<?=$ILPHP->IMODULE_POST_VAR('question');?>" data-tooltip="<?=htmlspecialchars(lang::_LS_get_define('70ac99c4'))?>"> <button type="submit" class="button"><?=htmlspecialchars(lang::_LS_get_define('bf712da5'))?></button>
 </form>
 </p>
 <? } ?>
 <p>
 <?if($ILPHP->polls->num_rows == 0){;?>
 <p class="info">
 <?=htmlspecialchars(lang::_LS_get_define('5932c19e'))?>
 </p>
 <?}else{;?>
 <div class="forum">
 <?if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ?>
 <table class="forum-sections" border="1">
 <tr class="head">
 <th><?=htmlspecialchars(lang::_LS_get_define('0cfdc80e'))?></th>
 <th width="120" style="text-align:center;"><?=htmlspecialchars(lang::_LS_get_define('048d68f9'))?></th>
 </tr>
 <?$ILPHP->while_i=0;while($ILPHP->i = $ILPHP->polls->fetch_assoc()){$ILPHP->while_i++;?>
 <tr class="forum-row">
 <td>
 <a href="/<?=LANG;?>/poll/<?=$ILPHP->i['id'];?>-<?=urlenc($ILPHP->i['question']);?>/"><?=htmlspecialchars($ILPHP->i['question']);?></a><?if($ILPHP->i['status'] == 'closed'){;?> (<?=htmlspecialchars(lang::_LS_get_define('dd5ffbf5'))?>)<?}elseif($ILPHP->i['status'] == 'deleted'){;?> (<?=htmlspecialchars(lang::_LS_get_define('77a1b0e4'))?>)<? } ?>
 </td>
 <td style="text-align:center;"><?=user($ILPHP->i['creator'])->html();?></td>
 </tr>
 <? } ?>
 </table>
 <?if($ILPHP->num_pages > 1){;?><div class="pages"><?=$ILPHP->im_pages_html();?></div><? } ?>
 </div>
 <? } ?>
 </p>
</div>
<?}?>